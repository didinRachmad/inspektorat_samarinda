<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use App\Models\Menu;

class AdminRoleSeeder extends Seeder
{
    public function run(): void
    {
        // Bersihkan cache permission Spatie
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // --- Pastikan semua permission ada ---
        $permissionNames = [
            'index',
            'show',
            'create',
            'store',
            'edit',
            'update',
            'destroy',
            'approve',
            'print',
        ];

        foreach ($permissionNames as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }
        $permissions = Permission::all();

        // --- Buat role ---
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $auditor    = Role::firstOrCreate(['name' => 'auditor', 'guard_name' => 'web']);
        $auditi     = Role::firstOrCreate(['name' => 'auditi', 'guard_name' => 'web']);
        $approver     = Role::firstOrCreate(['name' => 'approver', 'guard_name' => 'web']);

        $menus = Menu::all()->keyBy('route');

        // --- Super Admin: semua menu ---
        foreach ($menus as $menu) {
            foreach ($permissions as $permission) {
                // Batasi permission hanya untuk menu 'kka'
                if ($menu->route == 'kka' && !in_array($permission->name, ['create', 'store', 'destroy'])) {
                    continue;
                }

                DB::table('role_has_permissions')->insertOrIgnore([
                    'role_id'       => $superAdmin->id,
                    'permission_id' => $permission->id,
                    'menu_id'       => $menu->id,
                ]);
            }
        }

        // --- Auditor: hanya menu lhp, kka, tindak_lanjut_temuan ---
        $auditorMenus = ['lhp', 'kka', 'tindak_lanjut_temuan', 'regulasi', 'faq'];
        foreach ($auditorMenus as $menuName) {
            $menu = $menus[$menuName];
            foreach ($permissions as $permission) {
                // Jika menu 'kka', maka filter permission hanya create, store, destroy
                if ($menuName == 'kka' && !in_array($permission->name, ['create', 'store', 'destroy'])) {
                    continue;
                }
                if ($menuName == 'regulasi' && !in_array($permission->name, ['index', 'show'])) {
                    continue;
                }
                if ($menuName == 'faq' && !in_array($permission->name, ['index', 'show'])) {
                    continue;
                }
                DB::table('role_has_permissions')->insertOrIgnore([
                    'role_id'       => $auditor->id,
                    'permission_id' => $permission->id,
                    'menu_id'       => $menu->id,
                ]);
            }
        }

        // --- Auditi: hanya menu tindak_lanjut_temuan ---
        $auditiMenus = ['tindak_lanjut_temuan', 'regulasi', 'faq'];
        foreach ($auditiMenus as $menuName) {
            if ($menuName == 'regulasi' && !in_array($permission->name, ['index', 'show'])) {
                continue;
            }
            if ($menuName == 'faq' && !in_array($permission->name, ['index', 'show'])) {
                continue;
            }
            if (isset($menus[$menuName])) {
                $menu = $menus[$menuName];
                foreach ($permissions as $permission) {
                    DB::table('role_has_permissions')->insertOrIgnore([
                        'role_id'       => $auditi->id,
                        'permission_id' => $permission->id,
                        'menu_id'       => $menu->id,
                    ]);
                }
            }
        }

        // --- Approver: hanya menu lhp ---
        $approverMenus = ['lhp', 'regulasi', 'faq'];
        foreach ($approverMenus as $menuName) {
            if ($menuName == 'regulasi' && !in_array($permission->name, ['index', 'show'])) {
                continue;
            }
            if ($menuName == 'faq' && !in_array($permission->name, ['index', 'show'])) {
                continue;
            }
            if (isset($menus[$menuName])) {
                $menu = $menus[$menuName];
                foreach ($permissions as $permission) {
                    DB::table('role_has_permissions')->insertOrIgnore([
                        'role_id'       => $approver->id,
                        'permission_id' => $permission->id,
                        'menu_id'       => $menu->id,
                    ]);
                }
            }
        }
    }
}
