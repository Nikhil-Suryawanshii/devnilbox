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
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->char('country_code', 2); // TR, MK, UA, XK
            $table->string('recipient_name', 150);
            $table->string('bank_name', 150);
            $table->string('iban', 34);
            $table->string('swift_bic', 11);
            $table->string('purpose_of_payment', 255)->nullable(); // required only for UA
            $table->boolean('is_primary')->default(false);
            $table->timestamps();


            // Indexes
            $table->index('user_id', 'idx_user');
            $table->index('country_code', 'idx_country');
            
            // Foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_accounts');
    }
};
