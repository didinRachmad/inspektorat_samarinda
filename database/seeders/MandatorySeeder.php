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
            ['nama' => 'Mandatory I'],
            ['nama' => 'Mandatory II'],
            ['nama' => 'Mandatory III'],
            ['nama' => 'Mandatory IV'],
        ];

        foreach ($data as $item) {
            Mandatory::firstOrCreate($item);
        }
    }
}
