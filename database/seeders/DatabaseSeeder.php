<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Auditi;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(UserSeeder::class);
        $this->call(MenuSeeder::class);
        $this->call(AdminRoleSeeder::class);
        $this->call(RouteApprovalSeeder::class);
        $this->call(AuditiSeeder::class);
        $this->call(KodeTemuanSeeder::class);
        $this->call(KodeRekomendasiSeeder::class);
        $this->call(PkptSeeder::class);
        $this->call(NonPkptSeeder::class);
    }
}
