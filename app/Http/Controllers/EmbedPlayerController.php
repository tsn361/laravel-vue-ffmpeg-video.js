<?php

namespace App\Http\Controllers;


use App\Models\Video;
use Illuminate\Http\Request;

class EmbedPlayerController extends Controller
{
    public function getEmbedPlayer($slug){
        $video = Video::where('slug', $slug)->first();
        if(!$video){
            return redirect()->route('notfound');
        }
        return view('video.embedPlayer', compact('video'));
    }
}