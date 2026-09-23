<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('warranty_label')->nullable()->after('meta_keywords');
            $table->string('warranty_note')->nullable()->after('warranty_label');
            $table->unsignedInteger('return_days')->nullable()->after('warranty_note');
            $table->string('return_note')->nullable()->after('return_days');
        });

        Schema::table('shops', function (Blueprint $table) {
            $table->string('support_note')->nullable()->after('estimated_delivery_time');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['warranty_label', 'warranty_note', 'return_days', 'return_note']);
        });

        Schema::table('shops', function (Blueprint $table) {
            $table->dropColumn('support_note');
        });
    }
};
