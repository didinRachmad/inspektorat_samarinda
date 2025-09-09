<?php

namespace Database\Seeders;

use App\Models\Auditi;
use Illuminate\Database\Seeder;

class AuditiSeeder extends Seeder
{
    public function run(): void
    {
        $auditis = [
            ['nama_auditi' => 'Dinas Kesehatan', 'kode_auditi' => 'DKES', 'alamat' => 'Jl. Sehat No. 1', 'telepon' => '0274-123456'],
            ['nama_auditi' => 'Dinas Pendidikan', 'kode_auditi' => 'DPEND', 'alamat' => 'Jl. Cerdas No. 2', 'telepon' => '0274-234567'],
            ['nama_auditi' => 'Dinas Pekerjaan Umum', 'kode_auditi' => 'DPU', 'alamat' => 'Jl. Pembangunan No. 3', 'telepon' => '0274-345678'],
            ['nama_auditi' => 'Dinas Perhubungan', 'kode_auditi' => 'DISHUB', 'alamat' => 'Jl. Transportasi No. 4', 'telepon' => '0274-456789'],
            ['nama_auditi' => 'Dinas Sosial', 'kode_auditi' => 'DSOS', 'alamat' => 'Jl. Peduli No. 5', 'telepon' => '0274-567890'],
            ['nama_auditi' => 'Dinas Komunikasi dan Informatika', 'kode_auditi' => 'DISKOMINFO', 'alamat' => 'Jl. Digital No. 6', 'telepon' => '0274-678901'],
            ['nama_auditi' => 'Dinas Lingkungan Hidup', 'kode_auditi' => 'DLH', 'alamat' => 'Jl. Hijau No. 7', 'telepon' => '0274-789012'],
            ['nama_auditi' => 'Dinas Pertanian', 'kode_auditi' => 'DPRT', 'alamat' => 'Jl. Tani No. 8', 'telepon' => '0274-890123'],
            ['nama_auditi' => 'Dinas Tenaga Kerja', 'kode_auditi' => 'DISNAKER', 'alamat' => 'Jl. Kerja No. 9', 'telepon' => '0274-901234'],
            ['nama_auditi' => 'Dinas Pariwisata', 'kode_auditi' => 'DISPAR', 'alamat' => 'Jl. Wisata No. 10', 'telepon' => '0274-012345'],
        ];

        foreach ($auditis as $auditi) {
            Auditi::firstOrCreate(
                ['nama_auditi' => $auditi['nama_auditi']],
                $auditi
            );
        }
    }
}
