<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SellerController extends Controller
{
    public function createSeller(Request $request) {
        $incomingFields = $request->validate([
            'business_type' => 'required',
            'currency' => 'required',
            'full_name' => 'required',
            'shipping_address' => 'required',
            'minimal_order_total' => 'required|numeric',
            'seller_terms' => 'required'
        ]);

        $incomingFields['user_id'] = auth()->id();

        Seller::create($incomingFields);

        DB::table('users')
            ->where('id', auth()->id())
            ->update(['user_role_id' => 2]);

        return response()->json(['message' => 'Seller created successfully']);
    }
}
