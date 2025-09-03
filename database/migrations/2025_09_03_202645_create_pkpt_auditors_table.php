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
        Schema::create('pkpt_auditors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pkpt_id')->constrained('pkpts')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // kalau auditor ada di tabel users
            $table->string('nama_manual')->nullable(); // fallback bila tidak memakai users
            $table->enum('jabatan', ['PJ', 'WPJ', 'PT', 'KT', 'AT'])->nullable(); // peran orang tsb pada kegiatan
            $table->timestamps();

            $table->index(['pkpt_id', 'jabatan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pkpt_auditors');
    }
};
