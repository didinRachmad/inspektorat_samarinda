<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Permission;
use App\Models\Role;
use DB;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class AdminRoleSeeder extends Seeder
{
    public function run(): void
    {
        // Bersihkan cache permission Spatie
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Hanya role super_admin
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);

        // Ambil semua menu dan permission
        $menus = Menu::all();
        $permissions = Permission::all();

        // Assign semua permission ke semua menu untuk super_admin
        foreach ($menus as $menu) {
            foreach ($permissions as $permission) {
                DB::table('role_has_permissions')->insertOrIgnore([
                    'role_id'       => $superAdmin->id,
                    'permission_id' => $permission->id,
                    'menu_id'       => $menu->id,
                ]);
            }
        }
    }
}