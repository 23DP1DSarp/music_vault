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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->enum('status', ['In Process', 'Shipped', 'Cancelled'])->default('In Process');
            $table->string('shipping_address');
            $table->string('buyers_first_name');
            $table->string('buyers_last_name');
            $table->foreignId('country_id')->constrained()->onDelete('cascade');
            $table->string('city');
            $table->string('postal_code');
            $table->string('phone_number');
            $table->string('credit_card_number');
            $table->string('expiry_month');
            $table->string('expiry_year');
            $table->string('cvv');
        }); 
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
