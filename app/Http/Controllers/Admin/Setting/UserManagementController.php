<?php

namespace App\Http\Controllers\Admin\Setting;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Models\Irbanwil;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;

class UserManagementController extends Controller
{
    public function index()
    {
        return view('setting.users.index');
    }

    public function data()
    {
        $activeMenu = currentMenu();

        $sub = DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->select('model_has_roles.model_id', DB::raw("GROUP_CONCAT(roles.name SEPARATOR ', ') as roles_text"))
            ->groupBy('model_has_roles.model_id');

        $query = User::query()
            ->select('users.id', 'users.name', 'users.email', 'role_summary.roles_text', 'irbanwils.nama as nama')
            ->leftJoinSub($sub, 'role_summary', function ($join) {
                $join->on('users.id', '=', 'role_summary.model_id');
            })
            ->leftJoin('irbanwils', 'users.irbanwil_id', '=', 'irbanwils.id');

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('roles', fn($row) => $row->roles_text)
            ->editColumn('irbanwil', fn($row) => $row->nama ?? '-')
            ->filterColumn('roles', function ($query, $keyword) {
                $query->whereRaw("role_summary.roles_text LIKE ?", ["%{$keyword}%"]);
            })
            ->orderColumn('roles', function ($query, $order) {
                $query->orderByRaw("role_summary.roles_text $order");
            })
            ->addColumn('can_reset_password', fn($row) => Auth::user()->hasMenuPermission($activeMenu->id, 'edit'))
            ->addColumn('can_edit', fn($row) => Auth::user()->hasMenuPermission($activeMenu->id, 'edit'))
            ->addColumn('can_delete', fn($row) => Auth::user()->hasMenuPermission($activeMenu->id, 'destroy'))
            ->addColumn('reset_password_url', fn($row) => route('users.reset-password', $row->id))
            ->addColumn('edit_url', fn($row) => route('users.edit', $row->id))
            ->addColumn('delete_url', fn($row) => route('users.destroy', $row->id))
            ->make(true);
    }

    public function create()
    {
        $roles = Role::all();
        $irbanwils = Irbanwil::all();
        return view('setting.users.create', compact('roles', 'irbanwils'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|max:255|unique:users',
            'password'     => 'required|string|min:8|confirmed',
            'role_id'      => 'required|exists:roles,id',
            'irbanwil_id'  => 'nullable|exists:irbanwils,id',
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'name'        => $validated['name'],
                'email'       => $validated['email'],
                'password'    => bcrypt($validated['password']),
                'irbanwil_id' => $validated['irbanwil_id'] ?? null,
            ]);

            $role = Role::find($validated['role_id']);
            $user->assignRole($role);

            DB::commit();
            session()->flash('success', 'User baru berhasil ditambahkan.');
            return redirect()->route('users.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saat membuat users baru: ' . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan saat membuat user baru. Silakan coba lagi.');
            return redirect()->back()->withInput();
        }
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $irbanwils = Irbanwil::all();
        return view('setting.users.edit', compact('user', 'roles', 'irbanwils'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'        => 'required|string',
            'email'       => 'required|email|unique:users,email,' . $user->id,
            'role_id'     => 'required|exists:roles,id',
            'irbanwil_id' => 'nullable|exists:irbanwils,id',
        ]);

        DB::beginTransaction();
        try {
            $user->update([
                'name'        => $validated['name'],
                'email'       => $validated['email'],
                'irbanwil_id' => $validated['irbanwil_id'] ?? null,
            ]);

            $role = Role::find($validated['role_id']);
            $user->syncRoles($role);

            DB::commit();
            session()->flash('success', 'Data user berhasil diperbarui.');

            // 👉 Cek apakah user yang sedang diupdate adalah user yang login
            if (Auth::id() === $user->id) {
                return redirect()->route('dashboard'); // arahkan ke dashboard
            }

            return redirect()->route('users.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saat memperbarui users: ' . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan saat memperbarui data. Silakan coba lagi.');
            return redirect()->back()->withInput();
        }
    }

    public function resetPassword(User $user)
    {
        $defaultPassword = '12345678';

        DB::beginTransaction();
        try {
            $user->update([
                'password' => Hash::make($defaultPassword),
            ]);

            Log::info("Password untuk user {$user->email} telah direset oleh " . Auth::user()->email);
            DB::commit();
            session()->flash('success', 'Password user telah direset ke default.');
            return redirect()->route('users.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saat reset password users: ' . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan saat reset password. Silakan coba lagi.');
            return redirect()->back()->withInput();
        }
    }

    public function destroy(User $user)
    {
        if (auth()->id() == $user->id) {
            return redirect()->route('users.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        DB::beginTransaction();
        try {
            $user->forceDelete();
            DB::commit();
            session()->flash('success', 'Data user berhasil dinonaktifkan.');
            return redirect()->route('users.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saat menghapus user: ' . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan saat menghapus data. Silakan coba lagi.');
            return redirect()->back()->withInput();
        }
    }

    public function getUsersByRole($roleId)
    {
        $users = User::whereHas('roles', function ($query) use ($roleId) {
            $query->where('id', $roleId);
        })->get(['id', 'email']);

        return response()->json([
            'data' => $users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'text' => $user->email
                ];
            })
        ]);
    }
}
