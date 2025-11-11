<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Http\Requests\Regulasi\StoreRegulasiRequest;
use App\Http\Requests\Regulasi\UpdateRegulasiRequest;
use App\Models\Regulasi;
use Auth;
use DB;
use Illuminate\Http\Request;
use Log;
use Storage;
use Yajra\DataTables\Facades\DataTables;

class RegulasiController extends Controller
{
    public function index()
    {
        return view('regulasi');
    }

    public function data()
    {
        $activeMenu = currentMenu();

        $query = Regulasi::select('id', 'title', 'description', 'file_path');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('can_edit', fn($row) => Auth::user()->hasMenuPermission($activeMenu->id, 'edit'))
            ->addColumn('can_delete', fn($row) => Auth::user()->hasMenuPermission($activeMenu->id, 'destroy'))
            ->addColumn('delete_url', fn($row) => route('regulasi.destroy', $row->id))
            ->addColumn('download_url', fn($row) => route('regulasi.download', $row->id))
            ->make(true);
    }

    public function create() {}

    public function store(StoreRegulasiRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();

            if ($request->hasFile('file')) {
                $data['file_path'] = $request->file('file')->store('public/regulasi');
            }

            Regulasi::create($data);

            DB::commit();
            return redirect()->route('regulasi.index')->with('success', 'Regulasi berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Store Regulasi Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal menyimpan regulasi.');
        }
    }

    public function edit(Regulasi $regulasi) {}

    public function update(UpdateRegulasiRequest $request, Regulasi $regulasi)
    {
        if ($request->only(['title', 'description']) == $regulasi->only(['title', 'description']) && !$request->hasFile('file')) {
            return redirect()->route('regulasi.index')->with('info', 'Tidak ada perubahan data.');
        }

        DB::beginTransaction();
        try {
            $data = $request->validated();

            if ($request->hasFile('file')) {
                Storage::delete($regulasi->file_path);
                $data['file_path'] = $request->file('file')->store('public/regulasi');
            }

            $regulasi->update($data);

            DB::commit();
            return redirect()->route('regulasi.index')->with('success', 'Regulasi berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update Regulasi Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    public function destroy(Regulasi $regulasi)
    {
        DB::beginTransaction();
        try {
            Storage::delete($regulasi->file_path);
            $regulasi->delete();
            DB::commit();
            return redirect()->route('regulasi.index')->with('success', 'Regulasi berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Delete Regulasi Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }

    public function download(Regulasi $regulasi)
    {
        if (Storage::exists($regulasi->file_path)) {
            return Storage::download($regulasi->file_path);
        }
        return back()->with('error', 'File tidak ditemukan.');
    }

    public function getRegulasi(Request $request)
    {
        $query = $request->get('q');
        $perPage = 10;

        $regulasi = Regulasi::where('title', 'like', '%' . $query . '%')
            ->select('id', 'title')
            ->paginate($perPage);

        return response()->json($regulasi);
    }
}
