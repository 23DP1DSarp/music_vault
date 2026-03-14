<?php

namespace App\Models;

use App\Models\Item;
use Illuminate\Database\Eloquent\Model;

class Instruments extends Model
{
    protected $fillable = [
        'item_id',
        'model',
        'type',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
