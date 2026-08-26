<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shop_followers', function (Blueprint $table) {
            if (! Schema::hasColumn('shop_followers', 'is_liked')) {
                $table->boolean('is_liked')->default(true)->after('shop_id');
            }
        });

        try {
            Schema::table('shop_followers', function (Blueprint $table) {
                $table->unique(['user_id', 'shop_id']);
            });
        } catch (\Throwable) {
            // Unique index may already exist
        }
    }

    public function down(): void
    {
        Schema::table('shop_followers', function (Blueprint $table) {
            try {
                $table->dropUnique(['user_id', 'shop_id']);
            } catch (\Throwable) {
                //
            }
            if (Schema::hasColumn('shop_followers', 'is_liked')) {
                $table->dropColumn('is_liked');
            }
        });
    }
};
