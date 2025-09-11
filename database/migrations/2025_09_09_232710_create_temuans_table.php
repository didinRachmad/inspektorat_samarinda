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
            $table->foreignId('lha_id')->constrained('lhas')->onDelete('cascade'); // relasi ke LHA

            $table->string('judul_temuan');
            $table->foreignId('kode_temuan_id')->constrained('kode_temuans')->onDelete('restrict');

            $table->text('kondisi_temuan');
            $table->text('kriteria_temuan')->nullable();
            $table->text('sebab_temuan')->nullable();
            $table->text('akibat_temuan')->nullable();

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
