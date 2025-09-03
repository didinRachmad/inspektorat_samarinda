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
        Schema::create('delivery_order_shippings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_order_id')->constrained()->onDelete('cascade');
            $table->string('courier_code');
            $table->string('courier_name');
            $table->string('courier_service_name');
            $table->string('shipment_duration_range')->nullable();
            $table->integer('price');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_order_shippings');
    }
};
