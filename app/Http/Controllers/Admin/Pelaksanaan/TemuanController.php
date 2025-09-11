<?php

namespace App\Http\Controllers\Admin\Pelaksanaan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Temuan\StoreTemuanRequest;
use App\Http\Requests\Temuan\UpdateTemuanRequest;
use App\Models\KodeRekomendasi;
use App\Models\KodeTemuan;
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
            'lha',             // ambil data Temuan
            'rekomendasis',    // ambil daftar rekomendasi
        ])->select('temuans.*');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('lha_no', fn($row) => $row->lha->nomor_lha ?? '-') // nomor Temuan langsung
            ->addColumn('judul_temuan', fn($row) => $row->judul_temuan ?? '-')
            ->addColumn('kode_temuan', fn($row) => $row->kode_temuan ?? '-')
            ->addColumn(
                'rekomendasi',
                fn($row) =>
                $row->rekomendasis->pluck('rekomendasi_temuan')->implode('<br>') ?: '-'
            )
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
        $kodeTemuans = KodeTemuan::with('parent')
            ->whereNotNull('parent_id')
            ->get();
        $kodeRekomendasis = KodeRekomendasi::orderBy('urutan')->get();
        return view('pelaksanaan.temuan.create', compact('lhas', 'kodeTemuans', 'kodeRekomendasis'));
    }

    public function store(StoreTemuanRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();

            // Simpan temuan utama
            $temuan = Temuan::create([
                'lha_id'           => $data['lha_id'],
                'kode_temuan_id'   => $data['kode_temuan_id'], // relasi foreign key
                'judul_temuan'     => $data['judul_temuan'],
                'kondisi_temuan'   => $data['kondisi_temuan'],
                'kriteria_temuan'  => $data['kriteria_temuan'] ?? null,
                'sebab_temuan'     => $data['sebab_temuan'] ?? null,
                'akibat_temuan'    => $data['akibat_temuan'] ?? null,
            ]);

            // Simpan rekomendasi (jika ada input array rekomendasi)
            if ($request->has('rekomendasis')) {
                foreach ($request->rekomendasis as $r) {
                    if (!empty($r['rekomendasi_temuan'])) {
                        $temuan->rekomendasis()->create([
                            'kode_rekomendasi_id' => $r['kode_rekomendasi_id'] ?? null,
                            'rekomendasi_temuan'  => $r['rekomendasi_temuan'],
                        ]);
                    }
                }
            }

            // Simpan file (jika ada upload)
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $path = $file->store('temuans', 'public');
                    $temuan->files()->create([
                        'file_path' => $path,
                        'file_name' => $file->getClientOriginalName(),
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('temuan.index')->with('success', 'Temuan berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error simpan Temuan: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data.');
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
        $lhas = Lha::orderBy('tanggal_lha', 'desc')->get();
        $kodeTemuans = KodeTemuan::with('parent')
            ->whereNotNull('parent_id')
            ->get();
        $kodeRekomendasis = KodeRekomendasi::orderBy('urutan')->get();
        return view('pelaksanaan.temuan.edit', compact('temuan', 'lhas', 'kodeTemuans', 'kodeRekomendasis'));
    }

    public function update(UpdateTemuanRequest $request, Temuan $temuan)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();

            // =======================
            // UPDATE DATA UTAMA
            // =======================
            $temuan->update([
                'lha_id'           => $data['lha_id'],
                'kode_temuan_id'   => $data['kode_temuan_id'],
                'judul_temuan'     => $data['judul_temuan'],
                'kondisi_temuan'   => $data['kondisi_temuan'],
                'kriteria_temuan'  => $data['kriteria_temuan'] ?? null,
                'sebab_temuan'     => $data['sebab_temuan'] ?? null,
                'akibat_temuan'    => $data['akibat_temuan'] ?? null,
            ]);

            // =======================
            // REKOMENDASI
            // =======================
            $temuan->rekomendasis()->delete();
            if ($request->has('rekomendasis')) {
                foreach ($request->rekomendasis as $r) {
                    if (!empty($r['rekomendasi_temuan'])) {
                        $temuan->rekomendasis()->create([
                            'kode_rekomendasi_id' => $r['kode_rekomendasi_id'] ?? null,
                            'rekomendasi_temuan'  => $r['rekomendasi_temuan'],
                        ]);
                    }
                }
            }

            // =======================
            // FILE
            // =======================
            $fileIds = $request->input('file_ids', []); // file lama yg tetap dipakai
            $oldFiles = $temuan->files()->get();

            // 1. Hapus file lama yg tidak ada di request
            foreach ($oldFiles as $old) {
                if (!in_array($old->id, $fileIds)) {
                    if (\Storage::disk('public')->exists($old->file_path)) {
                        \Storage::disk('public')->delete($old->file_path);
                    }
                    $old->delete();
                }
            }

            // 2. Ganti file lama dengan file baru jika ada upload replace
            if ($request->hasFile('replace_files')) {
                foreach ($request->file('replace_files') as $id => $newFile) {
                    $old = $temuan->files()->find($id);
                    if ($old) {
                        // hapus file lama fisik
                        if (\Storage::disk('public')->exists($old->file_path)) {
                            \Storage::disk('public')->delete($old->file_path);
                        }

                        // simpan file baru
                        $path = $newFile->store('temuans', 'public');
                        $old->update([
                            'file_path' => $path,
                            'file_name' => $newFile->getClientOriginalName(),
                        ]);
                    }
                }
            }

            // 3. Tambah file baru
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $path = $file->store('temuans', 'public');
                    $temuan->files()->create([
                        'file_path' => $path,
                        'file_name' => $file->getClientOriginalName(),
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('temuan.index')->with('success', 'Temuan berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error update Temuan: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat mengupdate data.');
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
            session()->flash('success', 'Temuan berhasil dihapus.');
            return redirect()->route('temuan.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error hapus Temuan: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menghapus Temuan.');
        }
    }
}
