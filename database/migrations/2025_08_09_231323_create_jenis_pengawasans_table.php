<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jenis_pengawasans', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('jenis_pengawasans')
                ->cascadeOnDelete();
            $table->unsignedInteger('urutan')->default(1); // kolom urutan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jenis_pengawasans');
    }
};
