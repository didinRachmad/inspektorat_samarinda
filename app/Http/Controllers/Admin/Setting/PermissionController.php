<?php

namespace App\Http\Controllers\Admin\Setting;

use App\Http\Controllers\Controller;

use App\Models\Permission;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Log;
use Yajra\DataTables\DataTables;

class PermissionController extends Controller
{
    // Menampilkan daftar permissions
    public function index()
    {
        return view('setting.permissions.index');
    }

    public function data()
    {
        $activeMenu = currentMenu(); // helper dari AppServiceProvider
        $query = Permission::select('id', 'name');

        return DataTables::of($query)
            ->addIndexColumn()
            // ->addColumn('nama', fn ($row) => $row->nama)
            ->addColumn('can_edit', fn ($row) => Auth::user()->hasMenuPermission($activeMenu->id, 'edit'))
            ->addColumn('can_delete', fn ($row) => Auth::user()->hasMenuPermission($activeMenu->id, 'destroy'))
            ->addColumn('edit_url', fn ($row) => route('permissions.edit', $row->id))
            ->addColumn('delete_url', fn ($row) => route('permissions.destroy', $row->id))
            ->make(true);
    }

    // Menampilkan form untuk membuat permission baru
    public function create()
    {
        return view('setting.permissions.create');
    }

    // Menyimpan permission baru
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name',
        ]);

        DB::beginTransaction();
        try {
            Permission::create([
                'name' => $request->name,
            ]);
            DB::commit();
            session()->flash('success', 'Data permission berhasil dibuat.');
            return redirect()->route('permissions.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error menyimpan data: ' . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan saat menyimpan data.');
            return redirect()->back()->withInput();
        }
    }

    // Menampilkan form untuk mengedit permission
    public function edit(Permission $permission)
    {
        return view('setting.permissions.edit', compact('permission'));
    }

    // Memperbarui permission
    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name,' . $permission->id,
        ]);

        DB::beginTransaction();
        try {
            $permission->update([
                'name' => $request->name,
            ]);
            DB::commit();
            session()->flash('success', 'Data permission berhasil diperbarui.');
            return redirect()->route('permissions.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error memperbarui data: ' . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan saat memperbarui data.');
            return redirect()->back()->withInput();
        }
    }

    // Menghapus permission
    public function destroy(Permission $permission)
    {
        DB::beginTransaction();
        try {
            $permission->forceDelete();
            DB::commit();
            session()->flash('success', 'Data permission berhasil dihapus.');
            return redirect()->route('permissions.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error menghapus data: ' . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan saat menghapus data.');
            return redirect()->back();
        }
    }
}
