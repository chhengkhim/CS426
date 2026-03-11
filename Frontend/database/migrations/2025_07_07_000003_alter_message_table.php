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
            // Add missing columns for message system
            if (!Schema::hasColumn('message', 'seller_id')) {
                $table->foreignId('seller_id')->nullable()->after('customer_id')->constrained('sellers', 'seller_id')->onDelete('cascade');
            }
            if (!Schema::hasColumn('message', 'admin_id')) {
                $table->foreignId('admin_id')->nullable()->after('seller_id')->constrained('admin', 'admin_id')->onDelete('cascade');
            }
            if (!Schema::hasColumn('message', 'sender_type')) {
                $table->enum('sender_type', ['customer', 'seller', 'admin'])->default('customer')->after('feedback');
            }
            // Rename feedback to messages if it doesn't exist
            if (Schema::hasColumn('message', 'feedback') && !Schema::hasColumn('message', 'messages')) {
                $table->renameColumn('feedback', 'messages');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('message', function (Blueprint $table) {
            if (Schema::hasColumn('message', 'seller_id')) {
                $table->dropForeignKeyIfExists(['seller_id']);
                $table->dropColumn('seller_id');
            }
            if (Schema::hasColumn('message', 'admin_id')) {
                $table->dropForeignKeyIfExists(['admin_id']);
                $table->dropColumn('admin_id');
            }
            if (Schema::hasColumn('message', 'sender_type')) {
                $table->dropColumn('sender_type');
            }
            if (Schema::hasColumn('message', 'messages') && !Schema::hasColumn('message', 'feedback')) {
                $table->renameColumn('messages', 'feedback');
            }
        });
    }
};
