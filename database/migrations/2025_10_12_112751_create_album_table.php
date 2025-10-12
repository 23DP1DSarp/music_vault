<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('albums', function(Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('title');
            $table->string('author');
            $table->string('genre');
            $table->integer('year_of_release');
            $table->string('cover')->nullable();
            $table->string('track1');  
            $table->string('track2')->nullable();  
            $table->string('track3')->nullable();  
            $table->string('track4')->nullable();  
            $table->string('track5')->nullable();   
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('albums');
    }
};
