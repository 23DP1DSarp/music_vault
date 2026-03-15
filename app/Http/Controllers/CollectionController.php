<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Album;
use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CollectionController extends Controller
{
    public function addToCollection(Album $album, Request $request) {

        $incomingFields['album_id'] = $album->id;
        

        Collection::create($incomingFields);

        return redirect('/');
    }

    public function getCollection(Request $request) {
        $collections = DB::table('collections')
        ->where('user_id', '=', $request->user()->id)
        ->join('albums', 'albums.id', '=', 'collections.album_id')
        ->join('genres', 'albums.genre_id', '=', 'genres.id')
        ->join('users', 'users.id', '=', 'collections.user_id')
        ->select(
            'collections.*',
            'albums.title as title',
            'albums.cover as cover',
            'albums.author as author',
            'genres.genre_title as genre',
            'albums.release_date as release_date',
        )
        ->get();
        
        return response()->json($collections);
    }

    public function orderCollectionAlbums(Request $request) {
        $sortBy = $request->input('sortBy');
        $order = $request->input('sortOrder');
        $albums = DB::table('collections')
            ->where('collections.user_id', '=', $request->user()->id)
            ->join('albums', 'collections.album_id', '=', 'albums.id')
            ->join('genres', 'albums.genre_id', '=', 'genres.id')
            ->select(
                'albums.*',
                'genres.genre_title as genre',
            )
            ->orderBy($sortBy, $order)
            ->get();

        return response()->json($albums);
    }
}
