<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JenisPengawasan;

class JenisPengawasanSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Audit' => [
                'Audit Kinerja',
                'Audit Operasional',
                'ADTT/PDTT',
            ],
            'Reviu' => [
                'Reviu DAK Fisik',
                'Reviu Penyerapan Anggaran (PA)',
                'Reviu DAU Penggajian PPPK',
                'Reviu DAU Pendanaan Kelurahan',
                'Reviu DAU Bidang Kesehatan',
                'Reviu KUPA/PPAS P 2024',
                'Reviu Penyerapan DAU',
                'Reviu Manajemen ASN',
                'Reviu KUA 2024',
                'Reviu Tata Kelola BMD',
                'Reviu SSH dan ASB',
                'Reviu RKPD Perubahan 2024',
                'Reviu Capaian Penggunaan DAU Bidang Pekerjaan Umum',
                'Reviu Capaian Penggunaan DAK',
                'Reviu Implementasi E-purchasing',
                'Reviu Serapan PBJ',
                'Reviu Bantuan Sosial (Bansos)/Hibah',
                'Reviu Renja Perubahan 2024',
                'Reviu Renja 2025',
                'Reviu RKA Perubahan',
                'Reviu RKA',
                'Reviu P3DN',
                'Reviu KUPA/PPAS 2025',
            ],
            'Monitoring' => [
                'Monitoring Pengendalian Gratifikasi',
                'Monitoring P3DN Semester 1 Tahun 2024',
                'Monitoring WBS',
                'Monitoring MCP KPK',
                'Monitoring Tindak Lanjut AKIP',
                'Monitoring Pengadaan Barang/Jasa (PBJ)',
                'Monitoring TLHP Eksternal dan Internal',
                'Monitoring Stock Opname',
                'Monitoring Penanganan Benturan Kepentingan',
            ],
            'Evaluasi' => [
                'Evaluasi Internal Reformasi Birokrasi General',
                'Evaluasi Pelaksanaan Reformasi Birokrasi Tematik Pengendalian Inflasi dan Peningkatan Investasi',
                'Evaluasi Internal Reformasi Birokrasi Tematik Digitalisasi Administrasi Pemerintahan',
                'Evaluasi Internal Reformasi Birokrasi Tematik Peningkatan P3DN dan Pengentasan Kemiskinan',
                'Evaluasi Penilaian Maturitas SPIP',
            ],
            'Kegiatan Pengawasan Lainnya' => [
                'Penelitian dan Penelaahan Informasi',
                'Layanan Klinik Konsultasi',
                'FGD Pengelolaan dan Penatausahaan OPAD',
                'Saber Pungli',
                'Pemantauan Seleksi CASN',
                'Peer Reviu Internal',
                'Peer Reviu Eksternal',
            ],
        ];

        $parentIndex = 1; // urutan parent mulai dari 1

        foreach ($data as $parentName => $children) {
            // Buat parent dengan urutan
            $parent = JenisPengawasan::create([
                'nama' => $parentName,
                'parent_id' => null,
                'urutan' => $parentIndex,
            ]);

            // Buat sub-item dengan urutan mulai dari 1
            foreach ($children as $index => $childName) {
                JenisPengawasan::create([
                    'nama' => $childName,
                    'parent_id' => $parent->id,
                    'urutan' => $index + 1,
                ]);
            }

            $parentIndex++;
        }
    }
}
