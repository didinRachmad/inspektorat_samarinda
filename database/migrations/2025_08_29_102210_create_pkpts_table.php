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
            $table->smallInteger('tahun');
            $table->tinyInteger('bulan')->nullable();
            $table->string('no_pkpt')->unique();

            // Kolom utama
            $table->foreignId('mandatory_id')->constrained('mandatories')->cascadeOnDelete();
            $table->string('ruang_lingkup')->nullable();
            $table->string('sasaran');
            $table->foreignId('jenis_pengawasan_id')->constrained('jenis_pengawasans')->cascadeOnDelete();

            // Jadwal pemeriksaan
            $table->tinyInteger('jadwal_rmp_bulan')->nullable();
            $table->tinyInteger('jadwal_rsp_bulan')->nullable();
            $table->tinyInteger('jadwal_rpl_bulan')->nullable();
            $table->smallInteger('jadwal_hp_hari')->nullable();

            // Ringkasan terhitung/tersimpan (diupdate dari detail jabatan)
            $table->integer('jumlah_tenaga')->default(0);
            $table->integer('hp_5x6')->default(0);
            $table->unsignedBigInteger('anggaran_total')->default(0);

            // Relasi Irbanwil (jika ada tabel `irbanwils`)
            $table->foreignId('irbanwil_id')->nullable()->constrained('irbanwils')->nullOnDelete();

            $table->text('keterangan')->nullable();
            $table->integer('pkpt')->default(0);
            $table->string('file_surat_tugas')->nullable();

            $table->timestamps();

            // Index untuk pencarian/performance
            $table->index(['tahun', 'bulan']);
            $table->index('irbanwil_id');
        });

        // Pivot table untuk PKPT ↔ Auditi
        Schema::create('auditi_pkpt', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pkpt_id')->constrained('pkpts')->cascadeOnDelete();
            $table->foreignId('auditi_id')->constrained('auditis')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['pkpt_id', 'auditi_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auditi_pkpt');
        Schema::dropIfExists('pkpts');
    }
};
