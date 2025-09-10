<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\KodeTemuan\StoreKodeTemuanRequest;
use App\Http\Requests\KodeTemuan\UpdateKodeTemuanRequest;
use App\Models\KodeTemuan;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;

class KodeTemuanController extends Controller
{
    public function index()
    {
        return view('master.kode_temuan.index');
    }

    public function data()
    {
        $activeMenu = currentMenu();

        $query = KodeTemuan::select('id', 'kode', 'nama_temuan', 'parent_id', 'level', 'urutan')
            ->orderBy('kode');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('can_edit', fn($row) => Auth::user()->hasMenuPermission($activeMenu->id, 'edit'))
            ->addColumn('can_delete', fn($row) => Auth::user()->hasMenuPermission($activeMenu->id, 'destroy'))
            ->addColumn('edit_url', fn($row) => route('kode_temuan.edit', $row->id))
            ->addColumn('delete_url', fn($row) => route('kode_temuan.destroy', $row->id))
            ->make(true);
    }

    public function create()
    {
        $parentOptions = KodeTemuan::whereNull('parent_id')->orderBy('urutan')->get();
        return view('master.kode_temuan.create', compact('parentOptions'));
    }

    public function store(StoreKodeTemuanRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();

            KodeTemuan::create($data);

            DB::commit();
            return redirect()->route('kode_temuan.index')
                ->with('success', 'Kode Temuan berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Store Kode Temuan Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal menyimpan Kode Temuan');
        }
    }

    public function edit(KodeTemuan $kode_temuan)
    {
        $parentOptions = KodeTemuan::whereNull('parent_id')
            ->where('id', '!=', $kode_temuan->id)
            ->orderBy('urutan')
            ->get();
        return view('master.kode_temuan.edit', compact('kode_temuan', 'parentOptions'));
    }

    public function update(UpdateKodeTemuanRequest $request, KodeTemuan $kode_temuan)
    {
        if (
            $request->only(['kode', 'nama_temuan', 'parent_id', 'level', 'urutan']) ==
            $kode_temuan->only(['kode', 'nama_temuan', 'parent_id', 'level', 'urutan'])
        ) {
            return redirect()->route('kode_temuan.index')
                ->with('info', 'Tidak ada perubahan data.');
        }

        DB::beginTransaction();
        try {
            $kode_temuan->update($request->validated());

            DB::commit();
            return redirect()->route('kode_temuan.index')
                ->with('success', 'Kode Temuan berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update Kode Temuan Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    public function destroy(KodeTemuan $kode_temuan)
    {
        DB::beginTransaction();
        try {
            $kode_temuan->delete();
            DB::commit();
            return redirect()->route('kode_temuan.index')
                ->with('success', 'Data Kode Temuan berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saat menghapus Kode Temuan: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }

    public function getKodeTemuan(Request $request)
    {
        $query = $request->get('q');
        $perPage = 10;

        $kode_temuan = KodeTemuan::where('nama_temuan', 'like', '%' . $query . '%')
            ->select('id', 'kode', 'nama_temuan', 'parent_id', 'level')
            ->paginate($perPage);

        return response()->json($kode_temuan);
    }
}
