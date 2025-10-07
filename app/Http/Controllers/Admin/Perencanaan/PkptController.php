<?php

namespace App\Http\Controllers\Admin\Perencanaan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pkpt\StorePkptRequest;
use App\Http\Requests\Pkpt\UpdatePkptRequest;
use App\Models\Auditi;
use App\Models\Mandatory;
use App\Models\Pkpt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class PkptController extends Controller
{
    public function index()
    {
        return view('perencanaan.pkpt.index');
    }

    public function data()
    {
        $activeMenu = currentMenu();

        // Subquery untuk jumlah jabatan
        $subJabatans = DB::table('pkpt_jabatans')
            ->selectRaw('
            pkpt_id,
            SUM(CASE WHEN jabatan = "PJ" THEN jumlah ELSE 0 END) as pj,
            SUM(CASE WHEN jabatan = "WPJ" THEN jumlah ELSE 0 END) as wpj,
            SUM(CASE WHEN jabatan = "PT" THEN jumlah ELSE 0 END) as pt,
            SUM(CASE WHEN jabatan = "KT" THEN jumlah ELSE 0 END) as kt,
            SUM(CASE WHEN jabatan = "AT" THEN jumlah ELSE 0 END) as at,
            SUM(anggaran) as anggaran_total_calc
        ')
            ->groupBy('pkpt_id');

        $query = Pkpt::select([
            'pkpts.id',
            'pkpts.tahun',
            'pkpts.bulan',
            'pkpts.no_pkpt',
            'mandatories.nama',
            'auditis.nama_auditi',
            'pkpts.ruang_lingkup',
            'pkpts.sasaran',
            'pkpts.jenis_pengawasan',
            'pkpts.anggaran_total',
            'pkpts.jadwal_rmp_bulan',
            'pkpts.jadwal_rsp_bulan',
            'pkpts.jadwal_rpl_bulan',
            'pkpts.jadwal_hp_hari',
            'irbanwils.nama as nama_irbanwil',
            'jabatans.pj',
            'jabatans.wpj',
            'jabatans.pt',
            'jabatans.kt',
            'jabatans.at',
            'jabatans.anggaran_total_calc',
        ])
            ->leftJoin('mandatories', 'mandatories.id', '=', 'pkpts.mandatory_id')
            ->leftJoin('auditis', 'auditis.id', '=', 'pkpts.auditi_id')
            ->leftJoin('irbanwils', 'irbanwils.id', '=', 'auditis.irbanwil_id') // ✅ lewat auditis
            ->leftJoinSub($subJabatans, 'jabatans', function ($join) {
                $join->on('pkpts.id', '=', 'jabatans.pkpt_id');
            })
            ->where('pkpts.pkpt', 1);

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn(
                'bulan',
                fn($row) =>
                $row->bulan
                    ? Carbon::create()->month($row->bulan)->translatedFormat('F')
                    : '-'
            )
            ->editColumn(
                'jadwal_rmp_bulan',
                fn($row) =>
                $row->jadwal_rmp_bulan
                    ? Carbon::create()->month($row->jadwal_rmp_bulan)->translatedFormat('F')
                    : '-'
            )
            ->editColumn(
                'jadwal_rsp_bulan',
                fn($row) =>
                $row->jadwal_rsp_bulan
                    ? Carbon::create()->month($row->jadwal_rsp_bulan)->translatedFormat('F')
                    : '-'
            )
            ->editColumn(
                'jadwal_rpl_bulan',
                fn($row) =>
                $row->jadwal_rpl_bulan
                    ? Carbon::create()->month($row->jadwal_rpl_bulan)->translatedFormat('F')
                    : '-'
            )
            ->addColumn('anggaran_total', function ($row) {
                // gunakan anggaran_total summary, fallback ke hasil kalkulasi
                return (int) ($row->anggaran_total ?: $row->anggaran_total_calc);
            })
            ->addColumn(
                'can_edit',
                fn($row) =>
                Auth::user()->hasMenuPermission($activeMenu->id, 'edit')
            )
            ->addColumn(
                'can_delete',
                fn($row) =>
                Auth::user()->hasMenuPermission($activeMenu->id, 'destroy')
            )
            ->addColumn(
                'edit_url',
                fn($row) =>
                route('pkpt.edit', $row->id)
            )
            ->addColumn(
                'delete_url',
                fn($row) =>
                route('pkpt.destroy', $row->id)
            )
            ->make(true);
    }

    public function create()
    {
        $auditis = Auditi::orderBy('nama_auditi')->get();
        $mandatories = Mandatory::orderBy('nama')->get();
        return view('perencanaan.pkpt.create', compact('auditis', 'mandatories'));
    }

    public function store(StorePkptRequest $request)
    {
        DB::beginTransaction();
        try {
            $tahun = $request->input('tahun', date('Y'));
            $bulan = $request->input('bulan', date('m'));

            // Ambil urutan terakhir di bulan & tahun ini
            $lastPkpt = Pkpt::whereYear('created_at', $tahun)
                ->whereMonth('created_at', $bulan)
                ->where('pkpt', 1)
                ->orderBy('id', 'desc')
                ->first();

            $urutan = $lastPkpt ? $lastPkpt->id + 1 : 1;
            $no_pkpt = sprintf("PKPT-%02d-%d-%02d", $bulan, $tahun, $urutan);

            // Gabungkan data
            $data = $request->validated();
            $data['no_pkpt'] = $no_pkpt;
            $data['pkpt'] = 1;

            // Upload file
            if ($request->hasFile('file_surat_tugas')) {
                $file = $request->file('file_surat_tugas');
                $filename = time() . '_' . $file->getClientOriginalName();
                $data['file_surat_tugas'] = $file->storeAs('pkpt/surat_tugas', $filename, 'public');
            }

            $pkpt = Pkpt::create($data);

            // Simpan jabatans (nested array)
            foreach ($request->jabatans as $jabatan) {
                $pkpt->jabatans()->create($jabatan);
            }

            DB::commit();
            session()->flash('success', 'Data PKPT berhasil dibuat.');
            return redirect()->route('pkpt.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error simpan PKPT: ' . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan saat menyimpan data.');
            return redirect()->back()->withInput();
        }
    }

    public function edit(Pkpt $pkpt)
    {
        $pkpt->load('jabatans', 'auditi');
        $auditis = Auditi::orderBy('nama_auditi')->get();
        $mandatories = Mandatory::orderBy('nama')->get();

        return view('perencanaan.pkpt.edit', compact('pkpt', 'auditis', 'mandatories'));
    }

    public function update(UpdatePkptRequest $request, Pkpt $pkpt)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();

            // Jika ada file baru, hapus lama lalu simpan baru
            if ($request->hasFile('file_surat_tugas')) {
                if ($pkpt->file_surat_tugas) {
                    Storage::disk('public')->delete($pkpt->file_surat_tugas);
                }
                $data['file_surat_tugas'] = $request->file('file_surat_tugas')->store('pkpt/surat tugas', 'public');
            }

            $pkpt->update($data);

            // Hapus jabatans lama lalu insert ulang (sederhana)
            $pkpt->jabatans()->delete();
            foreach ($request->jabatans as $jabatan) {
                $pkpt->jabatans()->create($jabatan);
            }

            DB::commit();
            session()->flash('success', 'Data PKPT berhasil diperbarui.');
            return redirect()->route('pkpt.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error update PKPT: ' . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan saat memperbarui data.');
            return redirect()->back()->withInput();
        }
    }

    public function destroy(Pkpt $pkpt)
    {
        DB::beginTransaction();
        try {
            $pkpt->jabatans()->delete();
            $pkpt->delete();

            DB::commit();
            session()->flash('success', 'Data PKPT berhasil dihapus.');
            return redirect()->route('pkpt.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error hapus PKPT: ' . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan saat menghapus data.');
            return redirect()->back();
        }
    }
}
