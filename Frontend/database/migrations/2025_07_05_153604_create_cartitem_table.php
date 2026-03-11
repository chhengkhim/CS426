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
        Schema::create('cartitem', function (Blueprint $table) {
            $table->id('cart_item_id');
            $table->foreignId('customer_id')->constrained('customers', 'customer_id')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('product', 'product_id')->onDelete('cascade');
            $table->integer('quantity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cartitem');
    }
};
