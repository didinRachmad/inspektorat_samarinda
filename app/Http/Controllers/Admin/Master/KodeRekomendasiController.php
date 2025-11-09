<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\KodeRekomendasi\StoreKodeRekomendasiRequest;
use App\Http\Requests\KodeRekomendasi\UpdateKodeRekomendasiRequest;
use App\Models\KodeRekomendasi;
use App\Models\KodeTemuan;
use Yajra\DataTables\DataTables;
use Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KodeRekomendasiController extends Controller
{
    public function index()
    {
        return view('master.kode_rekomendasi.index');
    }

    public function data()
    {
        $activeMenu = currentMenu();

        $query = KodeRekomendasi::query()
            ->leftJoin('kode_temuan_rekomendasi as pivot', 'kode_rekomendasis.id', '=', 'pivot.kode_rekomendasi_id')
            ->leftJoin('kode_temuans', 'pivot.kode_temuan_id', '=', 'kode_temuans.id')
            ->select([
                'kode_rekomendasis.id',
                'kode_rekomendasis.kode',
                'kode_rekomendasis.nama_rekomendasi',
                'kode_rekomendasis.urutan',
                DB::raw("GROUP_CONCAT(CONCAT(kode_temuans.kode, ' - ', kode_temuans.nama_temuan) SEPARATOR ' | ') as kode_temuan_list")
            ])
            ->groupBy('kode_rekomendasis.id');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('can_edit', fn($row) => Auth::user()->hasMenuPermission($activeMenu->id, 'edit'))
            ->addColumn('can_delete', fn($row) => Auth::user()->hasMenuPermission($activeMenu->id, 'destroy'))
            ->addColumn('edit_url', fn($row) => route('kode_rekomendasi.edit', $row->id))
            ->addColumn('delete_url', fn($row) => route('kode_rekomendasi.destroy', $row->id))
            ->rawColumns(['kode_temuan'])
            ->make(true);
    }

    public function create()
    {
        $temuans = KodeTemuan::orderBy('kode')->get()->mapWithKeys(function ($item) {
            return [$item->id => ['kode' => $item->kode, 'nama_temuan' => $item->nama_temuan]];
        });
        return view('master.kode_rekomendasi.create', compact('temuans'));
    }

    public function store(StoreKodeRekomendasiRequest $request)
    {
        DB::beginTransaction();
        try {
            // Buat rekomendasi baru
            $rekomendasi = KodeRekomendasi::create($request->validated());

            // Attach ke temuan terkait melalui pivot
            if ($request->has('temuan_ids')) {
                $rekomendasi->kodeTemuans()->attach($request->temuan_ids);
            }

            DB::commit();

            return redirect()
                ->route('kode_rekomendasi.index')
                ->with('success', 'Kode Rekomendasi berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Store Kode Rekomendasi Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal menyimpan Kode Rekomendasi');
        }
    }

    public function edit(KodeRekomendasi $kode_rekomendasi)
    {
        $temuans = KodeTemuan::orderBy('kode')->get()->mapWithKeys(function ($item) {
            return [$item->id => ['kode' => $item->kode, 'nama_temuan' => $item->nama_temuan]];
        });

        $selectedTemuans = $kode_rekomendasi->kodeTemuans()->pluck('kode_temuans.id')->toArray();

        return view('master.kode_rekomendasi.edit', compact(
            'kode_rekomendasi',
            'temuans',
            'selectedTemuans'
        ));
    }

    public function update(UpdateKodeRekomendasiRequest $request, KodeRekomendasi $kode_rekomendasi)
    {
        DB::beginTransaction();
        try {
            $kode_rekomendasi->update($request->validated());

            $kode_rekomendasi->kodeTemuans()->sync($request->temuan_ids ?? []);

            DB::commit();

            return redirect()
                ->route('kode_rekomendasi.index')
                ->with('success', 'Kode Rekomendasi berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update Kode Rekomendasi Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui Kode Rekomendasi');
        }
    }

    public function destroy(KodeRekomendasi $kode_rekomendasi)
    {
        DB::beginTransaction();
        try {
            // Lepas relasi pivot sebelum delete
            $kode_rekomendasi->temuans()->detach();
            $kode_rekomendasi->delete();

            DB::commit();
            return redirect()->route('kode_rekomendasi.index')->with('success', 'Data berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Delete Kode Rekomendasi Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus data!');
        }
    }
}
