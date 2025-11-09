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
            $table->foreignId('lhp_id')->constrained('lhps')->cascadeOnDelete();
            $table->string('judul_temuan');
            $table->foreignId('kode_temuan_id')->constrained('kode_temuans')->restrictOnDelete();

            $table->text('kondisi_temuan')->nullable();
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
