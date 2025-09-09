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
        Schema::create('lhas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pkpt_id')->constrained('pkpts')->cascadeOnDelete(); // relasi ke PKPT
            $table->string('nomor_lha')->nullable();
            $table->date('tanggal_lha')->nullable();
            $table->text('uraian_temuan')->nullable();
            $table->text('rekomendasi')->nullable();
            $table->string('file_lha')->nullable(); // path file LHA (pdf/docx)
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('pkpt_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lhas');
    }
};
