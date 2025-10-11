<?php

use App\Models\Album;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AlbumController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

Route::get('/', function () {
    $albums = Album::all();
    return view('main', ['albums' => $albums]);
});

Route::post('/register', [UserController::class, 'register']);

Route::post('/logout', [UserController::class, 'logout']);

Route::post('/login', [UserController::class, 'login']);

Route::get('/showlogin', function () {
    return view('login');
});

Route::get('/showsignup', function () {
    return view('signup');
});

Route::get('/showaddalbum', function() {
    return view('addalbum');
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


Route::post('/add_album', [AlbumController::class, 'addAlbum']);

Route::get('/show_albums', [AlbumController::class, 'filterGenre']);