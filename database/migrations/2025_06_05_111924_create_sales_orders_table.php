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
        Schema::create('sales_orders', function (Blueprint $table) {
            $table->id();
            $table->string('no_so')->unique();
            $table->foreignId('customer_id')->constrained()->restrictOnDelete();
            $table->enum('metode_pembayaran', ['Tunai', 'Transfer']);
            $table->date('tanggal');
            $table->integer('total_qty')->default(0);
            $table->decimal('total_diskon', 12, 2)->default(0);
            $table->decimal('grand_total', 12, 2)->default(0);
            $table->integer('approval_level')->default(0);
            $table->string('status')->default('Draft');
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_orders');
    }
};
