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
        Schema::create('user_shops', function (Blueprint $table) {
            $table->string('user_id', 8)->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('shop_id', 8)->index();
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->primary(['user_id', 'shop_id']);
            $table->unsignedBigInteger('role_id');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_shops');
    }
};
