<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seller extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'business_type',
        'currency',
        'full_name',
        'shipping_address',
        'minimal_order_total',
        'seller_terms'
    ];


    public function seller()
    {
        return $this->belongsTo(User::class);
    }
}
