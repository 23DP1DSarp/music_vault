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
        if (!Schema::hasTable('items')) {
            Schema::create('items', function (Blueprint $table) {
                $table->id();
                $table->timestamps();
                $table->string('title');
                $table->enum('category', ['album', 'instrument', 'service']);
                $table->integer('quantity');
                $table->float('price');
                $table->enum('condition', ['good', 'used', 'bad']);
                $table->text('description', 200);
                $table->string('picture')->nullable();
                $table->foreignId('seller_id')->constrained()->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('services')) {
            Schema::create('services', function (Blueprint $table) {
                $table->id();
                $table->foreignId('item_id')->constrained()->onDelete('cascade');
                $table->integer('duration');
            });
        }

        if (!Schema::hasTable('album_items')) {
            Schema::create('album_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('album_id')->constrained()->onDelete('cascade');
                $table->foreignId('item_id')->constrained()->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('instruments')) {
            Schema::create('instruments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('item_id')->constrained()->onDelete('cascade');
                $table->string('model');
                $table->string('type');
            });
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
        Schema::dropIfExists('services');
        Schema::dropIfExists('album_items');
        Schema::dropIfExists('instruments');
    }
};
