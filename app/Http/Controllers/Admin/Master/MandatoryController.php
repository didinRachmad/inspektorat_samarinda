<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mandatory\StoreMandatoryRequest;
use App\Http\Requests\Mandatory\UpdateMandatoryRequest;
use App\Models\Mandatory;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;

class MandatoryController extends Controller
{
    public function index()
    {
        return view('master.mandatory.index');
    }

    public function data()
    {
        $activeMenu = currentMenu();

        $query = Mandatory::select('id', 'nama');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('can_edit', fn($row) => Auth::user()->hasMenuPermission($activeMenu->id, 'edit'))
            ->addColumn('can_delete', fn($row) => Auth::user()->hasMenuPermission($activeMenu->id, 'destroy'))
            ->addColumn('edit_url', fn($row) => route('mandatory.edit', $row->id))
            ->addColumn('delete_url', fn($row) => route('mandatory.destroy', $row->id))
            ->make(true);
    }

    public function create()
    {
        return view('master.mandatory.create');
    }

    public function store(StoreMandatoryRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();

            Mandatory::create($data);

            DB::commit();
            return redirect()->route('mandatory.index')
                ->with('success', 'Mandatory berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Store Mandatory Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal menyimpan mandatory.');
        }
    }

    public function edit(Mandatory $mandatory)
    {
        return view('master.mandatory.edit', compact('mandatory'));
    }

    public function update(UpdateMandatoryRequest $request, Mandatory $mandatory)
    {
        if (
            $request->only(['nama']) ==
            $mandatory->only(['nama'])
        ) {
            return redirect()->route('mandatory.index')
                ->with('info', 'Tidak ada perubahan data.');
        }

        DB::beginTransaction();
        try {
            $mandatory->update($request->validated());

            DB::commit();
            return redirect()->route('mandatory.index')
                ->with('success', 'Mandatory berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update Mandatory Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    public function destroy(Mandatory $mandatory)
    {
        DB::beginTransaction();
        try {
            $mandatory->delete();
            DB::commit();
            return redirect()->route('mandatory.index')
                ->with('success', 'Data mandatory berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saat menghapus mandatory: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }

    public function getMandatory(Request $request)
    {
        $query = $request->get('q');
        $perPage = 10;

        $mandatory = Mandatory::where('nama', 'like', '%' . $query . '%')
            ->select('id', 'nama')
            ->paginate($perPage);

        return response()->json($mandatory);
    }
}
