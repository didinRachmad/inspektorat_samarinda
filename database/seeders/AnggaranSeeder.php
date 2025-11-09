<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Anggaran;

class AnggaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Anggaran::firstOrCreate(['anggaran' => 170000]);
    }
}
