<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Seller;
use App\Models\User;
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
        ->select('items.*', 'albums.release_date as release_date', 'genres.genre_title as genre', 'countries.country_name as country', 'users.username as seller_name')
        ->get();
        return $items;
    }

    public function getAllInstrumentItems() {
        $items = DB::table('items')
        ->join('sellers', 'items.seller_id', '=', 'sellers.id')
        ->join('users', 'sellers.user_id', '=', 'users.id')
        ->join('instruments', 'instruments.item_id', '=', 'items.id')
        ->join('countries', 'countries.id', '=', 'users.country_id')
        ->where('items.category', 'instrument')
        ->select('items.*', DB::raw('DATE(items.created_at) as release_date'), DB::raw('"" as genre'), 'countries.country_name as country', 'users.username as seller_name')
        ->get();
        return $items;
    }

    public function getAllServiceItems() {
        $items = DB::table('items')
        ->join('sellers', 'items.seller_id', '=', 'sellers.id')
        ->join('users', 'sellers.user_id', '=', 'users.id')
        ->join('services', 'services.item_id', '=', 'items.id')
        ->join('countries', 'countries.id', '=', 'users.country_id')
        ->where('items.category', 'service')
        ->select('items.*', DB::raw('DATE(items.created_at) as release_date'), DB::raw('"" as genre'), 'countries.country_name as country', 'users.username as seller_name')
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
        ->select('items.*', 'users.username as seller_name', 'sellers.full_name as sellers_full_name', 'albums.id as album_id', 'countries.id as shipping_country_id', 'countries.country_name as shipping_country', 'sellers.shipping_address as origin_address', DB::raw('DATE(items.created_at) as created_at'))
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
                    ->when(($minPrice != null && $maxPrice != null) && ($minPrice >= 0 && $maxPrice > 0), function ($query) use ($minPrice, $maxPrice) {
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
                    'countries.country_name as country', 'users.username as seller_name')
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

        public function filterInstrumentItems(Request $request)
        {
            $countries = $request->input('countries', []);
            $decades = $request->input('decades', []);
            $minPrice = $request['minPrice'];
            $maxPrice = $request['maxPrice'];

            $items = DB::table('items')
                ->when(!empty($countries), function ($query) use ($countries) {
                    $query->whereIn('countries.country_name', $countries);
                })
                ->when(!empty($decades), function ($query) use ($decades) {
                    $query->whereBetween(DB::raw('YEAR(items.created_at)'), [$decades[0], $decades[0] + 9]);
                    if (count($decades) > 1) {
                        for ($i=1; $i < count($decades); $i++) {
                            $query->orWhereBetween(DB::raw('YEAR(items.created_at)'), [$decades[$i], $decades[$i] + 9]);
                        }
                    }
                })
                ->when($minPrice != null && $maxPrice != null, function ($query) use ($minPrice, $maxPrice) {
                    $query->whereBetween('items.price', [$minPrice, $maxPrice]);
                })
                ->join('sellers', 'items.seller_id', '=', 'sellers.id')
                ->join('users', 'sellers.user_id', '=', 'users.id')
                ->join('instruments', 'instruments.item_id', '=', 'items.id')
                ->join('countries', 'countries.id', '=', 'users.country_id')
                ->where('items.category', 'instrument')
                ->select('items.*', DB::raw('DATE(items.created_at) as release_date'), DB::raw('"" as genre'), 'countries.country_name as country', 'users.username as seller_name')
                ->get();

            return response()->json($items);
        }

        public function filterServiceItems(Request $request)
        {
            $countries = $request->input('countries', []);
            $decades = $request->input('decades', []);
            $minPrice = $request['minPrice'];
            $maxPrice = $request['maxPrice'];

            $items = DB::table('items')
                ->when(!empty($countries), function ($query) use ($countries) {
                    $query->whereIn('countries.country_name', $countries);
                })
                ->when(!empty($decades), function ($query) use ($decades) {
                    $query->whereBetween(DB::raw('YEAR(items.created_at)'), [$decades[0], $decades[0] + 9]);
                    if (count($decades) > 1) {
                        for ($i=1; $i < count($decades); $i++) {
                            $query->orWhereBetween(DB::raw('YEAR(items.created_at)'), [$decades[$i], $decades[$i] + 9]);
                        }
                    }
                })
                ->when($minPrice != null && $maxPrice != null, function ($query) use ($minPrice, $maxPrice) {
                    $query->whereBetween('items.price', [$minPrice, $maxPrice]);
                })
                ->join('sellers', 'items.seller_id', '=', 'sellers.id')
                ->join('users', 'sellers.user_id', '=', 'users.id')
                ->join('services', 'services.item_id', '=', 'items.id')
                ->join('countries', 'countries.id', '=', 'users.country_id')
                ->where('items.category', 'service')
                ->select('items.*', DB::raw('DATE(items.created_at) as release_date'), DB::raw('"" as genre'), 'countries.country_name as country', 'users.username as seller_name')
                ->get();

            return response()->json($items);
        }

        public function orderInstruments(Request $request)
        {
            $sortBy = $request->input('sortBy');
            $order = $request->input('sortOrder');
            $countries = $request->input('countries', []);
            $decades = $request->input('decades', []);

            $items = DB::table('items')
                ->when(!empty($countries), function ($query) use ($countries) {
                    $query->whereIn('countries.country_name', $countries);
                })
                ->when(!empty($decades), function ($query) use ($decades) {
                    $query->whereBetween(DB::raw('YEAR(items.created_at)'), [$decades[0], $decades[count($decades) - 1] + 9]);
                })
                ->join('sellers', 'items.seller_id', '=', 'sellers.id')
                ->join('users', 'sellers.user_id', '=', 'users.id')
                ->join('instruments', 'instruments.item_id', '=', 'items.id')
                ->join('countries', 'countries.id', '=', 'users.country_id')
                ->where('items.category', 'instrument')
                ->select('items.*', DB::raw('DATE(items.created_at) as release_date'), DB::raw('"" as genre'), 'countries.country_name as country', 'users.username as seller_name')
                ->orderBy($sortBy, $order)
                ->get();

            return response()->json($items);
        }

        public function orderServices(Request $request)
        {
            $sortBy = $request->input('sortBy');
            $order = $request->input('sortOrder');
            $countries = $request->input('countries', []);
            $decades = $request->input('decades', []);

            $items = DB::table('items')
                ->when(!empty($countries), function ($query) use ($countries) {
                    $query->whereIn('countries.country_name', $countries);
                })
                ->when(!empty($decades), function ($query) use ($decades) {
                    $query->whereBetween(DB::raw('YEAR(items.created_at)'), [$decades[0], $decades[count($decades) - 1] + 9]);
                })
                ->join('sellers', 'items.seller_id', '=', 'sellers.id')
                ->join('users', 'sellers.user_id', '=', 'users.id')
                ->join('services', 'services.item_id', '=', 'items.id')
                ->join('countries', 'countries.id', '=', 'users.country_id')
                ->where('items.category', 'service')
                ->select('items.*', DB::raw('DATE(items.created_at) as release_date'), DB::raw('"" as genre'), 'countries.country_name as country', 'users.username as seller_name')
                ->orderBy($sortBy, $order)
                ->get();

            return response()->json($items);
        }

        public function getItem(Item $item)
        {
            $category = $item->category;
            if ($category === 'album') {
                return $this->getAlbumItem($item);
            } elseif ($category === 'instrument') {
                $result = DB::table('items')
                    ->join('sellers', 'items.seller_id', '=', 'sellers.id')
                    ->join('users', 'sellers.user_id', '=', 'users.id')
                    ->join('countries', 'countries.id', '=', 'users.country_id')
                    ->join('instruments', 'instruments.item_id', '=', 'items.id')
                    ->where('items.id', '=', $item->id)
                    ->where('items.category', 'instrument')
                    ->select('items.*', 'users.username as seller_name', 'sellers.full_name as sellers_full_name', 'countries.id as shipping_country_id', 'countries.country_name as shipping_country', 'sellers.shipping_address as origin_address', DB::raw('DATE(items.created_at) as created_at'))
                    ->get();
                return $result;
            } elseif ($category === 'service') {
                $result = DB::table('items')
                    ->join('sellers', 'items.seller_id', '=', 'sellers.id')
                    ->join('users', 'sellers.user_id', '=', 'users.id')
                    ->join('countries', 'countries.id', '=', 'users.country_id')
                    ->join('services', 'services.item_id', '=', 'items.id')
                    ->where('items.id', '=', $item->id)
                    ->where('items.category', 'service')
                    ->select('items.*', 'users.username as seller_name', 'sellers.full_name as sellers_full_name', 'countries.id as shipping_country_id', 'countries.country_name as shipping_country', 'sellers.shipping_address as origin_address', DB::raw('DATE(items.created_at) as created_at'))
                    ->get();
                return $result;
            }

            return response()->json(['error' => 'Item not found'], 404);
        }

        public function isAddedToWishlist(Request $request, Item $item) {
            $userId = $this->getUserId($request);
            if (!$userId) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
    
            $exists = DB::table('wishlist')
                ->where('user_id', $userId)
                ->where('item_id', $item->id)
                ->exists();
    
            return response()->json(['added_to_wishlist' => $exists]);

        }

        public function addToWishlist(Request $request, Item $item) {
            $userId = $this->getUserId($request);
            if (!$userId) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            DB::table('wishlist')->insert([
                'user_id' => $userId,
                'item_id' => $item->id,
                'date_added' => now(),
            ]);

            return response()->json(['message' => 'Item added to wishlist']);
        }

        public function deleteFromWishlist(Request $request, Item $item) {
            $userId = $this->getUserId($request);
            if (!$userId) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            DB::table('wishlist')
                ->where('user_id', $userId)
                ->where('item_id', $item->id)
                ->delete();

            return response()->json(['message' => 'Item removed from wishlist']);

        }

        private function getUserId(Request $request){
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

        public function getWishlist(Request $request) {
            $userId = $this->getUserId($request);
            if (!$userId) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $items = DB::table('wishlist')
                ->where('wishlist.user_id', '=', $userId)
                ->join('items', 'wishlist.item_id', '=', 'items.id')
                ->join('sellers', 'items.seller_id', '=', 'sellers.id')
                ->join('users', 'sellers.user_id', '=', 'users.id')
                ->select('items.*', 'users.username as seller_name')
                ->get();

            return response()->json($items);
        } 

}