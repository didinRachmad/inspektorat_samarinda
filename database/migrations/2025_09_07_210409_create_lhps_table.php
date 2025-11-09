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
        Schema::create('lhps', function (Blueprint $table) {
            $table->id();

            // Relasi ke PKPT
            $table->foreignId('pkpt_id')->constrained()->cascadeOnDelete();

            $table->foreignId('auditi_id')->constrained('auditis')->cascadeOnDelete();

            // Informasi dasar LHP
            $table->string('nomor_lhp')->unique()->nullable();
            $table->date('tanggal_lhp')->nullable();
            $table->string('file_lhp')->nullable(); // path file pdf/docx
            $table->text('rekomendasi')->nullable();
            $table->text('catatan')->nullable();

            // Status approval
            $table->enum('approval_status', ['draft', 'waiting', 'approved', 'rejected'])
                ->default('draft')
                ->comment('Status approval LHP');
            $table->unsignedBigInteger('current_approval_sequence')
                ->nullable()
                ->comment('Urutan approval saat ini');
            $table->boolean('is_final_approved')
                ->default(false)
                ->comment('True jika semua level approval sudah selesai');
            $table->text('approval_note')->nullable()
                ->comment('Catatan atau alasan saat approve/reject');

            // Info terakhir approval
            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment('User terakhir yang melakukan approval');
            $table->timestamp('approved_at')->nullable();

            // Audit trail
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            // Index tambahan untuk performa query
            $table->index(['pkpt_id', 'auditi_id', 'approval_status']);
            $table->index('current_approval_sequence');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lhps');
    }
};
