<?php

namespace App\Models;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'title',
        'quantity',
        'price',
        'item_id',
        'origin_address',
        'country_id',
        'sellers_full_name',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
