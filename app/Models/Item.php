<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'title',
        'category',
        'quantity',
        'price',
        'condition',
        'description',
        'item_picture',
        'seller_id',
    ];

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    public function album()
    {
        return $this->hasOne(AlbumItems::class);
    }

    public function instruments()
    {
        return $this->hasOne(Instruments::class);
    }

    public function services()
    {
        return $this->hasOne(Services::class);
    }
}
