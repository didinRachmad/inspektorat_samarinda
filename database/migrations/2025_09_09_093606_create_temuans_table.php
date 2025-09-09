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
        Schema::create('temuans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lha_id')->constrained()->cascadeOnDelete();
            $table->year('tahun');
            $table->string('judul'); // judul temuan
            $table->string('kode_temuan')->nullable();
            $table->text('kondisi')->nullable();
            $table->text('kriteria')->nullable();
            $table->text('sebab')->nullable();
            $table->text('akibat')->nullable();
            $table->string('kode_rekomendasi')->nullable();
            $table->text('rekomendasi')->nullable();

            $table->string('file')->nullable(); // bukti pendukung
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temuans');
    }
};
