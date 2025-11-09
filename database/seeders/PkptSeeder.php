<?php

namespace Database\Seeders;

use App\Models\Pkpt;
use App\Models\PkptJabatan;
use App\Models\Auditi;
use App\Models\Irbanwil;
use App\Models\Mandatory;
use App\Models\JenisPengawasan;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class PkptSeeder extends Seeder
{
    protected $jumlahData = 10;

    protected $jabatanList = ['PJ', 'WPJ', 'PT', 'KT', 'AT'];
    protected $ruangLingkupList = ['PM', 'Administrasi', 'Teknis'];
    protected $sasaranList = ['Pendampingan LPPD', 'Evaluasi Kinerja', 'Audit Keuangan'];

    public function run(): void
    {
        $faker = Faker::create();
        $bulanTahun = now()->format('m-Y');

        // Ambil data Irbanwil
        $irbanwilIds = Irbanwil::pluck('id')->toArray();
        if (empty($irbanwilIds)) {
            $this->command->warn('Seeder Pkpt dilewati karena belum ada data di tabel irbanwils.');
            return;
        }

        // Ambil data mandatory
        $mandatoryIds = Mandatory::pluck('id')->toArray();
        if (empty($mandatoryIds)) {
            $this->command->warn('Seeder Pkpt dilewati karena belum ada data di tabel mandatories.');
            return;
        }

        // Ambil data auditi
        $auditiIds = Auditi::pluck('id')->toArray();
        if (empty($auditiIds)) {
            $this->command->warn('Seeder Pkpt dilewati karena belum ada data di tabel auditis.');
            return;
        }

        // Ambil data jenis_pengawasan (sub)
        $jenisPengawasanIds = JenisPengawasan::whereNotNull('parent_id')->pluck('id')->toArray();
        if (empty($jenisPengawasanIds)) {
            $this->command->warn('Seeder Pkpt dilewati karena belum ada data di tabel jenis_pengawasans.');
            return;
        }

        // Ambil nomor urut terakhir
        $lastPkpt = Pkpt::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->where('pkpt', 1)
            ->orderByDesc('id')
            ->first();
        $startUrut = $lastPkpt ? (int)substr($lastPkpt->no_pkpt, -2) + 1 : 1;

        for ($i = $startUrut; $i < $startUrut + $this->jumlahData; $i++) {
            $noUrut = str_pad($i, 2, '0', STR_PAD_LEFT);
            $kodeUnik = "PKPT-{$bulanTahun}-{$noUrut}";
            $bulanSekarang = now()->month;

            $jenisId = $faker->randomElement($jenisPengawasanIds);

            $pkpt = Pkpt::create([
                'tahun' => now()->year,
                'bulan' => $bulanSekarang,
                'no_pkpt' => $kodeUnik,
                'mandatory_id' => $faker->randomElement($mandatoryIds),
                'ruang_lingkup' => $faker->randomElement($this->ruangLingkupList),
                'sasaran' => $faker->randomElement($this->sasaranList),
                'jenis_pengawasan_id' => $jenisId,
                'jadwal_rmp_bulan' => $faker->numberBetween($bulanSekarang, 12),
                'jadwal_rsp_bulan' => $faker->numberBetween($bulanSekarang, 12),
                'jadwal_rpl_bulan' => $faker->numberBetween($bulanSekarang, 12),
                'jadwal_hp_hari' => $faker->numberBetween(10, 20),
                'irbanwil_id' => $faker->randomElement($irbanwilIds),
                'pkpt' => 1,
            ]);

            // Assign multiple auditi secara random (1-3)
            $randomAuditis = $faker->randomElements($auditiIds, $faker->numberBetween(1, 3));
            $pkpt->auditis()->sync($randomAuditis);

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
