<?php

namespace App\Models;

use App\Models\Item;
use Illuminate\Database\Eloquent\Model;

class Services extends Model
{
    protected $fillable = [
        'item_id',
        'duration',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
