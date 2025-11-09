<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Track;
use Illuminate\Http\Request;

class AlbumController extends Controller
{
    public function addAlbum(Request $request) {
        $incomingFields = $request->validate([
            'title' => 'required',
            'author' => 'required',
            'genre' => 'required',
            'label' => 'nullable',
            'release_date' => 'required',
            'country' => 'nullable',
            'cover' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'format' => 'nullable',
            'notes' => 'nullable'

        ]);

       

        if ($request->hasFile('cover')) {
            $incomingFields['cover'] = $request->file('cover')->store('images', 'public');
        }

        $incomingFields['title'] = strip_tags($incomingFields['title']);
        $incomingFields['author'] = strip_tags($incomingFields['author']);
        $incomingFields['genre'] = strip_tags($incomingFields['genre']);

        Album::create($incomingFields);


       

    
        return redirect('/');
    }

    public function filterGenre(Request $request)
    {
        // Filter products by category if selected
        if ($request->filled('genre')) {
            $albums = Album::where('genre', $request->genre)->get();
        } else {
            $albums = Album::all();
        }

        return view('main', compact('albums'));
    }


     public function addTrack(Request $request) {


         $incomingFields2 = $request->validate([
            'tracks' => 'required|array|min:1',
            'tracks.*.position' => 'nullable',
            'tracks.*.artist' => 'nullable',
            'tracks.*.song_title' => 'nullable',
            'tracks.*.duration' => 'nullable'
        ]);


        foreach ($incomingFields2['tracks'] as $data) {
            Track::create([
                'position' => $data['position'],
                'artist' => $data['artist'],
                'song_title' => $data['song_title'],
                'duration' => $data['duration'],
            ]);
        }
        return redirect('/');
     }
}
