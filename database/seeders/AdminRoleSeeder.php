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

        // Ambil atau buat role super_admin
        $superAdmin = Role::firstOrCreate([
            'name'       => 'super_admin',
            'guard_name' => 'web',
        ]);

        $menus = Menu::all();
        $permissions = Permission::all();

        // Assign semua permission ke semua menu (custom pivot menu_id)
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
