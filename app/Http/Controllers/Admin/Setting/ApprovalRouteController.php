<?php

namespace App\Http\Controllers\Admin\Setting;

use App\Http\Controllers\Controller;

use App\Models\ApprovalRoute;
use App\Models\Menu;
use App\Models\Role;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Log;
use Yajra\DataTables\DataTables;

class ApprovalRouteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('setting.approval_routes.index');
    }

    public function data()
    {
        $activeMenu = currentMenu(); // helper dari AppServiceProvider
        $query = ApprovalRoute::select(
            'approval_routes.id as id',
            'approval_routes.module as module',
            'approval_routes.sequence as sequence',
            'roles.name as role',
            'users.email as assigned_user'
        )
            ->leftJoin('roles', 'roles.id', '=', 'approval_routes.role_id')
            ->leftJoin('users', 'users.id', '=', 'approval_routes.assigned_user_id');

        return DataTables::of($query)
            ->addIndexColumn()
            // ->addColumn('nama', fn ($row) => $row->nama)
            ->addColumn('can_edit', fn ($row) => Auth::user()->hasMenuPermission($activeMenu->id, 'edit'))
            ->addColumn('can_delete', fn ($row) => Auth::user()->hasMenuPermission($activeMenu->id, 'destroy'))
            ->addColumn('edit_url', fn ($row) => route('approval_routes.edit', $row->id))
            ->addColumn('delete_url', fn ($row) => route('approval_routes.destroy', $row->id))
            ->make(true);
    }

    public function create()
    {
        $menus = Menu::whereNotNull('route')->orderBy('order')->get();
        $roles = Role::all();
        return view('setting.approval_routes.create', compact('roles', 'menus'));
    }

    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'module' => 'required|string|max:255',
            'role_id' => 'required|exists:roles,id',
            'sequence' => 'required|integer|min:1',
            'assigned_user_id' => 'nullable|exists:users,id',
        ]);

        try {
            DB::beginTransaction();

            ApprovalRoute::create($validated);

            DB::commit();
            session()->flash('success', 'Konfigurasi approval berhasil ditambahkan.');
            return redirect()->route('approval_routes.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error menyimpan data: ' . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ApprovalRoute $approval_route)
    {
        $menus = Menu::whereNotNull('route')->orderBy('order')->get();
        $roles = Role::all();
        return view('setting.approval_routes.edit', compact('approval_route', 'roles', 'menus'));
    }

    public function update(Request $request, ApprovalRoute $approval_route)
    {
        $validated = $request->validate([
            'module' => 'required|string|max:255',
            'role_id' => 'required|exists:roles,id',
            'sequence' => 'required|integer|min:1',
            'assigned_user_id' => 'nullable|exists:users,id',
        ]);

        try {
            DB::beginTransaction();

            $approval_route->update($validated);

            DB::commit();
            session()->flash('success', 'Konfigurasi approval berhasil diperbarui.');
            return redirect()->route('approval_routes.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error memperbarui data: ' . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ApprovalRoute $approval_route)
    {
        try {
            DB::beginTransaction();

            $approval_route->forceDelete();

            DB::commit();
            session()->flash('success', 'Konfigurasi approval berhasil dihapus.');
            return redirect()->route('approval_routes.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error menghapus data: ' . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
            return redirect()->back();
        }
    }
}
