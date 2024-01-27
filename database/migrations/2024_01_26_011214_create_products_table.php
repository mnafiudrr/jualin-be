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
        Schema::create('products', function (Blueprint $table) {
            $table->string('id', 8)->primary()->unique()->index();
            $table->string('name');
            $table->boolean('is_show_in_transaction')->default(true);
            $table->boolean('is_using_stock')->default(true);
            $table->integer('unit_value')->default(1);
            $table->unsignedBigInteger('unit_id');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('restrict');
            $table->integer('stock')->default(0);
            $table->integer('stock_min')->default(0);
            $table->string('barcode')->nullable();
            $table->integer('base_price')->default(0);
            $table->integer('price')->default(0);
            $table->integer('discount')->default(0);
            // discount_type: 0 = percent, 1 = nominal
            $table->integer('discount_type')->default(0);
            $table->string('image')->nullable();
            $table->string('rack')->nullable();
            $table->string('description')->nullable();
            $table->string('shop_id', 8)->index();
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
