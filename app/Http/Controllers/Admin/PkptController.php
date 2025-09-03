<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pkpt\StorePkptRequest;
use App\Http\Requests\Pkpt\UpdatePkptRequest;
use App\Models\Pkpt;
use App\Models\PkptJabatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;
use Auth;

class PkptController extends Controller
{
    /**
     * Tampilkan halaman index (list PKPT).
     */
    public function index()
    {
        return view('pkpt.index');
    }

    /**
     * Endpoint DataTables (server-side).
     */
    public function data()
    {
        $activeMenu = currentMenu();
        // muat relasi jabatans (jumlah + anggaran) dan auditors (opsional)
        $query = Pkpt::with(['jabatans', 'auditors'])->select('pkpts.*');

        return DataTables::of($query)
            ->addIndexColumn()

            // ambil jumlah per jabatan (simpan sebagai angka)
            ->addColumn('pj', function ($row) {
                return (int) optional($row->jabatans->firstWhere('jabatan', 'PJ'))->jumlah ?? 0;
            })->addColumn('wpj', function ($row) {
                return (int) optional($row->jabatans->firstWhere('jabatan', 'WPJ'))->jumlah ?? 0;
            })->addColumn('pt', function ($row) {
                return (int) optional($row->jabatans->firstWhere('jabatan', 'PT'))->jumlah ?? 0;
            })->addColumn('kt', function ($row) {
                return (int) optional($row->jabatans->firstWhere('jabatan', 'KT'))->jumlah ?? 0;
            })->addColumn('at', function ($row) {
                return (int) optional($row->jabatans->firstWhere('jabatan', 'AT'))->jumlah ?? 0;
            })

            // anggaran_total: pakai kolom summary jika tersedia, kalau tidak hitung dari detail
            ->addColumn('anggaran_total', function ($row) {
                return (int) ($row->anggaran_total ?: $row->jabatans->sum('anggaran'));
            })

            // hak akses & url action
            ->addColumn('can_edit', function ($row) use ($activeMenu) {
                return Auth::user()->hasMenuPermission($activeMenu->id, 'edit');
            })
            ->addColumn('can_delete', function ($row) use ($activeMenu) {
                return Auth::user()->hasMenuPermission($activeMenu->id, 'destroy');
            })
            ->addColumn('edit_url', function ($row) {
                return route('pkpt.edit', $row->id);
            })
            ->addColumn('delete_url', function ($row) {
                return route('pkpt.destroy', $row->id);
            })

            // jika Anda menampilkan nama auditor (opsional), bisa ditambahkan:
            ->addColumn('auditor_names', function ($row) { // kumpulkan nama_manual atau user->name
                $names = $row->auditors->map(function ($a) {
                    return $a->nama_manual ?? ($a->user?->name ?? null);
                })->filter()->values()->all();
                return implode(', ', $names);
            })

            ->make(true);
    }

    /**
     * Form create PKPT.
     */
    public function create()
    {
        return view('pkpt.create');
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
        $pkpt->load('jabatans'); // preload relasi
        return view('pkpt.edit', compact('pkpt'));
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
