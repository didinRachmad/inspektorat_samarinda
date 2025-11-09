<?php

namespace Database\Seeders;

use App\Models\Menu;
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
        // Ambil role approver dan auditor
        $approver = Role::where('name', 'approver')->firstOrFail();
        $auditor  = Role::where('name', 'auditor')->firstOrFail();

        // --- Tambahkan route approval untuk LHP ---
        $menuLhp = Menu::where('route', 'lhp')->first();
        if ($menuLhp) {
            DB::table('approval_routes')->insertOrIgnore([
                'module'           => $menuLhp->route,
                'module_id'        => $menuLhp->id,
                'role_id'          => $approver->id,
                'sequence'         => 1,
                'assigned_user_id' => null,
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);
        } else {
            $this->command->warn("Menu 'lhp' tidak ditemukan di tabel menus.");
        }

        // --- Tambahkan route approval untuk Tindak Lanjut Temuan ---
        $menuTindakLanjut = Menu::where('route', 'tindak_lanjut_temuan')->first();
        if ($menuTindakLanjut) {
            DB::table('approval_routes')->insertOrIgnore([
                'module'           => $menuTindakLanjut->route,
                'module_id'        => $menuTindakLanjut->id,
                'role_id'          => $auditor->id,
                'sequence'         => 1,
                'assigned_user_id' => null,
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);
        } else {
            $this->command->warn("Menu 'tindak_lanjut_temuan' tidak ditemukan di tabel menus.");
        }
    }
}
