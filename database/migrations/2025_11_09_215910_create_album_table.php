<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

        Schema::create('genres', function(Blueprint $table) {
            $table->id();
            $table->string('genre_title');
        });

        Schema::create('countries', function(Blueprint $table) {
            $table->id();
            $table->string('country_name');
        });

        Schema::create('albums', function(Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('title');
            $table->string('author');
            $table->foreignId('genre_id')->constrained()->onDelete('cascade');
            $table->string('label')->nullable();
            $table->date('release_date');
            $table->foreignId('country_id')->constrained()->onDelete('cascade');
            $table->string('cover')->nullable();
            $table->text('notes')->nullable();
        });

        Schema::create('formats', function(Blueprint $table) {
            $table->id();
            $table->string('format_name');
        });

        Schema::create('album_formats', function(Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('album_id')->constrained()->onDelete('cascade');
            $table->foreignId('format_id')->constrained()->onDelete('cascade');
        });
    }

    public function down(): void
    {   
        Schema::dropIfExists('genres');
        Schema::dropIfExists('countries');
        Schema::dropIfExists('albums');
        Schema::dropIfExists('formats');
        Schema::dropIfExists('album_formats');
    }
};
