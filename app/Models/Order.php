<?php

namespace App\Models;

use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'shipping_address',
        'buyers_first_name',
        'buyers_last_name',
        'country_id',
        'city',
        'shipping_address',
        'postal_code',
        'phone_number',
    ];

    public function order_items() {
        return $this->hasMany(OrderItem::class);
    }
}
