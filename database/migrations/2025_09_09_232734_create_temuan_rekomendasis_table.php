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
        Schema::create('temuan_rekomendasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('temuan_id')->constrained('temuans')->cascadeOnDelete();
            $table->foreignId('kode_rekomendasi_id')->constrained('kode_rekomendasis')->restrictOnDelete();

            $table->text('rekomendasi_temuan')->nullable(); // isi rekomendasi temuan
            $table->unsignedBigInteger('nominal')->default(0);

            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temuan_rekomendasis');
    }
};
