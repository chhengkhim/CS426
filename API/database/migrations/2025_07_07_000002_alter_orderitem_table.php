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
        Schema::table('orderitem', function (Blueprint $table) {
            if (!Schema::hasColumn('orderitem', 'seller_id')) {
                $table->foreignId('seller_id')->after('product_id')->constrained('sellers', 'seller_id')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orderitem', function (Blueprint $table) {
            if (Schema::hasColumn('orderitem', 'seller_id')) {
                $table->dropForeignKeyIfExists(['seller_id']);
                $table->dropColumn('seller_id');
            }
        });
    }
};
