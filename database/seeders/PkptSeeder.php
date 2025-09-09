<?php

namespace Database\Seeders;

use App\Models\Pkpt;
use App\Models\PkptJabatan;
use App\Models\Auditi;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class PkptSeeder extends Seeder
{
    protected $jumlahData = 10;

    protected $jabatanList = ['PJ', 'WPJ', 'PT', 'KT', 'AT'];
    protected $ruangLingkupList = ['PM', 'Administrasi', 'Teknis'];
    protected $sasaranList = ['Pendampingan LPPD', 'Evaluasi Kinerja', 'Audit Keuangan'];
    protected $jenisPengawasanList = ['REVIEW', 'AUDIT', 'PENGAWASAN', 'EVALUASI', 'MONITORING'];
    protected $irbanwilList = ['SEMUA IRBAN', 'IRBAN I', 'IRBAN II', 'IRBAN III', 'IRBAN IV', 'IRBAN KHUSUS'];

    public function run(): void
    {
        $faker = Faker::create();
        $bulanTahun = now()->format('m-Y');

        // Ambil data auditi
        $auditiIds = Auditi::pluck('id')->toArray();
        if (empty($auditiIds)) {
            $this->command->warn('Seeder Pkpt dilewati karena belum ada data di tabel auditis.');
            return;
        }

        // Ambil nomor urut terakhir
        $lastPkpt = Pkpt::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->orderByDesc('id')
            ->first();
        $startUrut = $lastPkpt ? (int)substr($lastPkpt->no_pkpt, -2) + 1 : 1;

        for ($i = $startUrut; $i < $startUrut + $this->jumlahData; $i++) {
            $noUrut = str_pad($i, 2, '0', STR_PAD_LEFT);
            $kodeUnik = "PKPT-{$bulanTahun}-{$noUrut}";

            $pkpt = Pkpt::create([
                'tahun' => now()->year,
                'bulan' => now()->month,
                'no_pkpt' => $kodeUnik,
                'auditi_id' => $faker->randomElement($auditiIds),
                'ruang_lingkup' => $faker->randomElement($this->ruangLingkupList),
                'sasaran' => $faker->randomElement($this->sasaranList),
                'jenis_pengawasan' => $faker->randomElement($this->jenisPengawasanList),
                'jadwal_rmp_bulan' => $faker->numberBetween(1, 3),
                'jadwal_rsp_bulan' => $faker->numberBetween(1, 3),
                'jadwal_rpl_bulan' => $faker->numberBetween(1, 3),
                'jadwal_hp_hari' => $faker->numberBetween(10, 20),
                'irbanwil' => $faker->randomElement($this->irbanwilList),
            ]);

            // Detail jabatan dinamis
            foreach ($this->jabatanList as $jabatan) {
                PkptJabatan::create([
                    'pkpt_id' => $pkpt->id,
                    'jabatan' => $jabatan,
                    'jumlah' => $faker->numberBetween(1, 5),
                    'anggaran' => $faker->numberBetween(1000000, 5000000),
                ]);
            }

            // Hitung ulang summary
            $detail = $pkpt->jabatans()->get();
            $pkpt->jumlah_tenaga = $detail->sum('jumlah');
            $pkpt->anggaran_total = $detail->sum('anggaran');
            $pkpt->hp_5x6 = $pkpt->jadwal_hp_hari * $pkpt->jumlah_tenaga;
            $pkpt->save();
        }
    }
}
