<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KodeTemuan;

class KodeTemuanSeeder extends Seeder
{
    public function run(): void
    {
        // =========================
        // Kategori 1: Ketidakpatuhan Terhadap Peraturan
        // =========================
        $kat1 = KodeTemuan::create([
            'kode' => '1.00.00',
            'nama_temuan' => 'Temuan Ketidakpatuhan Terhadap Peraturan',
            'level' => 1,
            'urutan' => 1,
        ]);

        KodeTemuan::create([
            'kode' => '1.01.00',
            'nama_temuan' => 'Kerugian negara/daerah atau kerugian negara/daerah yang terjadi pada perusahaan milik negara/daerah',
            'parent_id' => $kat1->id,
            'level' => 2,
            'urutan' => 1,
        ]);

        KodeTemuan::create([
            'kode' => '1.02.00',
            'nama_temuan' => 'Potensi kerugian negara/daerah atau kerugian negara/daerah yang terjadi pada perusahaan milik negara/daerah',
            'parent_id' => $kat1->id,
            'level' => 2,
            'urutan' => 2,
        ]);

        KodeTemuan::create([
            'kode' => '1.03.00',
            'nama_temuan' => 'Kekurangan penerimaan negara/daerah atau perusahaan milik negara/daerah',
            'parent_id' => $kat1->id,
            'level' => 2,
            'urutan' => 3,
        ]);

        KodeTemuan::create([
            'kode' => '1.04.00',
            'nama_temuan' => 'Administrasi',
            'parent_id' => $kat1->id,
            'level' => 2,
            'urutan' => 4,
        ]);

        KodeTemuan::create([
            'kode' => '1.05.00',
            'nama_temuan' => 'Indikasi tindak pidana',
            'parent_id' => $kat1->id,
            'level' => 2,
            'urutan' => 5,
        ]);

        // =========================
        // Kategori 2: Kelemahan Sistem Pengendalian Intern
        // =========================
        $kat2 = KodeTemuan::create([
            'kode' => '2.00.00',
            'nama_temuan' => 'Temuan Kelemahan Sistem Pengendalian Intern',
            'level' => 1,
            'urutan' => 2,
        ]);

        KodeTemuan::create([
            'kode' => '2.01.00',
            'nama_temuan' => 'Kelemahan sistem pengendalian akuntansi dan Pelaporan',
            'parent_id' => $kat2->id,
            'level' => 2,
            'urutan' => 1,
        ]);

        KodeTemuan::create([
            'kode' => '2.02.00',
            'nama_temuan' => 'Kelemahan sistem pengendalian pelaksanaan anggaran pendapatan dan belanja',
            'parent_id' => $kat2->id,
            'level' => 2,
            'urutan' => 2,
        ]);

        KodeTemuan::create([
            'kode' => '2.03.00',
            'nama_temuan' => 'Kelemahan struktur pengendalian intern',
            'parent_id' => $kat2->id,
            'level' => 2,
            'urutan' => 3,
        ]);

        // =========================
        // Kategori 3: Temuan 3E
        // =========================
        $kat3 = KodeTemuan::create([
            'kode' => '3.00.00',
            'nama_temuan' => 'Temuan 3 E',
            'level' => 1,
            'urutan' => 3,
        ]);

        KodeTemuan::create([
            'kode' => '3.01.00',
            'nama_temuan' => 'Ketidakhematan/pemborosan/ketidakekonomisan',
            'parent_id' => $kat3->id,
            'level' => 2,
            'urutan' => 1,
        ]);

        KodeTemuan::create([
            'kode' => '3.02.00',
            'nama_temuan' => 'Ketidakefisienan',
            'parent_id' => $kat3->id,
            'level' => 2,
            'urutan' => 2,
        ]);

        KodeTemuan::create([
            'kode' => '3.03.00',
            'nama_temuan' => 'Ketidakefektifan',
            'parent_id' => $kat3->id,
            'level' => 2,
            'urutan' => 3,
        ]);
    }
}
