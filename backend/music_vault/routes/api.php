<?php

use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Http\Controllers\AlbumController;
use App\Http\Controllers\CollectionController;
use App\Models\Album;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('/users', function(Request $request) {
    return User::all();
});

Route::post('/add_album_with_tracks', [AlbumController::class, 'addAlbum'])->name('add.album');

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

Route::post('/add_to_collection/{album}', [CollectionController::class, 'addToCollection']);

Route::get('/getcollection', [CollectionController::class, 'getCollection']);

Route::get('/ordercollectionalbums', [CollectionController::class, 'orderCollectionAlbums']);

Route::post('/createseller', [App\Http\Controllers\SellerController::class, 'createSeller']);

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

Route::middleware('auth:sanctum')->put('/change-user-info', function (
    Request $request,
    UpdateUserProfileInformation $updater
) {
    $updater->update($request->user(), $request->all());

    return response()->json([
        'message' => 'Profile updated successfully'
    ]);
});

Route::middleware('auth:sanctum')->put('/reset-password', function (
    Request $request,
    UpdateUserPassword $updater
) {
    $updater->update($request->user(), $request->all());

    return response()->json([
        'message' => 'Password updated successfully'
    ]);
});

Route::delete('/delete-account', function (Request $request) {
    $user = $request->user();
    $user->delete();
});