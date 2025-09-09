<?php

namespace App\Http\Controllers\Admin\Pelaksanaan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Temuan\StoreTemuanRequest;
use App\Http\Requests\Temuan\UpdateTemuanRequest;
use App\Models\Lha;
use App\Models\Temuan;
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

class TemuanController extends Controller
{
    public function index()
    {
        return view('pelaksanaan.temuan.index');
    }

    public function data()
    {
        $activeMenu = currentMenu();

        $query = Temuan::with([
            'pkpt:id,no_pkpt,auditi_id,sasaran',
            'pkpt.auditi:id,nama_auditi'
        ])->select('temuans.*');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('pkpt_no', fn($row) => $row->pkpt->no_pkpt ?? '-')
            ->addColumn('pkpt_auditi', fn($row) => $row->pkpt->auditi->nama_auditi ?? '-') // ✅ ambil dari relasi auditi
            ->addColumn('pkpt_sasaran', fn($row) => $row->pkpt->sasaran ?? '-')
            ->editColumn('nomor_temuan', fn($row) => $row->nomor_temuan ?? '-')
            ->editColumn('tanggal_temuan', fn($r) => $r->tanggal_temuan ? $r->tanggal_temuan->format('d-m-Y') : '-')
            ->editColumn('rekomendasi', fn($row) => $row->rekomendasi ? Str::limit($row->rekomendasi, 50) : '-')
            ->addColumn('can_show', fn() => Auth::user()->hasMenuPermission($activeMenu->id, 'show'))
            ->addColumn('can_edit', fn() => Auth::user()->hasMenuPermission($activeMenu->id, 'edit'))
            ->addColumn('can_delete', fn() => Auth::user()->hasMenuPermission($activeMenu->id, 'destroy'))
            ->addColumn('edit_url', fn($row) => route('temuan.edit', $row->id))
            ->addColumn('show_url', fn($row) => route('temuan.show', $row->id))
            ->addColumn('delete_url', fn($row) => route('temuan.destroy', $row->id))
            ->rawColumns(['rekomendasi'])
            ->make(true);
    }

    public function create()
    {
        $lhas = Lha::orderBy('tanggal_lha', 'desc')->get();
        return view('pelaksanaan.temuan.create', compact('lhas'));
    }

    public function store(StoreTemuanRequest $request)
    {
        DB::beginTransaction();
        try {
            // Ambil data hasil validasi dari FormRequest
            $data = $request->validated();

            // Ambil bulan & tahun dari tanggal LHA
            $tanggal = Carbon::parse($data['tanggal_temuan']);
            $bulan = $tanggal->format('m');
            $tahun = $tanggal->format('Y');

            // Cari nomor terakhir di bulan & tahun ini
            $lastTemuan = Temuan::whereYear('tanggal_temuan', $tahun)
                ->whereMonth('tanggal_temuan', $bulan)
                ->orderBy('id', 'desc')
                ->first();

            // Tentukan urutan
            $urutan = $lastTemuan ? $lastTemuan->id + 1 : 1;

            // Generate nomor otomatis
            $data['nomor_temuan'] = sprintf("LHA-%02d-%d-%02d", $bulan, $tahun, $urutan);

            // Tambahkan audit trail
            $data['created_by'] = auth()->id();

            // Upload file jika ada
            if ($request->hasFile('file_temuan')) {
                $data['file_temuan'] = $request->file('file_temuan')->store('temuans', 'public');
            }

            // Simpan ke DB
            Temuan::create($data);

            DB::commit();
            session()->flash('success', 'LHA berhasil dibuat.');
            return redirect()->route('temuan.index');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error simpan LHA: ' . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan saat menyimpan data.');
            return redirect()->back()->withInput();
        }
    }

    public function show(Temuan $temuan)
    {
        $temuan->load(['pkpt', 'kkas.auditor']);

        // ambil semua user yang punya role Auditor
        // $auditors = User::role('Auditor')->get();

        return view('pelaksanaan.temuan.show', compact('temuan'));
    }

    public function edit(Temuan $temuan)
    {
        $pkpts = Pkpt::orderBy('tahun', 'desc')->get();
        return view('pelaksanaan.temuan.edit', compact('temuan', 'pkpts'));
    }

    public function update(UpdateTemuanRequest $request, Temuan $temuan)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();

            if ($request->hasFile('file_temuan')) {
                // hapus file lama jika ada
                if ($temuan->file_temuan && Storage::disk('public')->exists($temuan->file_temuan)) {
                    Storage::disk('public')->delete($temuan->file_temuan);
                }
                $path = $request->file('file_temuan')->store('temuan', 'public');
                $data['file_temuan'] = $path;
            }

            $temuan->update($data);
            DB::commit();

            session()->flash('success', 'LHA berhasil diperbarui.');
            return redirect()->route('temuan.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error update LHA: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan saat update LHA.');
        }
    }

    public function destroy(Temuan $temuan)
    {
        DB::beginTransaction();
        try {
            // hapus file
            if ($temuan->file_temuan && Storage::disk('public')->exists($temuan->file_temuan)) {
                Storage::disk('public')->delete($temuan->file_temuan);
            }
            $temuan->delete();
            DB::commit();
            session()->flash('success', 'LHA berhasil dihapus.');
            return redirect()->route('temuan.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error hapus LHA: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menghapus LHA.');
        }
    }
}
