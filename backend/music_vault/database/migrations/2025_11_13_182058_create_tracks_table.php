<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{


    public function up(): void
    {
        Schema::create('tracks', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('position');
            $table->string('artist');
            $table->string('song_title');
            $table->string('duration');
            $table->foreignId('album_id')->constrained()->onDelete('cascade');
        });
    }
    

    public function down(): void
    {
        Schema::dropIfExists('tracks');
    }
};
