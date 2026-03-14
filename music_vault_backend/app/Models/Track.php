<?php

namespace App\Models;

use App\Models\Album;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Track extends Model
{
    use HasFactory;

    protected $fillable = [
        'album_id',
        'position',
        'artist',
        'song_title',
        'duration'
    ];


    public function album()
    {
        return $this->belongsTo(Album::class);
    }

}
