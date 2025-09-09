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
        Schema::create('hpds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lha_id')->constrained('lhas')->cascadeOnDelete();

            $table->year('tahun');
            $table->string('judul'); // hal-hal yang perlu diperhatikan
            $table->text('kondisi')->nullable();
            $table->text('kriteria')->nullable();
            $table->text('saran')->nullable();

            $table->string('file')->nullable(); // bukti pendukung
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hpds');
    }
};
