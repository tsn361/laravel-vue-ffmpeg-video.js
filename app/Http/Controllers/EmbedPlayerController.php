<?php

namespace App\Http\Controllers;


use App\Models\Video;
use Illuminate\Http\Request;

class EmbedPlayerController extends Controller
{
    public function getEmbedPlayer($file_name){
        $video = Video::where('file_name', $file_name)->first();
        if(!$video){
            return redirect()->route('notfound');
        }
        return view('video.embedPlayer', compact('video'));
    }
}