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
        Schema::create('kkas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lha_id')->constrained('lhas')->cascadeOnDelete();
            $table->string('judul');
            $table->string('uraian_prosedur')->nullable();
            $table->text('hasil')->nullable();
            $table->string('file_kka')->nullable();
            $table->foreignId('auditor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('lha_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kkas');
    }
};
