<?php

namespace App\Models;

use App\Models\User;
use App\Models\Album;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Collection extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'album_id',
        'user_id',
        'added_at',
    ];

    public function album()
    {
        return $this->belongsTo(Album::class);
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
