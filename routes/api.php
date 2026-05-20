<?php

use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Http\Controllers\AlbumController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\UserController;
use App\Models\Album;
use App\Models\User;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

// Authentication endpoints (no middleware required)
Route::post('/register', [UserController::class, 'register'])->middleware('guest');
Route::post('/login', [UserController::class, 'login'])->middleware('guest');
Route::post('/add_to_collection/{album}', [CollectionController::class, 'addToCollection']);
Route::get('/getcollection', [CollectionController::class, 'getCollection']);
Route::get('/ordercollectionalbums', [CollectionController::class, 'orderCollectionAlbums']);

Route::get('/email/verify', function () {
    return 200;
})->middleware('check.api.token')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return response()->json(['message' => 'Email verified successfully']);
})->middleware('check.api.token')->name('verification.verify');
 
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
 
    return response()->json(['message' => 'Verification link sent!']);
})->middleware(['check.api.token', 'throttle:6,1'])->name('verification.send');

// Protected endpoints (require authentication token)
Route::middleware(['check.api.token'])->group(function () {
    Route::post('/logout', [UserController::class, 'logout']);
    Route::get('/user', [UserController::class, 'getCurrentUser']);
    
    // Collection routes
    
    
    // Seller routes
    Route::post('/createseller', [SellerController::class, 'createSeller']);
    
    // Item routes
    Route::post('/sellitem', [ItemController::class, 'sellItem']);
    
    // Album routes
    Route::post('/add_album_with_tracks', [AlbumController::class, 'addAlbum'])->name('add.album');
});


Route::get('/users', function(Request $request) {
    return User::all();
});

Route::get('/', function () {
    $album = DB::table('albums')
    ->join('genres', 'albums.genre_id', '=', 'genres.id')
    ->select(
        'albums.*',
        'genres.genre_title as genre',
    )
    ->get();
    return $album;
});

Route::get('/filter_albums', [AlbumController::class, 'filterAlbums']);

Route::get('/getgenres', function () {
    return DB::table('genres')
    ->select(
        'genres.*',
    )
    ->get();
});

Route::get('/getcountries', function () {
    return DB::table('countries')
    ->select(
        'countries.*',
    )
    ->get();
});

Route::get('/order_albums', function(Request $request) {
    $sortBy = $request->input('sortBy');
    $order = $request->input('sortOrder');
    $genres = $request->input('genres', []);
    $countries = $request->input('countries', []);
    $decades = $request->input('decades', []);
    $albums = DB::table('albums')
        ->when(!empty($genres), function ($query) use ($genres) {
            $query->whereIn('genres.genre_title', $genres);
        })
        ->when(!empty($countries), function ($query) use ($countries) {
            $query->whereIn('countries.country_name', $countries);
        })
        ->when(!empty($decades), function ($query) use ($decades) {
            $query->whereBetween(DB::raw('YEAR(albums.release_date)'), [$decades[0], $decades[count($decades) - 1] + 9]);
        })
        ->join('genres', 'albums.genre_id', '=', 'genres.id')
        ->join('countries', 'countries.id', '=', 'albums.country_id')
        ->select(
            'albums.*',
            'genres.genre_title as genre',
            'countries.country_name as country',
        )
        ->orderBy($sortBy, $order)
        ->get();

    return response()->json($albums);
});

Route::get('/order_instruments', [ItemController::class, 'orderInstruments']);
Route::get('/order_services', [ItemController::class, 'orderServices']);

Route::get('/album_info/{album}', function(Album $album) {
    $album = DB::table('albums')
    ->where('albums.id', '=', $album->id)
    ->join('genres', 'albums.genre_id', '=', 'genres.id')
    ->join('countries', 'countries.id', '=', 'albums.country_id')
    ->select(
        'albums.*',
        'genres.genre_title as genre',
        'countries.country_name as country',
    )
    ->first();

    $tracks = DB::table('tracks')
    ->where('album_id', '=', $album->id)
    ->get();
    return [$album, $tracks];
});
/*
Route::post('/email/verification-notification', function(Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return response()->json(['status' => 'verification-link-sent']);
})->middleware(['auth:sanctum', 'throttle:6,1']);

Route::get('/email/verify/{id}/{hash}', function ($id, $hash) {
    $user = User::findOrFail($id);

    if (! hash_equals((string) $hash, sha1($user->email))) {
        return response()->json(['message' => 'Invalid verification'], 403);
    }

    $user->markEmailAsVerified();
    return response()->json(['message' => 'Email verified!']);
});
*/
Route::middleware('check.api.token')->put('/change-user-info', function (
    Request $request,
    UpdateUserProfileInformation $updater
) {
    $updater->update($request->user(), $request->all());

    return response()->json([
        'message' => 'Profile updated successfully'
    ]);
});

Route::middleware('check.api.token')->put('/reset-password', function (
    Request $request,
    UpdateUserPassword $updater
) {
    $updater->update($request->user(), $request->all());

    return response()->json([
        'message' => 'Password updated successfully'
    ]);
});

Route::delete('/delete-account', [UserController::class, 'deleteUser'])->middleware('check.api.token');

Route::get('/get_album_items', [ItemController::class, 'getAllAlbumItems']);
Route::get('/get_instrument_items', [ItemController::class, 'getAllInstrumentItems']);
Route::get('/get_service_items', [ItemController::class, 'getAllServiceItems']);

Route::get('/get_album_item/{item}', [ItemController::class, 'getAlbumItem']);
Route::get('/get_item/{item}', [ItemController::class, 'getItem']);

Route::get('/filter_album_items', [ItemController::class, 'filterAlbumItems']);
Route::get('/filter_instrument_items', [ItemController::class, 'filterInstrumentItems']);
Route::get('/filter_service_items', [ItemController::class, 'filterServiceItems']);

Route::post('/create_order', [OrderController::class, 'checkAvaliabllity']);


Route::get('/get_currencies', function () {
    return DB::table('currencies')
    ->select(
        'currencies.*',
    )
    ->get();
});

Route::get('/getusercountry', [UserController::class, 'getUserCountry']);

Route::get('/is_added_to_collection/{album}', [CollectionController::class, 'isAddedToCollection']);

Route::delete('/delete_from_collection/{album}', [CollectionController::class, 'deleteFromCollection']);

Route::post('/add_to_wishlist/{item}', [ItemController::class, 'addToWishlist']);

Route::delete('/delete_from_wishlist/{item}', [ItemController::class, 'deleteFromWishlist']);

Route::get('/is_added_to_wishlist/{item}', [ItemController::class, 'isAddedToWishlist']);

Route::get('/getwishlist', [ItemController::class, 'getWishlist']);