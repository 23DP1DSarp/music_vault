<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Album extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'author',
        'genre',
        'year_of_release',
        'cover',
        'track1',
        'track2',
        'track3',
        'track4',
        'track5'
    ];
    
    
    public function showAlbums() {
        return $this->hasMany(Album::class);
    }
    
}
