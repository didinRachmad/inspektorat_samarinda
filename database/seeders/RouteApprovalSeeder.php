<?php

namespace Database\Seeders;

use App\Models\Role;
use DB;
use Illuminate\Database\Seeder;

class RouteApprovalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil role super_admin
        $superAdmin = Role::where('name', 'super_admin')->firstOrFail();

        // Daftar module yang butuh approval route
        $modules = ['temuan'];

        foreach ($modules as $module) {
            // Assign super_admin sebagai sequence 1 dan 2 (atau sesuai kebutuhan)
            for ($sequence = 1; $sequence <= 2; $sequence++) {
                DB::table('approval_routes')->insertOrIgnore([
                    'module'            => $module,
                    'role_id'           => $superAdmin->id,
                    'sequence'          => $sequence,
                    'assigned_user_id'  => null,
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ]);
            }
        }
    }
}
