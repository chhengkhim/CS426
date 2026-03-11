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
        Schema::create('review', function (Blueprint $table) {
            $table->id('review_id');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('order_id');
            $table->integer('rating');
            $table->text('comment')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('customer_id')->references('customer_id')->on('customers')->onDelete('cascade');
            $table->foreign('product_id')->references('product_id')->on('product')->onDelete('cascade');
            $table->foreign('order_id')->references('order_id')->on('orders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('review');
    }
};
