<?php

namespace Database\Seeders;

use App\Models\KodeRekomendasi;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KodeRekomendasiSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['kode' => '01', 'nama_rekomendasi' => 'Penyetoran ke kas negara/daerah, kas BUMN/D, dan masyarakat', 'urutan' => 1],
            ['kode' => '02', 'nama_rekomendasi' => 'Pengembalian barang kepada negara, daerah, BUMN/D, dan masyarakat', 'urutan' => 2],
            ['kode' => '03', 'nama_rekomendasi' => 'Perbaikan fisik barang/jasa dalam proses pembangunan atau penggantian barang/jasa oleh rekanan', 'urutan' => 3],
            ['kode' => '04', 'nama_rekomendasi' => 'Penghapusan barang milik negara/daerah', 'urutan' => 4],
            ['kode' => '05', 'nama_rekomendasi' => 'Pelaksanaan sanksi administrasi kepegawaian', 'urutan' => 5],
            ['kode' => '06', 'nama_rekomendasi' => 'Perbaikan laporan dan penertiban administrasi/kelengkapan administrasi', 'urutan' => 6],
            ['kode' => '07', 'nama_rekomendasi' => 'Perbaikan sistem dan prosedur akuntansi dan pelaporan', 'urutan' => 7],
            ['kode' => '08', 'nama_rekomendasi' => 'Peningkatan kualitas dan kuantitas sumber daya manusia pendukung sistem pengendalian', 'urutan' => 8],
            ['kode' => '09', 'nama_rekomendasi' => 'Perubahan atau perbaikan prosedur, peraturan dan kebijakan', 'urutan' => 9],
            ['kode' => '10', 'nama_rekomendasi' => 'Perubahan atau perbaikan struktur organisasi', 'urutan' => 10],
            ['kode' => '11', 'nama_rekomendasi' => 'Koordinasi antar instansi termasuk juga penyerahan penanganan kasus kepada instansi yang berwenang', 'urutan' => 11],
            ['kode' => '12', 'nama_rekomendasi' => 'Pelaksanaan penelitian oleh tim khusus atau audit lanjutan oleh unit pengawas intern', 'urutan' => 12],
            ['kode' => '13', 'nama_rekomendasi' => 'Pelaksanaan sosialisasi', 'urutan' => 13],
            ['kode' => '14', 'nama_rekomendasi' => 'Lain-lain', 'urutan' => 14],
        ];

        foreach ($data as $item) {
            KodeRekomendasi::create($item);
        }
    }
}
