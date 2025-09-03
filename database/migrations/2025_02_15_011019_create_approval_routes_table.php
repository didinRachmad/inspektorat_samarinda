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
        Schema::create('approval_routes', function (Blueprint $table) {
            $table->id();
            $table->string('module'); // misal: 'Item'
            $table->unsignedBigInteger('role_id');
            $table->integer('sequence'); // urutan approval
            $table->unsignedBigInteger('assigned_user_id')->nullable(); // jika ingin meng-assign user tertentu
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->foreign('assigned_user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_routes');
    }
};
