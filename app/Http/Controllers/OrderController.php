<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use App\Mail\OrderConfirmation;
use App\Models\Item;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;


class OrderController extends Controller
{

    public function checkAvaliabllity(Request $request) {
        $shoppingList = $request->validate([
            'shoppingList' => 'required|array|min:1'
        ]);

        $unavailableItems = [];

        foreach ($shoppingList['shoppingList'] as $data) {
            $item = Item::find($data['id']);
            if (!$item || $item->quantity < $data['quantity']) {
                $unavailableItems[] = [
                    'id' => $data['id'],
                    'requested_quantity' => $data['quantity'],
                    'available_quantity' => $item ? $item->quantity : 0
                ];
            }
        }

        if (!empty($unavailableItems)) {
            return response()->json(['unavailable_items' => $unavailableItems], 202);
        } else {
            return $this->createOrder($request);
        }

    } 

    public function createOrder(Request $request) {
        $incomingFields = $request->validate([
            'shipping_address' => 'required',
            'buyers_first_name' => 'required',
            'buyers_last_name' => 'required',
            'country_id' => 'integer|required',
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
                'shipping_country_id' => $data['shipping_country_id']
            ]);

            Item::where('id',$data['id'])->update(['quantity'=> DB::raw('quantity - ' . $data['quantity'])]);

            if (Item::where('id',$data['id'])->value('quantity') <= 0) {
                Item::where('id', $data['id'])->delete();
            }
        }

        $shoppingList = $request->input('shoppingList', []);

        $this->sendOrderConfirmation($shoppingList);

        


    }

    public function sendOrderConfirmation($shoppingList)
    {
        $details = [
            'name' => User::where('id', $this->getUserId(request()))->value('username'),
            'message' => 'Jūs esat pasūtījis preci mūsu tirgū. Mēs apstrādāsim jūsu pasūtījumu un nosūtīsim to pēc iespējas ātrāk. Paldies, ka iepirkāties pie mums!\n        Jūsu pasūtījuma ID ir ' . Order::where('user_id', $this->getUserId(request()))->latest()->value('id') . ' un jūsu pasūtītās preces: ' . implode(', ', array_map(function($item) {
                return $item['title'] . ' (Daudzums: ' . $item['quantity'] . ')';
            }, $shoppingList))

        ];

        Mail::to(User::where('id', $this->getUserId(request()))->value('email'))->send(new OrderConfirmation($details));

        $this->sendSellerNotification($shoppingList);

        return "Email Sent!";
    }

    public function sendSellerNotification($shoppingList)
    {
        $details = [
            'name' => User::where('id', $this->getUserId(request()))->value('username'),
            'message' => 'Jūs esat pārdevis preci mūsu tirgū. Mēs apstrādāsim pasūtījumu un informēsim jūs, kad tas tiks nosūtīts pircējam. Paldies, ka pārdodat pie mums!\n            Jūsu pasūtījuma ID ir ' . Order::where('user_id', $this->getUserId(request()))->latest()->value('id') . ' un jūsu pārdotās preces: ' . implode(', ', array_map(function($item) {
                return $item['title'] . ' (Daudzums: ' . $item['quantity'] . ')';
            }, $shoppingList))

        ];

        Mail::to(User::where('id', $this->getUserId(request()))->value('email'))->send(new OrderConfirmation($details));

        return "Email Sent!";
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
