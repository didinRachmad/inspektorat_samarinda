<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pkpts', function (Blueprint $table) {
            $table->id();

            // Periode & pengurutan
            $table->smallInteger('tahun');            // contoh: 2025
            $table->tinyInteger('bulan')->nullable(); // 1..12 (dokumen per "Bulan Januari/Februari", optional)
            $table->string('no_pkpt')->unique();

            // Kolom utama
            $table->foreignId('auditi_id')->constrained('auditis')->cascadeOnDelete(); // contoh: "PM" atau nama OPD (pakai teks bebas agar fleksibel)
            $table->string('ruang_lingkup')->nullable(); // contoh: "PM"
            $table->string('sasaran');                // contoh: "Pendampingan LPPD"
            $table->enum('jenis_pengawasan', ['REVIEW', 'AUDIT', 'PENGAWASAN', 'EVALUASI', 'MONITORING'])
                ->default('REVIEW');

            // Jadwal pemeriksaan
            $table->tinyInteger('jadwal_rmp_bulan')->nullable(); // 1..12
            $table->tinyInteger('jadwal_rsp_bulan')->nullable(); // 1..12
            $table->tinyInteger('jadwal_rpl_bulan')->nullable(); // 1..12
            $table->smallInteger('jadwal_hp_hari')->nullable();  // jumlah hari (HP pada bagian jadwal)

            // Ringkasan terhitung/tersimpan (diupdate dari detail jabatan)
            $table->integer('jumlah_tenaga')->default(0);        // total personel (sum dari detail)
            $table->integer('hp_5x6')->default(0);               // = jadwal_hp_hari × jumlah_tenaga
            $table->unsignedBigInteger('anggaran_total')->default(0); // total (sum anggaran per jabatan)

            // Lain-lain
            $table->string('irbanwil')->nullable();              // contoh: "IRBAN I"
            $table->string('auditor_ringkas')->nullable();       // contoh tampilan singkat: "Daniel, Roby" (opsional)
            $table->text('keterangan')->nullable();

            // Audit trail
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['tahun', 'bulan']);
            $table->index('irbanwil');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pkpts');
    }
};
