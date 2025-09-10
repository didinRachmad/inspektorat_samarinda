<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\KodeRekomendasi;
use Illuminate\Http\Request;
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
        $query = KodeRekomendasi::select('id', 'kode', 'nama_rekomendasi', 'urutan')->orderBy('urutan');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('can_edit', fn($row) => Auth::user()->hasMenuPermission($activeMenu->id, 'edit'))
            ->addColumn('can_delete', fn($row) => Auth::user()->hasMenuPermission($activeMenu->id, 'destroy'))
            ->addColumn('edit_url', fn($row) => route('kode_rekomendasi.edit', $row->id))
            ->addColumn('delete_url', fn($row) => route('kode_rekomendasi.destroy', $row->id))
            ->make(true);
    }

    public function create()
    {
        return view('master.kode_rekomendasi.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|string|max:10|unique:kode_rekomendasis,kode',
            'nama_rekomendasi' => 'required|string|max:255',
            'urutan' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            KodeRekomendasi::create($request->all());
            DB::commit();
            return redirect()->route('kode_rekomendasi.index')->with('success', 'Kode Rekomendasi berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Store Kode Rekomendasi Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal menyimpan Kode Rekomendasi');
        }
    }

    public function edit(KodeRekomendasi $kode_rekomendasi)
    {
        return view('master.kode_rekomendasi.edit', compact('kode_rekomendasi'));
    }

    public function update(Request $request, KodeRekomendasi $kode_rekomendasi)
    {
        $request->validate([
            'kode' => 'required|string|max:10|unique:kode_rekomendasis,kode,' . $kode_rekomendasi->id,
            'nama_rekomendasi' => 'required|string|max:255',
            'urutan' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            $kode_rekomendasi->update($request->all());
            DB::commit();
            return redirect()->route('kode_rekomendasi.index')->with('success', 'Kode Rekomendasi berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update Kode Rekomendasi Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal update Kode Rekomendasi');
        }
    }

    public function destroy(KodeRekomendasi $kode_rekomendasi)
    {
        DB::beginTransaction();
        try {
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
