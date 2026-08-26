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
        Schema::table('shop_user', function (Blueprint $table) {
            $table->timestamp('customer_deleted_at')->nullable();
            $table->timestamp('customer_blocked_at')->nullable();
            $table->timestamp('seller_blocked_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shop_user', function (Blueprint $table) {
            $table->dropColumn(['customer_deleted_at', 'customer_blocked_at', 'seller_blocked_at']);
        });
    }
};
