<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ItemController extends Controller
{
   public function sellItem(Request $request) {
        $incomingFields = $request->validate([
            'title' => 'required|string',
            'category' => 'required', 
            'album_id' => 'required_if:category,album|exists:albums,id',
            'model' => 'required_if:category,instrument|string',
            'quantity' => 'required|integer',
            'price' => 'required|numeric', 
            'condition' => 'required|string',
            'description' => 'nullable|string',
            'picture' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        if ($request->hasFile('picture')) {
            $incomingFields['picture'] = $request->file('picture')->store('images', 'public');
        }

        $incomingFields['seller_id'] = Seller::where('user_id', $request->user()->id)->value('id');

        $item = Item::create($incomingFields);

        if ($incomingFields['category'] == 'Album') {
            $item->album()->create([
                'album_id' => $incomingFields['album_id'],
                'item_id' => $item->id,
            ]);
        } elseif ($incomingFields['category'] == 'Instrument') {
            $item->instruments()->create([
                'item_id' => $item->id,
                'model' => $incomingFields['model'],
                'type' => $incomingFields['type'],
            ]);
        } elseif ($incomingFields['category'] == 'Service') {
            $item->services()->create([
                'item_id' => $item->id,
                'duration' => $incomingFields['duration'],
            ]);
        }
   }

   public function getAlbumItems() {
        $items = DB::table('items')
        ->join('sellers', 'items.seller_id', '=', 'sellers.id')
        ->join('users', 'sellers.user_id', '=', 'users.id')
        ->where('items.category', 'album')
        ->select('items.*', 'users.name as seller_name')
        ->get();
        return $items;
   }
}
