<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\Irbanwil\StoreIrbanwilRequest;
use App\Http\Requests\Irbanwil\UpdateIrbanwilRequest;
use App\Models\Irbanwil;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;

class IrbanwilController extends Controller
{
    public function index()
    {
        return view('master.irbanwil.index');
    }

    public function data()
    {
        $activeMenu = currentMenu();

        $query = Irbanwil::select('id', 'nama');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('can_edit', fn($row) => Auth::user()->hasMenuPermission($activeMenu->id, 'edit'))
            ->addColumn('can_delete', fn($row) => Auth::user()->hasMenuPermission($activeMenu->id, 'destroy'))
            ->addColumn('edit_url', fn($row) => route('irbanwil.edit', $row->id))
            ->addColumn('delete_url', fn($row) => route('irbanwil.destroy', $row->id))
            ->make(true);
    }

    public function create()
    {
        return view('master.irbanwil.create');
    }

    public function store(StoreIrbanwilRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();

            Irbanwil::create($data);

            DB::commit();
            return redirect()->route('irbanwil.index')
                ->with('success', 'Irbanwil berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Store Irbanwil Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal menyimpan irbanwil.');
        }
    }

    public function edit(Irbanwil $irbanwil)
    {
        return view('master.irbanwil.edit', compact('irbanwil'));
    }

    public function update(UpdateIrbanwilRequest $request, Irbanwil $irbanwil)
    {
        if (
            $request->only(['nama']) ==
            $irbanwil->only(['nama'])
        ) {
            return redirect()->route('irbanwil.index')
                ->with('info', 'Tidak ada perubahan data.');
        }

        DB::beginTransaction();
        try {
            $irbanwil->update($request->validated());

            DB::commit();
            return redirect()->route('irbanwil.index')
                ->with('success', 'Irbanwil berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update Irbanwil Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    public function destroy(Irbanwil $irbanwil)
    {
        DB::beginTransaction();
        try {
            $irbanwil->delete();
            DB::commit();
            return redirect()->route('irbanwil.index')
                ->with('success', 'Data irbanwil berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saat menghapus irbanwil: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }

    public function getIrbanwil(Request $request)
    {
        $query = $request->get('q');
        $perPage = 10;

        $irbanwil = Irbanwil::where('nama', 'like', '%' . $query . '%')
            ->select('id', 'nama')
            ->paginate($perPage);

        return response()->json($irbanwil);
    }
}
