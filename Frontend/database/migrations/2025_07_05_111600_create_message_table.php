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
        Schema::create('message', function (Blueprint $table) {
            $table->id('message_id');
            $table->foreignId('customer_id')->constrained('customers', 'customer_id')->onDelete('cascade');
            $table->foreignId('recipient_seller_id')->constrained('sellers', 'seller_id')->onDelete('cascade');
            $table->string('subject');
            $table->text('feedback');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message');
    }
};
