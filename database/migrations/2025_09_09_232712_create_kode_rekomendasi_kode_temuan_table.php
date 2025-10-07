<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kode_rekomendasi_kode_temuan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kode_rekomendasi_id')
                ->constrained('kode_rekomendasis')
                ->cascadeOnDelete();
            $table->foreignId('kode_temuan_id')
                ->constrained('kode_temuans')
                ->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['kode_rekomendasi_id', 'kode_temuan_id'], 'kode_rekomendasi_temuan_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kode_rekomendasi_kode_temuan');
    }
};
