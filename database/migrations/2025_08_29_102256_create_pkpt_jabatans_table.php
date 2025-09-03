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
        Schema::create('pkpt_jabatans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pkpt_id')->constrained('pkpts')->cascadeOnDelete();

            $table->enum('jabatan', ['PJ', 'WPJ', 'PT', 'KT', 'AT']);
            $table->integer('jumlah')->nullable()->default(0);                 // kolom "Jumlah Tenaga auditor/pemeriksaan" per jabatan
            $table->unsignedBigInteger('anggaran')->nullable()->default(0);    // "Anggaran Pemeriksaan (Rp)" per jabatan

            $table->timestamps();

            $table->unique(['pkpt_id', 'jabatan']); // satu record per jabatan per PKPT
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pkpt_jabatans');
    }
};
