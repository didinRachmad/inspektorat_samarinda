<?php

namespace App\Http\Controllers\Admin\Setting;

use App\Http\Controllers\Controller;

use App\Models\Menu;
use App\Models\Permission;
use App\Models\Role;
use Auth;
use Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Route;
use Str;
use Yajra\DataTables\DataTables;

class RoleController extends Controller
{
    // Menampilkan daftar roles
    public function index()
    {
        return view('setting.roles.index');
    }

    public function data()
    {
        $activeMenu = currentMenu(); // helper dari AppServiceProvider
        $query = Role::select('id', 'name');

        return DataTables::of($query)
            ->addIndexColumn()
            // ->addColumn('nama', fn ($row) => $row->nama)
            ->addColumn('can_edit_permission', fn($row) => Auth::user()->hasMenuPermission($activeMenu->id, 'edit'))
            ->addColumn('can_edit', fn($row) => Auth::user()->hasMenuPermission($activeMenu->id, 'edit'))
            ->addColumn('can_delete', fn($row) => Auth::user()->hasMenuPermission($activeMenu->id, 'destroy'))
            ->addColumn('edit_permission_url', fn($row) => route('roles.menu-permissions', $row->id))
            ->addColumn('edit_url', fn($row) => route('roles.edit', $row->id))
            ->addColumn('delete_url', fn($row) => route('roles.destroy', $row->id))
            ->make(true);
    }

    // Menampilkan form untuk membuat role baru
    public function create()
    {
        return view('setting.roles.create');
    }

    // Menyimpan role baru
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|unique:roles,name',
        ]);

        DB::beginTransaction();

        try {
            Role::create([
                'name' => $validatedData['name'],
            ]);

            DB::commit();
            session()->flash('success', 'Role berhasil dibuat.');
            return redirect()->route('roles.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saat membuat role: ' . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan saat membuat role. Silakan coba lagi.');
            return redirect()->back()->withInput();
        }
    }

    // Menampilkan form untuk mengedit role
    public function edit(Role $role)
    {
        return view('setting.roles.edit', compact('role'));
    }

    // Memperbarui role
    public function update(Request $request, Role $role)
    {
        $validatedData = $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id,
        ]);

        DB::beginTransaction();

        try {
            $role->update([
                'name' => $validatedData['name'],
            ]);

            DB::commit();
            session()->flash('success', 'Role berhasil diperbarui.');
            return redirect()->route('roles.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saat memperbarui role: ' . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan saat memperbarui role. Silakan coba lagi.');
            return redirect()->back()->withInput();
        }
    }

    // Menghapus role
    public function destroy(Role $role)
    {
        DB::beginTransaction();

        try {
            $role->forceDelete();

            DB::commit();
            session()->flash('success', 'Role berhasil dihapus.');
            return redirect()->route('roles.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saat menghapus role: ' . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan saat menghapus role. Silakan coba lagi.');
            return redirect()->back()->withInput();
        }
    }

    // Menampilkan halaman pengaturan permissions untuk role tertentu
    public function menuPermissions(Role $role)
    {
        // 1) Ambil semua menu (urut berdasarkan order)
        $menus = Menu::orderBy('order')->get();

        // 2) Ambil semua permission per menu untuk role ini
        //    hasil: [ menu_id => [permission_id, ...], ... ]
        $roleMenuPermissions = DB::table('role_has_permissions')
            ->where('role_id', $role->id)
            ->select('menu_id', 'permission_id')
            ->get()
            ->groupBy('menu_id')
            ->map(fn($group) => $group->pluck('permission_id')->toArray())
            ->toArray();

        // 3) Semua daftar possible permissions (misal: index, create, update, delete)
        $permissions = Permission::all();

        return view('setting.roles.menu-permissions', compact(
            'role',
            'menus',
            'roleMenuPermissions',
            'permissions'
        ));
    }

    public function assignMenuPermissions(Request $request, Role $role)
    {
        // 1) validasi input
        $request->validate([
            'menu_permissions'     => 'array',
            'menu_permissions.*'   => 'array',
            'menu_permissions.*.*' => 'exists:permissions,id',
        ]);

        // 2) mulai transaksi manual
        DB::beginTransaction();

        try {
            // detach semua relasi lama
            $role->menus()->detach();

            // attach ulang dengan permission_id di pivot
            foreach ($request->menu_permissions as $menuId => $permissionIds) {
                foreach ($permissionIds as $permissionId) {
                    $role->menus()->attach($menuId, [
                        'permission_id' => $permissionId,
                    ]);
                }
            }

            Cache::forget('menu_tree_role_' . $role->id);
            Cache::forget('has_profile_access_role_' . $role->id);

            // commit transaksi
            DB::commit();

            // redirect sukses
            return redirect()
                ->route('roles.index')
                ->with('success', "Menu permissions untuk role “{$role->name}” berhasil diperbarui.");
        } catch (\Exception $e) {
            // rollback jika gagal
            DB::rollBack();
            \Log::error('Gagal assign menu permissions: ' . $e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan permission. Silakan coba lagi.');
        }
    }

    public function getRoles(Request $request)
    {
        $search = $request->input('q', '');

        $roles = Role::select('id', 'name')
            ->when($search, fn($q) => $q->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->get();

        return response()->json($roles);
    }
}
