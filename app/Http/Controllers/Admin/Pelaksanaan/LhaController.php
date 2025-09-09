<?php

namespace App\Http\Controllers\Admin\Pelaksanaan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Lha\StoreLhaRequest;
use App\Http\Requests\Lha\UpdateLhaRequest;
use App\Models\Lha;
use App\Models\Pkpt;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Str;

class LhaController extends Controller
{
    public function index()
    {
        return view('pelaksanaan.lha.index');
    }

    public function data()
    {
        $activeMenu = currentMenu();

        $query = Lha::with([
            'pkpt:id,no_pkpt,auditi_id,sasaran',
            'pkpt.auditi:id,nama_auditi'
        ])->select('lhas.*');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('pkpt_no', fn($row) => $row->pkpt->no_pkpt ?? '-')
            ->addColumn('pkpt_auditi', fn($row) => $row->pkpt->auditi->nama_auditi ?? '-') // ✅ ambil dari relasi auditi
            ->addColumn('pkpt_sasaran', fn($row) => $row->pkpt->sasaran ?? '-')
            ->editColumn('nomor_lha', fn($row) => $row->nomor_lha ?? '-')
            ->editColumn('tanggal_lha', fn($r) => $r->tanggal_lha ? $r->tanggal_lha->format('d-m-Y') : '-')
            ->editColumn('rekomendasi', fn($row) => $row->rekomendasi ? Str::limit($row->rekomendasi, 50) : '-')
            ->addColumn('can_show', fn() => Auth::user()->hasMenuPermission($activeMenu->id, 'show'))
            ->addColumn('can_edit', fn() => Auth::user()->hasMenuPermission($activeMenu->id, 'edit'))
            ->addColumn('can_delete', fn() => Auth::user()->hasMenuPermission($activeMenu->id, 'destroy'))
            ->addColumn('edit_url', fn($row) => route('lha.edit', $row->id))
            ->addColumn('show_url', fn($row) => route('lha.show', $row->id))
            ->addColumn('delete_url', fn($row) => route('lha.destroy', $row->id))
            ->rawColumns(['rekomendasi'])
            ->make(true);
    }

    public function create()
    {
        $pkpts = Pkpt::orderBy('tahun', 'desc')->get();
        return view('pelaksanaan.lha.create', compact('pkpts'));
    }

    public function store(StoreLhaRequest $request)
    {
        DB::beginTransaction();
        try {
            // Ambil data hasil validasi dari FormRequest
            $data = $request->validated();

            // Ambil bulan & tahun dari tanggal LHA
            $tanggal = Carbon::parse($data['tanggal_lha']);
            $bulan = $tanggal->format('m');
            $tahun = $tanggal->format('Y');

            // Cari nomor terakhir di bulan & tahun ini
            $lastLha = Lha::whereYear('tanggal_lha', $tahun)
                ->whereMonth('tanggal_lha', $bulan)
                ->orderBy('id', 'desc')
                ->first();

            // Tentukan urutan
            $urutan = $lastLha ? $lastLha->id + 1 : 1;

            // Generate nomor otomatis
            $data['nomor_lha'] = sprintf("LHA-%02d-%d-%02d", $bulan, $tahun, $urutan);

            // Tambahkan audit trail
            $data['created_by'] = auth()->id();

            // Upload file jika ada
            if ($request->hasFile('file_lha')) {
                $data['file_lha'] = $request->file('file_lha')->store('lhas', 'public');
            }

            // Simpan ke DB
            Lha::create($data);

            DB::commit();
            session()->flash('success', 'LHA berhasil dibuat.');
            return redirect()->route('lha.index');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error simpan LHA: ' . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan saat menyimpan data.');
            return redirect()->back()->withInput();
        }
    }

    public function show(Lha $lha)
    {
        $lha->load(['pkpt', 'kkas.auditor']);

        // ambil semua user yang punya role Auditor
        // $auditors = User::role('Auditor')->get();

        return view('pelaksanaan.lha.show', compact('lha'));
    }

    public function edit(Lha $lha)
    {
        $pkpts = Pkpt::orderBy('tahun', 'desc')->get();
        return view('pelaksanaan.lha.edit', compact('lha', 'pkpts'));
    }

    public function update(UpdateLhaRequest $request, Lha $lha)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();

            if ($request->hasFile('file_lha')) {
                // hapus file lama jika ada
                if ($lha->file_lha && Storage::disk('public')->exists($lha->file_lha)) {
                    Storage::disk('public')->delete($lha->file_lha);
                }
                $path = $request->file('file_lha')->store('lha', 'public');
                $data['file_lha'] = $path;
            }

            $lha->update($data);
            DB::commit();

            session()->flash('success', 'LHA berhasil diperbarui.');
            return redirect()->route('lha.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error update LHA: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan saat update LHA.');
        }
    }

    public function destroy(Lha $lha)
    {
        DB::beginTransaction();
        try {
            // hapus file
            if ($lha->file_lha && Storage::disk('public')->exists($lha->file_lha)) {
                Storage::disk('public')->delete($lha->file_lha);
            }
            $lha->delete();
            DB::commit();
            session()->flash('success', 'LHA berhasil dihapus.');
            return redirect()->route('lha.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error hapus LHA: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menghapus LHA.');
        }
    }
}
