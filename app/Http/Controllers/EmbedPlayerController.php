<?php

namespace App\Http\Controllers;


use App\Models\Video;
use Illuminate\Http\Request;

class EmbedPlayerController extends Controller
{
    public function getEmbedPlayer(Request $request){
        $video = Video::where('slug', $request->slug)->first();
        return view('video.embedPlayer', compact('video'));
    }
}