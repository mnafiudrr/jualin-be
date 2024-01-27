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
        Schema::create('cart_products', function (Blueprint $table) {
            $table->string('cart_id', 8);
            $table->foreign('cart_id')->references('id')->on('carts')->onDelete('cascade');
            $table->string('product_id', 8);
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->primary(['cart_id', 'product_id']);
            $table->integer('quantity')->default(1);
            $table->string('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_products');
    }
};
