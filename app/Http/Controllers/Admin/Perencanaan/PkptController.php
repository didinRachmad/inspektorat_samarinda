<?php

namespace App\Http\Controllers\Admin\Perencanaan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pkpt\StorePkptRequest;
use App\Http\Requests\Pkpt\UpdatePkptRequest;
use App\Models\Auditi;
use App\Models\Pkpt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;
use Auth;
use Carbon\Carbon;

class PkptController extends Controller
{
    /**
     * Tampilkan halaman index (list PKPT).
     */
    public function index()
    {
        return view('perencanaan.pkpt.index');
    }

    /**
     * Endpoint DataTables (server-side).
     */
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
            'auditis.nama_auditi',
            'pkpts.ruang_lingkup',
            'pkpts.sasaran',
            'pkpts.anggaran_total',
            'pkpts.jadwal_rmp_bulan',
            'pkpts.jadwal_rsp_bulan',
            'pkpts.jadwal_rpl_bulan',
            'pkpts.jadwal_hp_hari',
            'jabatans.pj',
            'jabatans.wpj',
            'jabatans.pt',
            'jabatans.kt',
            'jabatans.at',
            'jabatans.anggaran_total_calc',
        ])
            ->leftJoin('auditis', 'auditis.id', '=', 'pkpts.auditi_id')
            ->leftJoinSub($subJabatans, 'jabatans', function ($join) {
                $join->on('pkpts.id', '=', 'jabatans.pkpt_id');
            });

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('bulan', function ($row) {
                return $row->bulan
                    ? Carbon::create()->month($row->bulan)->translatedFormat('F')
                    : '-';
            })
            ->editColumn('jadwal_rmp_bulan', function ($row) {
                return $row->jadwal_rmp_bulan
                    ? Carbon::create()->month($row->jadwal_rmp_bulan)->translatedFormat('F')
                    : '-';
            })
            ->editColumn('jadwal_rsp_bulan', function ($row) {
                return $row->jadwal_rsp_bulan
                    ? Carbon::create()->month($row->jadwal_rsp_bulan)->translatedFormat('F')
                    : '-';
            })
            ->editColumn('jadwal_rpl_bulan', function ($row) {
                return $row->jadwal_rpl_bulan
                    ? Carbon::create()->month($row->jadwal_rpl_bulan)->translatedFormat('F')
                    : '-';
            })
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

    /**
     * Form create PKPT.
     */
    public function create()
    {
        $auditis = Auditi::orderBy('nama_auditi')->get();
        return view('perencanaan.pkpt.create', compact('auditis'));
    }

    /**
     * Simpan PKPT baru.
     */
    public function store(StorePkptRequest $request)
    {
        DB::beginTransaction();
        try {
            // Ambil bulan & tahun dari input, fallback ke sekarang
            $tahun = $request->input('tahun', date('Y'));
            $bulan = $request->input('bulan', date('m'));

            // Ambil urutan terakhir di bulan & tahun ini
            $lastPkpt = Pkpt::whereYear('created_at', $tahun)
                ->whereMonth('created_at', $bulan)
                ->orderBy('id', 'desc')
                ->first();

            $urutan = $lastPkpt ? $lastPkpt->id + 1 : 1;
            $no_pkpt = sprintf("PKPT-%02d-%d-%02d", $bulan, $tahun, $urutan);

            // Gabungkan ke validated data
            $data = $request->validated();
            $data['no_pkpt'] = $no_pkpt;

            // Simpan PKPT
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

    /**
     * Form edit PKPT.
     */
    public function edit(Pkpt $pkpt)
    {
        $pkpt->load('jabatans', 'auditi');
        $auditis = Auditi::orderBy('nama_auditi')->get();

        return view('perencanaan.pkpt.edit', compact('pkpt', 'auditis'));
    }

    /**
     * Update PKPT.
     */
    public function update(UpdatePkptRequest $request, Pkpt $pkpt)
    {
        DB::beginTransaction();
        try {
            $pkpt->update($request->validated());

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

    /**
     * Hapus PKPT.
     */
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
