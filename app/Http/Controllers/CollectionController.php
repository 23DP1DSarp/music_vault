<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Album;
use App\Models\Collection;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CollectionController extends Controller
{
    public function addToCollection(Album $album, Request $request) {

        $userId = $this->getUserId($request);
        if (!$userId) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $incomingFields['album_id'] = $album->id;
        $incomingFields['user_id'] = $userId;
        $incomingFields['added_at'] = now();

        Collection::create($incomingFields);

        return response()->json(['message' => 'Album added to collection']);
    }

    public function getCollection(Request $request) {
        $userId = $this->getUserId($request);
        if (!$userId) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $collections = DB::table('collections')
        ->where('user_id', '=', $userId)
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
        $userId = $this->getUserId($request);
        if (!$userId) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $albums = DB::table('collections')
            ->where('collections.user_id', '=', $userId)
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
