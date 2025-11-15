<?php

namespace App\Models;

use App\Models\Track;
use App\Models\Collection;
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
        'label',
        'release_date',
        'country',
        'cover',
        'format',
        'notes'

    ];
    
    
    public function showAlbums() {
        return $this->hasMany(Album::class);
    }


    public function tracks() {
        return $this->hasMany(Track::class);
    }

    public function collection() {
        return $this->hasMany(Collection::class);
    }
    
}
