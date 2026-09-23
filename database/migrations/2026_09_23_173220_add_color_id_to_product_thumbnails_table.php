<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_thumbnails', function (Blueprint $table) {
            $table->foreignId('color_id')->nullable()->after('media_id')->constrained('colors')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('product_thumbnails', function (Blueprint $table) {
            $table->dropConstrainedForeignId('color_id');
        });
    }
};
