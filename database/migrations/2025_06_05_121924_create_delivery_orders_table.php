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
        Schema::create('delivery_orders', function (Blueprint $table) {
            $table->id();
            $table->string('no_do')->unique();
            $table->foreignId('sales_order_id')
                ->constrained('sales_orders')
                ->onDelete('restrict');
            $table->date('tanggal')->nullable();
            $table->integer('total_qty')->default(0);
            $table->decimal('total_diskon', 12, 2)->default(0);
            $table->decimal('grand_total', 12, 2)->default(0);

            $table->string('origin')->nullable();
            $table->text('origin_name')->nullable();
            $table->string('destination')->nullable(); // untuk cek ongkir Biteship
            $table->text('destination_name')->nullable(); // untuk cek ongkir Biteship

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
        Schema::dropIfExists('delivery_orders');
    }
};
