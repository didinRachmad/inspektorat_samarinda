<?php

namespace App\Http\Controllers\Admin\Pelaksanaan;

use App\Events\TindakLanjutTemuanEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\TindakLanjutTemuan\StoreTindakLanjutTemuanRequest;
use App\Http\Requests\TindakLanjutTemuan\UpdateTindakLanjutTemuanRequest;
use App\Models\ApprovalRoute;
use App\Models\KodeRekomendasi;
use App\Models\KodeTemuan;
use App\Models\TindakLanjutTemuan;
use App\Models\Pkpt;
use App\Models\Temuan;
use App\Models\TemuanRekomendasi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TindakLanjutTemuanController extends Controller
{
    public function index()
    {
        return view('pelaksanaan.tindak_lanjut_temuan.index');
    }

    public function data()
    {
        $activeMenu = currentMenu();
        $user = Auth::user();

        // Query dengan join untuk sort & search
        $query = TindakLanjutTemuan::select(
            'tindak_lanjut_temuans.*',
            'lhps.nomor_lhp as nomor_lhp',
            'temuans.judul_temuan',
            'temuans.kondisi_temuan',
            'temuans.kriteria_temuan',
            'temuans.sebab_temuan',
            'temuans.akibat_temuan',
            DB::raw("CONCAT(kode_temuans.kode, ' | ', kode_temuans.nama_temuan) as kode_nama_temuan"),
            'auditis.nama_auditi'
        )
            ->leftJoin('lhps', 'lhps.id', '=', 'tindak_lanjut_temuans.lhp_id')
            ->leftJoin('temuans', 'temuans.id', '=', 'tindak_lanjut_temuans.temuan_id')
            ->leftJoin('kode_temuans', 'kode_temuans.id', '=', 'temuans.kode_temuan_id')
            ->leftJoin('auditis', 'auditis.id', '=', 'tindak_lanjut_temuans.auditi_id');

        // Filter role user
        if (!$user->hasRole('super_admin')) {
            $query->where(function ($q) use ($user) {
                // Auditi hanya lihat tindak lanjut mereka sendiri
                $q->where('tindak_lanjut_temuans.auditi_id', $user->id);

                // Auditor lihat semua auditi di wilayahnya
                if ($user->hasRole('auditor')) {
                    $q->orWhere(function ($q2) use ($user) {
                        $q2->where('auditis.irbanwil_id', $user->irbanwil_id);
                    });
                }

                // Auditi lain (misal supervisor/role lain yang punya relasi auditi_id)
                if ($user->hasRole('auditi')) {
                    $q->orWhere('tindak_lanjut_temuans.auditi_id', $user->auditi_id);
                }
            });
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('can_show', fn($r) => $user->hasMenuPermission($activeMenu->id, 'show'))
            ->addColumn('can_approve', function ($r) use ($user) {
                if (in_array($r->approval_status, ['draft', 'waiting'])) {
                    if ($r->approval_status === 'draft' && $user->auditi_id === $r->auditi_id && $r->deskripsi_tindak_lanjut != null) return true;

                    if ($r->approval_status === 'waiting') {
                        $routes = ApprovalRoute::where('module', 'tindak_lanjut_temuan')
                            ->where('sequence', $r->current_approval_sequence)
                            ->get();
                        $userRoleIds = $user->roles->pluck('id')->toArray();
                        foreach ($routes as $route) {
                            if ($route->assigned_user_id && $route->assigned_user_id == $user->id) return true;
                            if (!$route->assigned_user_id && in_array($route->role_id, $userRoleIds)) return true;
                        }
                    }
                }
                return false;
            })
            ->addColumn('show_url', fn($r) => route('tindak_lanjut_temuan.show', $r->id))
            ->addColumn('approve_url', fn($r) => route('tindak_lanjut_temuan.approve', $r->id))
            ->addColumn('batas_waktu', function ($r) {
                $batasWaktu = Carbon::parse($r->created_at)->addDays(30);
                return [
                    'formatted' => $batasWaktu->translatedFormat('d F Y'),
                    'expired' => now()->greaterThan($batasWaktu),
                ];
            })
            ->rawColumns(['kondisi_temuan', 'kriteria_temuan', 'sebab_temuan', 'akibat_temuan'])
            ->make(true);
    }

    public function create() {}
    public function store(StoreTindakLanjutTemuanRequest $request) {}

    public function show(TindakLanjutTemuan $tindak_lanjut_temuan)
    {
        $user = auth()->user();
        $canApprove = false;
        $canInputTindakLanjut = false;

        // === Cek izin approve ===
        if ($tindak_lanjut_temuan->approval_status === 'draft') {
            $canApprove = $user->auditi_id === $tindak_lanjut_temuan->auditi_id;
        } elseif ($tindak_lanjut_temuan->approval_status === 'waiting') {
            $routes = ApprovalRoute::where('module', 'tindak_lanjut_temuan')
                ->where('sequence', $tindak_lanjut_temuan->current_approval_sequence)
                ->get();

            foreach ($routes as $route) {
                if (
                    ($route->assigned_user_id && $route->assigned_user_id == $user->id) ||
                    (!$route->assigned_user_id && $user->roles->pluck('id')->contains($route->role_id))
                ) {
                    $canApprove = true;
                    break;
                }
            }
        }

        // === Hitung apakah auditi masih boleh input tindak lanjut ===
        $batasWaktu = Carbon::parse($tindak_lanjut_temuan->created_at)->addDays(30);
        $masihDalamBatas = now()->lessThanOrEqualTo($batasWaktu);

        if (
            $user->hasRole('auditi') &&
            $tindak_lanjut_temuan->approval_status === 'draft' &&
            $masihDalamBatas
        ) {
            $canInputTindakLanjut = true;
        }

        return view('pelaksanaan.tindak_lanjut_temuan.show', compact(
            'tindak_lanjut_temuan',
            'canApprove',
            'masihDalamBatas',
            'canInputTindakLanjut',
            'batasWaktu'
        ));
    }

    public function edit(TindakLanjutTemuan $tindak_lanjut_temuan)
    {
        // biasanya tidak digunakan, karena input dilakukan dari show view
    }

    public function update(UpdateTindakLanjutTemuanRequest $request, TindakLanjutTemuan $tindak_lanjut_temuan)
    {
        $batasWaktu = Carbon::parse($tindak_lanjut_temuan->created_at)->addDays(30);
        if (now()->greaterThan($batasWaktu)) {
            return back()->with('error', 'Waktu pengisian tindak lanjut sudah melewati 30 hari.');
        }

        // Cek status masih draft
        if ($tindak_lanjut_temuan->approval_status !== 'draft') {
            return back()->with('error', 'Data tidak dapat diubah karena status bukan draft.');
        }

        DB::beginTransaction();
        try {
            $data = $request->validated();

            // Isi otomatis tanggal tindak lanjut jika belum ada
            if (empty($data['tanggal_tindak_lanjut'])) {
                $data['tanggal_tindak_lanjut'] = now()->toDateString();
            }

            // Proses upload lampiran baru (multi file)
            if ($request->hasFile('lampiran')) {
                // Hapus file lama jika ada
                if ($tindak_lanjut_temuan->lampiran) {
                    $existingFiles = json_decode($tindak_lanjut_temuan->lampiran, true);
                    if (is_array($existingFiles)) {
                        foreach ($existingFiles as $file) {
                            if (Storage::disk('public')->exists('tindak_lanjut_temuans/' . basename($file))) {
                                Storage::disk('public')->delete('tindak_lanjut_temuans/' . basename($file));
                            }
                        }
                    }
                }

                // Simpan file baru dengan nama asli
                $paths = [];
                foreach ($request->file('lampiran') as $file) {
                    $filename = $file->getClientOriginalName();
                    $file->storeAs('tindak_lanjut_temuans', $filename, 'public');
                    $paths[] = 'tindak_lanjut_temuans/' . $filename;
                }

                $data['lampiran'] = json_encode($paths);
            }

            // Update data utama
            $tindak_lanjut_temuan->update([
                'deskripsi_tindak_lanjut' => $data['deskripsi_tindak_lanjut'],
                'tanggal_tindak_lanjut'   => $data['tanggal_tindak_lanjut'],
                'lampiran'                => $data['lampiran'] ?? $tindak_lanjut_temuan->lampiran,
            ]);

            DB::commit();

            return redirect()
                ->route('tindak_lanjut_temuan.index')
                ->with('success', 'Tindak Lanjut Temuan berhasil diperbarui.');
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Error update Tindak Lanjut Temuan: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat update Tindak Lanjut Temuan.');
        }
    }

    public function destroy(TindakLanjutTemuan $tindak_lanjut_temuan) {}

    public function approve(Request $request, TindakLanjutTemuan $tindak_lanjut_temuan)
    {
        $user = auth()->user();
        $action = $request->input('action'); // approve / reject / revise
        $note = $request->input('note');

        DB::beginTransaction();

        try {
            $routes = ApprovalRoute::where('module', 'tindak_lanjut_temuan')->orderBy('sequence')->get();
            $userRoleIds = $user->roles->pluck('id')->toArray();

            $canUserApproveRoute = function ($route) use ($user, $userRoleIds) {
                return ($route->assigned_user_id && $route->assigned_user_id == $user->id)
                    || (!$route->assigned_user_id && in_array($route->role_id, $userRoleIds));
            };

            // ===================== DRAFT =====================
            if ($tindak_lanjut_temuan->approval_status === 'draft') {
                if ($routes->isEmpty()) {
                    // Langsung final approved
                    $tindak_lanjut_temuan->update([
                        'approval_status' => 'approved',
                        'is_final_approved' => true,
                        'approval_note' => $note,
                        'approved_by' => $user->id,
                        'approved_at' => now(),
                    ]);
                    event(new TindakLanjutTemuanEvent($tindak_lanjut_temuan, 'approve', $user, $note));
                } else {
                    $tindak_lanjut_temuan->update([
                        'approval_status' => 'waiting',
                        'current_approval_sequence' => $routes->first()->sequence,
                        'approval_note' => $note,
                        'approved_by' => $user->id,
                        'approved_at' => now(),
                    ]);
                    event(new TindakLanjutTemuanEvent($tindak_lanjut_temuan, 'waiting', $user, $note));
                }

                DB::commit();
                return response()->json([
                    'status' => true,
                    'message' => 'Tindak Lanjut Temuan dikirim ke tahap approval pertama.'
                ]);
            }

            // ===================== WAITING =====================
            if ($tindak_lanjut_temuan->approval_status === 'waiting') {
                $currentRoute = $routes->firstWhere('sequence', $tindak_lanjut_temuan->current_approval_sequence);

                if (!$currentRoute || !$canUserApproveRoute($currentRoute)) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Anda tidak memiliki hak untuk approval ini.'
                    ]);
                }

                // -------- APPROVE --------
                if ($action === 'approve') {
                    $nextRoute = $routes->firstWhere('sequence', $tindak_lanjut_temuan->current_approval_sequence + 1);

                    if ($nextRoute) {
                        // Next approver
                        $tindak_lanjut_temuan->update([
                            'current_approval_sequence' => $nextRoute->sequence,
                            'approval_note' => $note,
                            'approved_by' => $user->id,
                            'approved_at' => now(),
                        ]);

                        event(new TindakLanjutTemuanEvent($tindak_lanjut_temuan, 'waiting', $user, $note));

                        DB::commit();
                        return response()->json([
                            'status' => true,
                            'message' => 'Tindak Lanjut Temuan diteruskan ke level approval berikutnya.'
                        ]);
                    } else {
                        // Final approve
                        $tindak_lanjut_temuan->update([
                            'approval_status' => 'approved',
                            'is_final_approved' => true,
                            'approval_note' => $note,
                            'approved_by' => $user->id,
                            'approved_at' => now(),
                        ]);

                        event(new TindakLanjutTemuanEvent($tindak_lanjut_temuan, 'approve', $user, $note));

                        DB::commit();
                        return response()->json([
                            'status' => true,
                            'message' => 'Tindak Lanjut Temuan disetujui secara final.'
                        ]);
                    }
                }

                // -------- REJECT --------
                if ($action === 'reject') {
                    $tindak_lanjut_temuan->update([
                        'approval_status' => 'rejected',
                        'approval_note' => $note,
                        'approved_by' => $user->id,
                        'approved_at' => now(),
                    ]);

                    event(new TindakLanjutTemuanEvent($tindak_lanjut_temuan, 'reject', $user, $note));

                    DB::commit();
                    return response()->json([
                        'status' => true,
                        'message' => 'Tindak Lanjut Temuan ditolak.'
                    ]);
                }

                // -------- REVISE --------
                if ($action === 'revise') {
                    $prevRoute = $routes->where('sequence', '<', $tindak_lanjut_temuan->current_approval_sequence)
                        ->sortByDesc('sequence')
                        ->first();

                    if ($prevRoute) {
                        $tindak_lanjut_temuan->update([
                            'current_approval_sequence' => $prevRoute->sequence,
                            'approval_note' => $note,
                            'approved_by' => $user->id,
                            'approved_at' => now(),
                        ]);

                        event(new TindakLanjutTemuanEvent($tindak_lanjut_temuan, 'revise', $user, $note));

                        DB::commit();
                        return response()->json([
                            'status' => true,
                            'message' => 'Tindak Lanjut Temuan dikembalikan ke tahap sebelumnya untuk revisi.'
                        ]);
                    } else {
                        $tindak_lanjut_temuan->update([
                            'approval_status' => 'draft',
                            'current_approval_sequence' => null,
                            'approval_note' => $note,
                        ]);

                        event(new TindakLanjutTemuanEvent($tindak_lanjut_temuan, 'revise', $user, $note));

                        DB::commit();
                        return response()->json([
                            'status' => true,
                            'message' => 'Tindak Lanjut Temuan dikembalikan ke pembuat untuk revisi.'
                        ]);
                    }
                }
            }

            return response()->json([
                'status' => false,
                'message' => 'Status Tindak Lanjut Temuan tidak valid untuk approval.'
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Gagal approval Tindak Lanjut Temuan: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat memproses approval.'
            ]);
        }
    }
}
