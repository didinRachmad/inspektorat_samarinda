<?php

namespace Database\Seeders;

use App\Models\Auditi;
use App\Models\Irbanwil;
use Illuminate\Database\Seeder;

class AuditiSeeder extends Seeder
{
    public function run(): void
    {
        $auditis = [
            ['nama_auditi' => 'Dinas Kesehatan', 'kode_auditi' => 'DKES', 'irbanwil' => 'Irbanwil I', 'alamat' => 'Jl. Sehat No. 1', 'telepon' => '0274-123456'],
            ['nama_auditi' => 'Dinas Pendidikan', 'kode_auditi' => 'DPEND', 'irbanwil' => 'Irbanwil I', 'alamat' => 'Jl. Cerdas No. 2', 'telepon' => '0274-234567'],
            ['nama_auditi' => 'Dinas Pekerjaan Umum', 'kode_auditi' => 'DPU', 'irbanwil' => 'Irbanwil II', 'alamat' => 'Jl. Pembangunan No. 3', 'telepon' => '0274-345678'],
            ['nama_auditi' => 'Dinas Perhubungan', 'kode_auditi' => 'DISHUB', 'irbanwil' => 'Irbanwil II', 'alamat' => 'Jl. Transportasi No. 4', 'telepon' => '0274-456789'],
            ['nama_auditi' => 'Dinas Sosial', 'kode_auditi' => 'DSOS', 'irbanwil' => 'Irbanwil III', 'alamat' => 'Jl. Peduli No. 5', 'telepon' => '0274-567890'],
            ['nama_auditi' => 'Dinas Komunikasi dan Informatika', 'kode_auditi' => 'DISKOMINFO', 'irbanwil' => 'Irbanwil III', 'alamat' => 'Jl. Digital No. 6', 'telepon' => '0274-678901'],
            ['nama_auditi' => 'Dinas Lingkungan Hidup', 'kode_auditi' => 'DLH', 'irbanwil' => 'Irbanwil IV', 'alamat' => 'Jl. Hijau No. 7', 'telepon' => '0274-789012'],
            ['nama_auditi' => 'Dinas Pertanian', 'kode_auditi' => 'DPRT', 'irbanwil' => 'Irbanwil IV', 'alamat' => 'Jl. Tani No. 8', 'telepon' => '0274-890123'],
            ['nama_auditi' => 'Dinas Tenaga Kerja', 'kode_auditi' => 'DISNAKER', 'irbanwil' => 'Irbanwil Khusus', 'alamat' => 'Jl. Kerja No. 9', 'telepon' => '0274-901234'],
            ['nama_auditi' => 'Dinas Pariwisata', 'kode_auditi' => 'DISPAR', 'irbanwil' => 'Irbanwil Khusus', 'alamat' => 'Jl. Wisata No. 10', 'telepon' => '0274-012345'],
        ];

        foreach ($auditis as $auditi) {
            $irbanwil = Irbanwil::where('nama', $auditi['irbanwil'])->first();

            Auditi::firstOrCreate(
                ['nama_auditi' => $auditi['nama_auditi']],
                [
                    'kode_auditi'  => $auditi['kode_auditi'],
                    'irbanwil_id'  => $irbanwil?->id,
                    'alamat'       => $auditi['alamat'],
                    'telepon'      => $auditi['telepon'],
                ]
            );
        }
    }
}
