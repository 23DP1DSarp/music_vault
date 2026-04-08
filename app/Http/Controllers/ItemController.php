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
            'type' => 'required_if:category,instrument|string',
            'quantity' => 'nullable|integer',
            'price' => 'required|numeric', 
            'duration' => 'required_if:category,service|integer',
            'condition' => 'nullable|string',
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

   public function getAllAlbumItems() {
        $items = DB::table('items')
        ->join('sellers', 'items.seller_id', '=', 'sellers.id')
        ->join('users', 'sellers.user_id', '=', 'users.id')
        ->join('album_items', 'album_items.item_id', '=', 'items.id')
        ->join('albums', 'album_items.album_id', '=', 'albums.id')
        ->join('genres', 'albums.genre_id', '=', 'genres.id')
        ->join('countries', 'countries.id', '=', 'albums.country_id')
        ->where('items.category', 'album')
        ->select('items.*', 'albums.release_date as release_date', 'genres.genre_title as genre', 'countries.country_name as country', 'users.name as seller_name')
        ->get();
        return $items;
    }

    public function getAlbumItem (Item $item) {
        $item = DB::table('items')
        ->join('sellers', 'items.seller_id', '=', 'sellers.id')
        ->join('users', 'sellers.user_id', '=', 'users.id')
        ->join('countries', 'countries.id', '=', 'users.country_id')
        ->join('album_items', 'album_items.item_id', '=', 'items.id')
        ->join('albums', 'album_items.album_id', '=', 'albums.id')
        ->where('items.id', '=', $item->id)
        ->where('items.category', 'album')
        ->select('items.*', 'users.name as seller_name', 'albums.id as album_id', 'countries.country_name as shipping_country', DB::raw('DATE(items.created_at) as created_at'))
        ->get();
        return $item;
    }

    public function filterAlbumItems(Request $request)
        {
            $genres = $request->input('genres', []);
            $countries = $request->input('countries', []);
            $decades = $request->input('decades', []);
            $minPrice = $request['minPrice'];
            $maxPrice = $request['maxPrice'];

            if (!empty($genres) || !empty($countries) || !empty($decades) || ($minPrice != null && $maxPrice != null)){
                $items = DB::table('items')
                    ->when(!empty($genres), function ($query) use ($genres) {
                        $query->whereIn('genres.genre_title', $genres);
                    })
                    ->when(!empty($countries), function ($query) use ($countries) {
                        $query->whereIn('countries.country_name', $countries);
                    })
                    ->when(!empty($decades), function ($query) use ($decades) {
                        $query->whereBetween(DB::raw('YEAR(albums.release_date)'), [$decades[0], $decades[0] + 9]);

                        if (count($decades) > 1) {
                            for ($i=1; $i < count($decades); $i++) { 
                                $query->orWhereBetween(DB::raw('YEAR(albums.release_date)'), [$decades[$i], $decades[$i] + 9]);
                            }
                        }
                    })
                    ->when($minPrice != null && $maxPrice != null, function ($query) use ($minPrice, $maxPrice) {
                        $query->whereBetween('items.price', [$minPrice, $maxPrice]);
                    })
                    ->join('sellers', 'items.seller_id', '=', 'sellers.id')
                    ->join('users', 'sellers.user_id', '=', 'users.id')
                    ->join('album_items', 'album_items.item_id', '=', 'items.id')
                    ->join('albums', 'album_items.album_id', '=', 'albums.id')
                    ->join('genres', 'albums.genre_id', '=', 'genres.id')
                    ->join('countries', 'countries.id', '=', 'albums.country_id')
                    ->where('items.category', 'album')
                    ->select('items.*', 'albums.release_date as release_date', 'genres.genre_title as genre', 
                    'countries.country_name as country', 'users.name as seller_name')
                    ->get();
            } else {
                $items = DB::table('items')
                ->join('sellers', 'items.seller_id', '=', 'sellers.id')
                ->join('users', 'sellers.user_id', '=', 'users.id')
                ->join('album_items', 'album_items.item_id', '=', 'items.id')
                ->join('albums', 'album_items.album_id', '=', 'albums.id')
                ->join('genres', 'albums.genre_id', '=', 'genres.id')
                ->join('countries', 'countries.id', '=', 'albums.country_id')
                ->where('items.category', 'album')
                ->select('items.*', 'albums.release_date as release_date', 'genres.genre_title as genre', 'countries.country_name as country', 'users.name as seller_name')
                ->get();
                return $items;
            }

            return response()->json($items);
        }

}
