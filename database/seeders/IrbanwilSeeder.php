<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Irbanwil;

class IrbanwilSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['nama' => 'Irbanwil I'],
            ['nama' => 'Irbanwil II'],
            ['nama' => 'Irbanwil III'],
            ['nama' => 'Irbanwil IV'],
            ['nama' => 'Irbanwil Khusus'],
        ];

        foreach ($data as $item) {
            Irbanwil::firstOrCreate($item);
        }
    }
}
