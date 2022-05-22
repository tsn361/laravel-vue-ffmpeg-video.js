<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VideoController extends Controller
{
    
    public function index(){
        return view('video.index');
    }

    public function upload_UI(){
        return view('video.upload');
    }
}
