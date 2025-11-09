<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Mandatory;

class MandatorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['nama' => 'Reguler'],
            ['nama' => 'Pengaduan'],
            ['nama' => 'Pendampingan Eksternal'],
            ['nama' => 'MCP KPK'],
        ];

        foreach ($data as $item) {
            Mandatory::firstOrCreate($item);
        }
    }
}
