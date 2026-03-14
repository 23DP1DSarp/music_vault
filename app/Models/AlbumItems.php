<?php

namespace App\Models;

use App\Models\Album;
use App\Models\Item;
use Illuminate\Database\Eloquent\Model;

class AlbumItems extends Model
{
    protected $fillable = [
        'album_id',
        'item_id',
    ];

    public function album()
    {
        return $this->belongsTo(Album::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
