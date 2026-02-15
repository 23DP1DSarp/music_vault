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
        Schema::create('goods', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('category', ['album', 'instrument', 'service']);
            $table->integer('quantity');
            $table->float('price');
            $table->enum('condition', ['good', 'used', 'bad']);
            $table->text('description', 200);
            $table->string('picture')->nullable();
            $table->foreignId('seller_id')->constrained()->onDelete('cascade');
        });

        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('good_id')->constrained()->onDelete('cascade');
            $table->integer('duration');
        });

        Schema::create('album_goods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('album_id')->constrained()->onDelete('cascade');
            $table->foreignId('good_id')->constrained()->onDelete('cascade');
        });

        Schema::create('instruments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('good_id')->constrained()->onDelete('cascade');
            $table->string('model');
            $table->string('type');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goods');
        Schema::dropIfExists('services');
        Schema::dropIfExists('album_goods');
        Schema::dropIfExists('instruments');
    }
};
