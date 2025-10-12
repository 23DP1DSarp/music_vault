<?php

namespace App\Http\Controllers;

use App\Models\Album;
use Illuminate\Http\Request;

class AlbumController extends Controller
{
    public function addAlbum(Request $request) {
        $incomingFields = $request->validate([
            'title' => 'required',
            'author' => 'required',
            'genre' => 'required',
            'year_of_release' => 'required|integer',
            'cover' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'track1' => 'required',
            'track2' => 'nullable',
            'track3' => 'nullable',
            'track4' => 'nullable',
            'track5' => 'nullable'

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
}
