<?php

namespace App\Http\Controllers\Admin\Pelaksanaan;

use App\Events\LhpEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Lhp\StoreLhpRequest;
use App\Http\Requests\Lhp\UpdateLhpRequest;
use App\Models\ApprovalRoute;
use App\Models\KodeRekomendasi;
use App\Models\KodeTemuan;
use App\Models\Lhp;
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
use Str;

class LhpController extends Controller
{
    public function index()
    {
        return view('pelaksanaan.lhp.index');
    }

    public function data()
    {
        $activeMenu = currentMenu();
        $user = Auth::user();

        // Query dengan join untuk sort & search
        $query = Lhp::select('lhps.*', 'auditis.nama_auditi as auditi_nama', 'pkpts.no_pkpt as pkpt_no', 'pkpts.sasaran as pkpt_sasaran')
            ->leftJoin('auditis', 'auditis.id', '=', 'lhps.auditi_id')
            ->leftJoin('pkpts', 'pkpts.id', '=', 'lhps.pkpt_id');

        // Filter role user
        if (!$user->hasRole('super_admin')) {
            $query->where(function ($q) use ($user) {
                $q->where('lhps.created_by', $user->id)
                    ->orWhere(function ($q2) use ($user) {
                        if ($user->hasRole('auditor')) {
                            $q2->whereHas('pkpt.auditis', fn($q3) => $q3->where('irbanwil_id', $user->irbanwil_id))
                                ->where('approval_status', '!=', 'draft');
                        } elseif ($user->hasRole('auditi')) {
                            $q2->whereHas('auditi', fn($q3) => $q3->where('id', $user->auditi_id))
                                ->where('approval_status', '!=', 'draft');
                        } elseif ($user->hasRole('approver')) {
                            $q2->where('approval_status', '!=', 'draft');
                        }
                    });
            });
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('pkpt_no', fn($r) => $r->pkpt_no ?? '-')
            ->editColumn('pkpt_sasaran', fn($r) => $r->pkpt_sasaran ?? '-')
            ->editColumn('nomor_lhp', fn($r) => $r->nomor_lhp ?? '-')
            ->editColumn('auditi', fn($r) => $r->auditi_nama ?? '-')
            ->editColumn('tanggal_lhp', fn($r) => $r->tanggal_lhp ? $r->tanggal_lhp->format('d-m-Y') : '-')
            ->editColumn('rekomendasi', fn($r) => $r->rekomendasi ? Str::limit($r->rekomendasi, 50) : '-')
            ->addColumn('next_approver', function ($r) {
                if ($r->approval_status === 'draft') return 'Menunggu pengajuan oleh pembuat';
                if ($r->approval_status === 'waiting') {
                    $routes = ApprovalRoute::where('module', 'lhp')
                        ->where('sequence', $r->current_approval_sequence)
                        ->get();
                    if ($routes->isEmpty()) return '-';

                    $names = [];
                    foreach ($routes as $route) {
                        if ($route->assigned_user_id) $names[] = optional($route->assignedUser)->name;
                        elseif ($route->role_id) $names[] = 'Role: ' . optional($route->role)->name;
                    }
                    return 'Menunggu approval oleh ' . implode(', ', $names);
                }
                return '-';
            })
            ->addColumn('is_super_admin', fn() => $user->hasRole('super_admin'))
            ->addColumn('can_show', fn() => $user->hasMenuPermission($activeMenu->id, 'show'))
            ->addColumn('can_edit', function ($r) use ($user) {
                $menuPermission = $user->hasMenuPermission(currentMenu()->id, 'edit');
                $isDraft = $r->approval_status === 'draft';
                $isCreator = $user->id === $r->created_by;

                $canEdit = $menuPermission && $isDraft && $isCreator;

                // Log detail kenapa bisa false
                if (!$canEdit) {
                    Log::info("can_edit false", [
                        'user_id' => $user->id,
                        'tindak_lanjut_id' => $r->id,
                        'has_menu_permission' => $menuPermission,
                        'approval_status' => $r->approval_status,
                        'is_creator' => $isCreator
                    ]);
                }

                return $canEdit;
            })
            ->addColumn('can_delete', fn() => $user->hasMenuPermission($activeMenu->id, 'destroy'))
            ->addColumn('can_approve', function ($r) use ($user) {
                if ($user->hasMenuPermission(currentMenu()->id, 'approve') && in_array($r->approval_status, ['draft', 'waiting'])) {
                    if ($r->approval_status === 'draft' && $user->id === $r->created_by) return true;
                    if ($r->approval_status === 'waiting') {
                        $routes = ApprovalRoute::where('module', 'lhp')
                            ->where('sequence', $r->current_approval_sequence)
                            ->get();
                        $userRoleIds = $user->roles->pluck('id')->toArray();
                        foreach ($routes as $route) {
                            if ($route->assigned_user_id && $route->assigned_user_id == $user->id) return true;
                            if (!$route->assigned_user_id && in_array($route->role_id, $userRoleIds)) return true;
                        }
                        return false;
                    }
                }
                return false;
            })
            ->addColumn('show_url', fn($r) => route('lhp.show', $r->id))
            ->addColumn('edit_url', fn($r) => route('lhp.edit', $r->id))
            ->addColumn('delete_url', fn($r) => route('lhp.destroy', $r->id))
            ->addColumn('approve_url', fn($r) => route('lhp.approve', $r->id))
            ->filterColumn('auditi', fn($query, $keyword) => $query->where('auditis.nama_auditi', 'like', "%{$keyword}%"))
            ->orderColumn('auditi', 'auditis.nama_auditi $1')
            ->rawColumns(['rekomendasi'])
            ->make(true);
    }

    public function create()
    {
        $user = Auth::user();

        $pkpts = Pkpt::with([
            'auditis:id,nama_auditi,irbanwil_id',
        ]);

        // Jika bukan super admin, filter PKPT berdasarkan irbanwil auditor (user login)
        if (!$user->hasRole('super_admin')) {
            $pkpts = $pkpts->whereHas('auditis', function ($q) use ($user) {
                $q->where('irbanwil_id', $user->irbanwil_id);
            });
        }

        $pkpts = $pkpts->orderBy('tahun', 'desc')->get();

        // Ambil kode temuan
        $kodeTemuans = KodeTemuan::with('rekomendasis')->whereNotNull('parent_id')->get();

        // Ambil kode rekomendasi
        $kodeRekomendasis = KodeRekomendasi::orderBy('urutan')->get();

        // Buat mapping kode_temuan_id => daftar rekomendasi
        $mapping = [];
        foreach ($kodeTemuans as $kt) {
            $mapping[$kt->id] = $kt->rekomendasis->map(function ($kr) {
                return [
                    'id' => $kr->id,
                    'kode' => $kr->kode,
                    'nama_rekomendasi' => $kr->nama_rekomendasi,
                ];
            })->toArray();
        }

        return view('pelaksanaan.lhp.create', compact(
            'pkpts',
            'kodeTemuans',
            'kodeRekomendasis',
            'mapping' // <- tambahkan mapping
        ));
    }
    public function store(StoreLhpRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();

            // Generate nomor LHP otomatis
            $tanggal = Carbon::parse($data['tanggal_lhp']);
            $bulan = $tanggal->format('m');
            $tahun = $tanggal->format('Y');

            $lastLhp = Lhp::whereYear('tanggal_lhp', $tahun)
                ->whereMonth('tanggal_lhp', $bulan)
                ->orderBy('id', 'desc')
                ->first();

            $urutan = $lastLhp ? $lastLhp->id + 1 : 1;
            $data['nomor_lhp'] = sprintf("LHP-%02d-%d-%03d", $bulan, $tahun, $urutan);
            $data['created_by'] = Auth::id();

            // Upload file LHP utama
            if ($request->hasFile('file_lhp')) {
                $data['file_lhp'] = $request->file('file_lhp')->store('lhps', 'public');
            }

            // Buat LHP
            $lhp = Lhp::create($data);

            // ========================
            // Simpan Data Temuan
            // ========================
            if (!empty($data['temuans'])) {
                foreach ($data['temuans'] as $index => $temuanData) {
                    $temuan = $lhp->temuans()->create([
                        'judul_temuan'    => $temuanData['judul_temuan'] ?? null,
                        'kode_temuan_id'  => $temuanData['kode_temuan_id'] ?? null,
                        'kondisi_temuan'  => $temuanData['kondisi_temuan'] ?? null,
                        'kriteria_temuan' => $temuanData['kriteria_temuan'] ?? null,
                        'sebab_temuan'    => $temuanData['sebab_temuan'] ?? null,
                        'akibat_temuan'   => $temuanData['akibat_temuan'] ?? null,
                    ]);

                    // Simpan rekomendasi
                    if (!empty($temuanData['rekomendasis'])) {
                        foreach ($temuanData['rekomendasis'] as $rekomendasiData) {
                            $temuan->rekomendasis()->create([
                                'kode_rekomendasi_id' => $rekomendasiData['kode_rekomendasi_id'] ?? null,
                                'rekomendasi_temuan'  => $rekomendasiData['rekomendasi_temuan'] ?? null,
                                'nominal'             => $rekomendasiData['nominal'] ?? 0,
                            ]);
                        }
                    }

                    // Simpan semua file pendukung (nested array)
                    if (!empty($temuanData['files'])) {
                        foreach ($temuanData['files'] as $file) {
                            if ($file) {
                                $path = $file->store('temuan_files', 'public');
                                $temuan->files()->create([
                                    'file_path' => $path,
                                    'file_name' => $file->getClientOriginalName(),
                                    'file_type' => $file->getClientOriginalExtension(),
                                    'file_size' => $file->getSize(),
                                ]);
                            }
                        }
                    }
                }
            }

            DB::commit();
            return redirect()->route('lhp.index')->with('success', 'LHP berhasil disimpan.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Gagal simpan LHP: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    public function show(Lhp $lhp)
    {
        // Load relasi yang dibutuhkan
        $lhp->load([
            'pkpt', // data PKPT
            'kkas.auditor', // data KKA dan auditor
            'temuans.rekomendasis.kodeRekomendasi', // rekomendasi tiap temuan
            'temuans.files' // file pendukung tiap temuan
        ]);

        $user = auth()->user();
        $canApprove = false;

        if ($lhp->approval_status === 'draft') {
            // Draft → hanya pembuat bisa kirim
            $canApprove = $user->id === $lhp->created_by;
        } elseif ($lhp->approval_status === 'waiting') {
            // Waiting → cek approval route
            $routes = ApprovalRoute::where('module', 'lhp')
                ->where('sequence', $lhp->current_approval_sequence)
                ->get();

            foreach ($routes as $route) {
                if (($route->assigned_user_id && $route->assigned_user_id == $user->id) ||
                    (!$route->assigned_user_id && $user->roles->pluck('id')->contains($route->role_id))
                ) {
                    $canApprove = true;
                    break;
                }
            }
        }

        return view('pelaksanaan.lhp.show', compact('lhp', 'canApprove'));
    }

    public function edit(Lhp $lhp)
    {
        $user = Auth::user();
        $pkpts = Pkpt::with('auditis:id,nama_auditi,irbanwil_id');

        if (!$user->hasRole('super_admin')) {
            $pkpts = $pkpts->whereHas('auditis', function ($q) use ($user) {
                $q->where('irbanwil_id', $user->irbanwil_id);
            });
        }

        $pkpts = $pkpts->orderBy('tahun', 'desc')->get();

        $kodeTemuans = KodeTemuan::with('parent')
            ->whereNotNull('parent_id')
            ->get();
        // Ambil kode rekomendasi
        $kodeRekomendasis = KodeRekomendasi::orderBy('urutan')->get();

        // Buat mapping kode_temuan_id => daftar rekomendasi
        $mapping = [];
        foreach ($kodeTemuans as $kt) {
            $mapping[$kt->id] = $kt->rekomendasis->map(function ($kr) {
                return [
                    'id' => $kr->id,
                    'kode' => $kr->kode,
                    'nama_rekomendasi' => $kr->nama_rekomendasi,
                ];
            })->toArray();
        }

        // load temuan & rekomendasi
        $lhp->load(['temuans.rekomendasis', 'temuans.files']);

        return view('pelaksanaan.lhp.edit', compact('lhp', 'pkpts', 'kodeTemuans', 'kodeRekomendasis', 'mapping'));
    }

    public function update(UpdateLhpRequest $request, Lhp $lhp)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();

            // Update file LHP utama jika ada
            if ($request->hasFile('file_lhp')) {
                if ($lhp->file_lhp && Storage::disk('public')->exists($lhp->file_lhp)) {
                    Storage::disk('public')->delete($lhp->file_lhp);
                }
                $data['file_lhp'] = $request->file('file_lhp')->store('lhps', 'public');
            }

            // Update LHP utama
            $lhp->update($data);

            // ========================
            // HANDLE TEMUAN
            // ========================
            $temuansInput = $request->input('temuans', []);
            $existingTemuanIds = $lhp->temuans()->pluck('id')->toArray();
            $inputTemuanIds = array_filter(array_column($temuansInput, 'id'));

            // Hapus temuan yang tidak ada di input
            $toDeleteTemuan = array_diff($existingTemuanIds, $inputTemuanIds);
            Temuan::whereIn('id', $toDeleteTemuan)->delete();

            foreach ($temuansInput as $i => $temuanData) {

                // ========================
                // Buat atau update temuan
                // ========================
                if (!empty($temuanData['id'])) {
                    $temuan = Temuan::find($temuanData['id']);
                    $temuan->update([
                        'judul_temuan'    => $temuanData['judul_temuan'] ?? null,
                        'kode_temuan_id'  => $temuanData['kode_temuan_id'] ?? null,
                        'kondisi_temuan'  => $temuanData['kondisi_temuan'] ?? null,
                        'kriteria_temuan' => $temuanData['kriteria_temuan'] ?? null,
                        'sebab_temuan'    => $temuanData['sebab_temuan'] ?? null,
                        'akibat_temuan'   => $temuanData['akibat_temuan'] ?? null,
                    ]);
                } else {
                    $temuan = $lhp->temuans()->create([
                        'judul_temuan'    => $temuanData['judul_temuan'] ?? null,
                        'kode_temuan_id'  => $temuanData['kode_temuan_id'] ?? null,
                        'kondisi_temuan'  => $temuanData['kondisi_temuan'] ?? null,
                        'kriteria_temuan' => $temuanData['kriteria_temuan'] ?? null,
                        'sebab_temuan'    => $temuanData['sebab_temuan'] ?? null,
                        'akibat_temuan'   => $temuanData['akibat_temuan'] ?? null,
                    ]);
                }

                // ========================
                // HANDLE REKOMENDASI
                // ========================
                $rekomendasisInput = $temuanData['rekomendasis'] ?? [];
                $existingRekomIds = $temuan->rekomendasis()->pluck('id')->toArray();
                $inputRekomIds = array_filter(array_column($rekomendasisInput, 'id'));

                // Hapus rekomendasi yang tidak ada di input
                $toDeleteRekom = array_diff($existingRekomIds, $inputRekomIds);
                TemuanRekomendasi::whereIn('id', $toDeleteRekom)->delete();

                foreach ($rekomendasisInput as $r) {
                    if (!empty($r['id'])) {
                        $rekom = TemuanRekomendasi::find($r['id']);
                        $rekom->update([
                            'kode_rekomendasi_id' => $r['kode_rekomendasi_id'] ?? null,
                            'rekomendasi_temuan'  => $r['rekomendasi_temuan'] ?? null,
                            'nominal'             => $r['nominal'] ?? 0,
                        ]);
                    } else {
                        $temuan->rekomendasis()->create([
                            'kode_rekomendasi_id' => $r['kode_rekomendasi_id'] ?? null,
                            'rekomendasi_temuan'  => $r['rekomendasi_temuan'] ?? null,
                            'nominal'             => $r['nominal'] ?? 0,
                        ]);
                    }
                }

                // ========================
                // HANDLE FILES
                // ========================

                // Hapus file lama yang tidak dipertahankan
                $oldFilesInput = $temuanData['old_files'] ?? [];
                $temuan->files()->whereNotIn('id', $oldFilesInput)->get()->each(function ($file) {
                    if (Storage::disk('public')->exists($file->file_path)) {
                        Storage::disk('public')->delete($file->file_path);
                    }
                    $file->delete();
                });

                // Upload file baru
                $newFiles = $temuanData['files'] ?? [];
                foreach ($newFiles as $file) {
                    if ($file) {
                        $path = $file->store('temuan_files', 'public');
                        $temuan->files()->create([
                            'file_path' => $path,
                            'file_name' => $file->getClientOriginalName(),
                            'file_type' => $file->getClientOriginalExtension(),
                            'file_size' => $file->getSize(),
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('lhp.index')->with('success', 'LHP berhasil diperbarui.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error update LHP: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->withInput()->with('error', 'Terjadi kesalahan saat update LHP.');
        }
    }

    public function destroy(Lhp $lhp)
    {
        DB::beginTransaction();
        try {
            // hapus file
            if ($lhp->file_lhp && Storage::disk('public')->exists($lhp->file_lhp)) {
                Storage::disk('public')->delete($lhp->file_lhp);
            }
            $lhp->delete();
            DB::commit();
            session()->flash('success', 'LHP berhasil dihapus.');
            return redirect()->route('lhp.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error hapus LHP: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menghapus LHP.');
        }
    }

    public function approve(Request $request, Lhp $lhp)
    {
        $user = auth()->user();
        $action = $request->input('action'); // approve / reject / revise
        $note = $request->input('note');

        DB::beginTransaction();

        try {
            $routes = ApprovalRoute::where('module', 'lhp')->orderBy('sequence')->get();
            $userRoleIds = $user->roles->pluck('id')->toArray();

            $canUserApproveRoute = function ($route) use ($user, $userRoleIds) {
                return ($route->assigned_user_id && $route->assigned_user_id == $user->id)
                    || (!$route->assigned_user_id && in_array($route->role_id, $userRoleIds));
            };

            // ===================== DRAFT =====================
            if ($lhp->approval_status === 'draft') {
                if ($routes->isEmpty()) {
                    // Langsung final approved
                    $lhp->update([
                        'approval_status' => 'approved',
                        'is_final_approved' => true,
                        'approval_note' => $note,
                        'approved_by' => $user->id,
                        'approved_at' => now(),
                    ]);
                    event(new LhpEvent($lhp, 'approve', $user, $note));
                } else {
                    $lhp->update([
                        'approval_status' => 'waiting',
                        'current_approval_sequence' => $routes->first()->sequence,
                        'approval_note' => $note,
                        'approved_by' => $user->id,
                        'approved_at' => now(),
                    ]);
                    event(new LhpEvent($lhp, 'waiting', $user, $note));
                }

                DB::commit();
                return response()->json([
                    'status' => true,
                    'message' => 'LHP dikirim ke tahap approval pertama.'
                ]);
            }

            // ===================== WAITING =====================
            if ($lhp->approval_status === 'waiting') {
                $currentRoute = $routes->firstWhere('sequence', $lhp->current_approval_sequence);

                if (!$currentRoute || !$canUserApproveRoute($currentRoute)) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Anda tidak memiliki hak untuk approval ini.'
                    ]);
                }

                // -------- APPROVE --------
                if ($action === 'approve') {
                    $nextRoute = $routes->firstWhere('sequence', $lhp->current_approval_sequence + 1);

                    if ($nextRoute) {
                        // Next approver
                        $lhp->update([
                            'current_approval_sequence' => $nextRoute->sequence,
                            'approval_note' => $note,
                            'approved_by' => $user->id,
                            'approved_at' => now(),
                        ]);

                        event(new LhpEvent($lhp, 'waiting', $user, $note));

                        DB::commit();
                        return response()->json([
                            'status' => true,
                            'message' => 'LHP diteruskan ke level approval berikutnya.'
                        ]);
                    } else {
                        // Final approve
                        $lhp->update([
                            'approval_status' => 'approved',
                            'is_final_approved' => true,
                            'approval_note' => $note,
                            'approved_by' => $user->id,
                            'approved_at' => now(),
                        ]);

                        event(new LhpEvent($lhp, 'approve', $user, $note));

                        DB::commit();
                        return response()->json([
                            'status' => true,
                            'message' => 'LHP disetujui secara final.'
                        ]);
                    }
                }

                // -------- REJECT --------
                if ($action === 'reject') {
                    $lhp->update([
                        'approval_status' => 'rejected',
                        'approval_note' => $note,
                        'approved_by' => $user->id,
                        'approved_at' => now(),
                    ]);

                    event(new LhpEvent($lhp, 'reject', $user, $note));

                    DB::commit();
                    return response()->json([
                        'status' => true,
                        'message' => 'LHP ditolak.'
                    ]);
                }

                // -------- REVISE --------
                if ($action === 'revise') {
                    $prevRoute = $routes->where('sequence', '<', $lhp->current_approval_sequence)
                        ->sortByDesc('sequence')
                        ->first();

                    if ($prevRoute) {
                        $lhp->update([
                            'current_approval_sequence' => $prevRoute->sequence,
                            'approval_note' => $note,
                            'approved_by' => $user->id,
                            'approved_at' => now(),
                        ]);

                        event(new LhpEvent($lhp, 'revise', $user, $note));

                        DB::commit();
                        return response()->json([
                            'status' => true,
                            'message' => 'LHP dikembalikan ke tahap sebelumnya untuk revisi.'
                        ]);
                    } else {
                        $lhp->update([
                            'approval_status' => 'draft',
                            'current_approval_sequence' => null,
                            'approval_note' => $note,
                        ]);

                        event(new LhpEvent($lhp, 'revise', $user, $note));

                        DB::commit();
                        return response()->json([
                            'status' => true,
                            'message' => 'LHP dikembalikan ke pembuat untuk revisi.'
                        ]);
                    }
                }
            }

            return response()->json([
                'status' => false,
                'message' => 'Status LHP tidak valid untuk approval.'
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Gagal approval LHP: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat memproses approval.'
            ]);
        }
    }
}
