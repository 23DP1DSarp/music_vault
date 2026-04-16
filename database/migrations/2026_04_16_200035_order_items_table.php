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
        if (!Schema::hasTable('order_items')) {
            Schema::create('order_items', function (Blueprint $table) {
                $table->id();
                $table->timestamps();
                $table->foreignId('order_id')->constrained()->onDelete('cascade');
                $table->string('title');
                $table->integer('quantity');
                $table->float('price');
                $table->foreignId('item_id')->constrained()->onDelete('cascade');
                $table->string('origin_address');
                $table->foreignId('country_id')->constrained()->onDelete('cascade');
                $table->string('sellers_full_name');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
