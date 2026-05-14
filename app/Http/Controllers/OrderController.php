<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function createOrder(Request $request) {
        $incomingFields = $request->validate([
            'shipping_address' => 'required',
            'buyers_first_name' => 'required',
            'buyers_last_name' => 'required',
            'shipping_country_id' => 'integer|required',
            'city' => 'required',
            'shipping_address' => 'required',
            'postal_code' => 'required',
            'phone_number' => 'integer|required',
            'shoppingList' => 'required|array|min:1'
        ]);

        $userId = $this->getUserId($request);
        if (!$userId) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $incomingFields['user_id'] = $userId;

        $order = Order::create($incomingFields);

        foreach ($incomingFields['shoppingList'] as $data) {
            $order->order_items()->create([

                'title' => $data['title'],
                'quantity' => $data['quantity'],
                'price' => $data['price'],
                'item_id' => $data['id'],
                'shipping_country_id' => $data['country_id']
            ]);
        }
            
    }

    private function getUserId(Request $request)
    {
        $user = $request->user();
        if ($user) {
            return $user->id;
        }

        $token = $request->bearerToken();
        if ($token) {
            $user = User::where('api_token', $token)->first();
            if ($user) {
                return $user->id;
            }
        }

        return null;
    }
}
