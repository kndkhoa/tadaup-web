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
        Schema::create('customers', function (Blueprint $table) {
            $table->id('customer_id');
            $table->id('user_id');
            $table->id('role_id');
            $table->string('full_name');
            $table->string('phone');
            $table->string('address');
            $table->string('image_font_id');
            $table->string('image_back_id');
            $table->string('bank_account');
            $table->id('user_sponser_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
