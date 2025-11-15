<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Collection;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CollectionController extends Controller
{
    public function addToCollection(Album $album, Request $request) {

        $incomingFields['album_id'] = $album->id;
        $incomingFields['user_id'] = auth()->id();

        Collection::create($incomingFields);

        return redirect('/');
    }
}
