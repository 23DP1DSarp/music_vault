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
            $table->integer('position')->nullable();
            $table->string('artist')->nullable();
            $table->string('song_title')->nullable();
            $table->string('duration')->nullable();
        });
    }
    

    public function down(): void
    {
        Schema::dropIfExists('tracks');
    }
};
