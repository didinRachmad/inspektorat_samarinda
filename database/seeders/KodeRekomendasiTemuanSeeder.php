<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KodeRekomendasiTemuanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            // Temuan level 2 dari Ketidakpatuhan Terhadap Peraturan
            ['kode_temuan_id' => 2, 'kode_rekomendasi_id' => 1],
            ['kode_temuan_id' => 2, 'kode_rekomendasi_id' => 2],
            ['kode_temuan_id' => 2, 'kode_rekomendasi_id' => 3],
            ['kode_temuan_id' => 3, 'kode_rekomendasi_id' => 1],
            ['kode_temuan_id' => 3, 'kode_rekomendasi_id' => 2],
            ['kode_temuan_id' => 4, 'kode_rekomendasi_id' => 6],
            ['kode_temuan_id' => 5, 'kode_rekomendasi_id' => 11],

            // Temuan level 2 dari Kelemahan Sistem Pengendalian Intern
            ['kode_temuan_id' => 8, 'kode_rekomendasi_id' => 7],
            ['kode_temuan_id' => 8, 'kode_rekomendasi_id' => 8],
            ['kode_temuan_id' => 9, 'kode_rekomendasi_id' => 7],
            ['kode_temuan_id' => 10, 'kode_rekomendasi_id' => 9],

            // Temuan level 2 dari Temuan 3 E
            ['kode_temuan_id' => 12, 'kode_rekomendasi_id' => 9],
            ['kode_temuan_id' => 12, 'kode_rekomendasi_id' => 10],
            ['kode_temuan_id' => 13, 'kode_rekomendasi_id' => 9],
            ['kode_temuan_id' => 14, 'kode_rekomendasi_id' => 9],
        ];

        $now = now();
        foreach ($data as &$d) {
            $d['created_at'] = $now;
            $d['updated_at'] = $now;
        }
        DB::table('kode_temuan_rekomendasi')->insert($data);
    }
}
