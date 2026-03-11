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
        Schema::table('message', function (Blueprint $table) {
            // Drop the recipient_seller_id column since we're using seller_id instead
            if (Schema::hasColumn('message', 'recipient_seller_id')) {
                $table->dropForeign(['recipient_seller_id']);
                $table->dropColumn('recipient_seller_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('message', function (Blueprint $table) {
            $table->foreignId('recipient_seller_id')->constrained('sellers', 'seller_id')->onDelete('cascade')->after('customer_id');
        });
    }
};
