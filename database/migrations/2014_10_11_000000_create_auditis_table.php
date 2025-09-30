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
        Schema::create('auditis', function (Blueprint $table) {
            $table->id();
            $table->string('nama_auditi')->unique();
            $table->string('kode_auditi')->nullable();
            $table->foreignId('irbanwil_id')->nullable()->constrained('irbanwils')->nullOnDelete();
            $table->string('alamat')->nullable();
            $table->string('telepon')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auditis');
    }
};
