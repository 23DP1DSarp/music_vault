<?php

namespace App\Models;

use App\Models\AlbumItems;
use App\Models\Instruments;
use App\Models\Item;
use App\Models\Seller;
use App\Models\Services;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{

    use HasFactory;

    protected $fillable = [
        'title',
        'category',
        'quantity',
        'price',
        'condition',
        'description',
        'picture',
        'seller_id',
    ];

    public function showItems() {
        return $this->hasMany(Item::class);
    }

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
