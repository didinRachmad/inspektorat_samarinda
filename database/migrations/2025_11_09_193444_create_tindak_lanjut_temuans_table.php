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
        Schema::create('tindak_lanjut_temuans', function (Blueprint $table) {
            $table->id();

            // Relasi utama
            $table->foreignId('lhp_id')
                ->constrained('lhps')
                ->onDelete('cascade')
                ->comment('Relasi ke LHP yang berisi temuan');

            $table->foreignId('temuan_id')
                ->constrained('temuans')
                ->onDelete('cascade')
                ->comment('Relasi ke temuan spesifik yang ditindaklanjuti');

            $table->foreignId('auditi_id')
                ->constrained('users')
                ->onDelete('cascade')
                ->comment('User auditi yang membuat tindak lanjut');

            // Detail tindak lanjut
            $table->text('deskripsi_tindak_lanjut')
                ->comment('Uraian langkah tindak lanjut yang dilakukan auditi');
            $table->string('lampiran')->nullable()
                ->comment('Bukti atau dokumen pendukung tindak lanjut');
            $table->date('tanggal_tindak_lanjut')->nullable()
                ->comment('Tanggal pengisian tindak lanjut oleh auditi');

            // === Kolom approval (disamakan seperti di tabel LHP) ===
            $table->enum('approval_status', ['draft', 'waiting', 'approved'])
                ->default('draft')
                ->comment('Status approval tindak lanjut');
            $table->unsignedBigInteger('current_approval_sequence')
                ->nullable()
                ->comment('Urutan approval saat ini');
            $table->boolean('is_final_approved')
                ->default(false)
                ->comment('True jika semua level approval tindak lanjut sudah selesai');
            $table->text('approval_note')->nullable()
                ->comment('Catatan atau alasan saat approve/reject');

            // Info terakhir approval
            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment('User terakhir yang melakukan approval');
            $table->timestamp('approved_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tindak_lanjut_temuans');
    }
};
