<?php

use App\Http\Controllers\AlbumController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\UserController;
use App\Models\Album;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $albums = Album::all();
    return view('main', ['albums' => $albums]);
});

Route::get('/getalbums', function () {
    return Album::all();
});


// Authentication routes moved to api.php

Route::get('/showlogin', function () {
    return view('login');
});

Route::get('/showsignup', function () {
    return view('signup');
});

Route::get('/showaddalbum', function() {
    return view('addalbum');
});

Route::get('/showcatalog', function () {
    $albums = Album::all();
    return view('catalog', ['albums' => $albums]);
});

Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
 
    return redirect('/');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
 
    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');


Route::post('/add_album_with_tracks', [AlbumController::class, 'addAlbum'])->name('add.album');

Route::get('/show_albums', [AlbumController::class, 'filterGenre']);

Route::get('/showprofile', function() {
    $collections = DB::table('collections')
    ->where('user_id', '=', auth()->id())
    ->join('albums', 'albums.id', '=', 'collections.album_id')
    ->join('users', 'users.id', '=', 'collections.user_id')
    ->select(
        'collections.*',
        'albums.title as title',
        'albums.cover as cover',
        'albums.author as author',
        'albums.genre as genre',
        'albums.release_date as release_date',
    )
    ->get();
    return view('userprofile', ['collections' => $collections]);
});


Route::post('/add_to_collection/{album}', [CollectionController::class, 'addToCollection']);

Route::get('/album_info/{album}', function(Album $album) {
    $album = DB::table('albums')
    ->where('id', '=', $album->id)
    ->first();

    $tracks = DB::table('tracks')
    ->where('album_id', '=', $album->id)
    ->get();
    return view('albuminfo', ['album' => $album, 'tracks' => $tracks]);
});


Route::get('/albumoffers/{album}', function(Album $album) {
    $album = DB::table('albums')
    ->where('id', '=', $album->id)
    ->first();

    $collections = DB::table('collections')
    ->where('user_id', '=', auth()->id())
    ->join('albums', 'albums.id', '=', 'collections.album_id')
    ->join('users', 'users.id', '=', 'collections.user_id')
    ->select(
        'collections.*',
        'albums.title as title',
        'albums.cover as cover',
        'albums.author as author',
        'albums.genre as genre',
        'albums.release_date as release_date',
    )
    ->get();

    return view('albumoffers', ['album' => $album, 'collections' => $collections]);
});


Route::get('/showsellersform', function () {
    return view('sellersform');
});