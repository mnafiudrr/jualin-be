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
        Schema::create('carts', function (Blueprint $table) {
            $table->string('id', 8)->primary()->unique();
            $table->string('user_id', 8);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('shop_id', 8);
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->string('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
