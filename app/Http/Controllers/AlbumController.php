<?php

namespace App\Http\Controllers;

use App\Models\Album;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AlbumController extends Controller
{
    public function addAlbum(Request $request) {
        $incomingFields = $request->validate([
            'title' => 'required',
            'author' => 'required',
            'genre_id' => 'required',
            'label' => 'required',
            'release_date' => 'required',
            'country_id' => 'required',
            'cover' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'format' => 'nullable',
            'notes' => 'nullable',
            'tracks' => 'required|array|min:1',
        ]);

        if ($request->hasFile('cover')) {
            $incomingFields['cover'] = $request->file('cover')->store('images', 'public');
        }

        $incomingFields['title'] = strip_tags($incomingFields['title']);
        $incomingFields['author'] = strip_tags($incomingFields['author']);
        $incomingFields['genre_id'] = strip_tags($incomingFields['genre_id']);

        $album = Album::create($incomingFields);

        foreach ($incomingFields['tracks'] as $data) {
        if ($data['position'] !== null && $data['song_title'] !== null && $data['artist'] !== null && $data['duration'] !== null) {
            $album->tracks()->create([
                'position' => $data['position'],
                'artist' => $data['artist'],
                'song_title' => $data['song_title'],
                'duration' => $data['duration'],
            ]);
        }
            
        }
        return redirect('/');
    }

    public function filterAlbums(Request $request)
    {
        $genres = $request->input('genres', []);
        $countries = $request->input('countries', []);
        $decades = $request->input('decades', []);

        if (!empty($genres) || !empty($countries) || !empty($decades)) {
            $albums = DB::table('albums')
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
                ->join('genres', 'genres.id', '=', 'albums.genre_id')
                ->join('countries', 'countries.id', '=', 'albums.country_id')
                ->select(
                    'albums.*',
                    'genres.genre_title as genre',
                    'countries.country_name as country',
                )
                ->get();
        } else {
            $albums = DB::table('albums')
                ->join('genres', 'genres.id', '=', 'albums.genre_id')
                ->join('countries', 'countries.id', '=', 'albums.country_id')
                ->select(
                    'albums.*',
                    'genres.genre_title as genre',
                    'countries.country_name as country',
                )
                ->get();
        }

        return response()->json($albums);
    }
/*
    public function getCountries(Request $request)
    {
        $countries = $request->input('countries', []);

        if (empty($countries)) {
            $albums = Album::query()
            ->when(!empty($countries), function ($query) use ($countries) {
                $query->whereIn('country', $countries);
            })
            ->get();
        } else {
            $albums = Album::all();
        }
       
        return response()->json($albums);
    }

    public function getDecades(Request $request)
    {
        $decades = $request->input('decades', []);

        if (empty($decades)) {
            $albums = DB::table('albums')
                ->whereIn(DB::raw('YEAR(albums.release_date)'), $decades)
                ->join('genres', 'genres.id', '=', 'albums.genre_id')
                ->select(
                    'albums.*',
                    'genres.genre_title as genre',
                )
                ->get();
        } else {
            $albums = Album::all();
        }
        return response()->json($albums);
    }
*/
     
}
