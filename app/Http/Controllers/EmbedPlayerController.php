<?php

namespace App\Http\Controllers;


use App\Models\Video;
use Illuminate\Http\Request;

class EmbedPlayerController extends Controller
{
    public function getEmbedPlayer($slug,$playerwidth,$playerheight){
        $video = Video::where('slug', $slug)->first();
        $playerWidth = isset($playerwidth) ? $playerwidth : '560';
        $playerHeight = isset($playerheight) ? $playerheight : '315';
        return view('video.embedPlayer', compact('video','playerWidth','playerHeight'));
    }
}