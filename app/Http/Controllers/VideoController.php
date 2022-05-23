<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\Videos;
use Auth;
use Cviebrock\EloquentSluggable\Services\SlugService;

use FFMpeg\FFProbe;
use FFMpeg;
use FFMpeg\Coordinate\Dimension;

class VideoController extends Controller
{
    
    public function index(){
        return view('video.index');
    }

    public function upload_UI(){
        return view('video.upload');
    }

    public function videoTranscodeStatus($id){
        $video = Videos::find($id);
        
        if($video){
            $array = array(0 => '1080', 1 => '720', 2 => '480', 3 => '360', 4 => '240');
            $key = array_search($video->original_resolution, $array);
            $newArray = array_slice($array, $key);
            return view('video.transcodeStatus', compact('newArray'));
        }else{
            return redirect()->route('video.index');
        }
        
    }

    public function fileUploadPost(Request $request)
    {
        // dd($request->file('file'));
        $request->validate([
        'file' => 'required|mimes:mp4,ogx,oga,ogv,ogg,webm'
        ]);
        $file = $request->file('file');
        if($request->file()) {
            $fileName = time().'.'.request()->file->getClientOriginalExtension();
            $save_path = Auth::user()->id;
            $request->file('file')->storeAs($save_path, $fileName,'uploads');
            // request()->file->move(public_path('files'), $fileName);
    
            return response()->json(['success'=>'true', 'fileName'=>$fileName, 'filePath'=>$save_path]);
        
        }
    }

    public function saveVideoInfo(Request $request){
        $request->validate([
            'title' => 'required',
        ]);

        $path = Auth::user()->id.'/'.$request->fileName;
        $media = FFMpeg::fromDisk('uploads')->open($path);
        $durationInSeconds = $media->getDurationInSeconds(); // returns an integer
        $bitrate = $media->getVideoStream()->get('bit_rate'); // returns an integer
        $codec = $media->getVideoStream()->get('codec_name'); // returns a string
        $original_filesize = $size = Storage::disk('uploads')->size($path);
        $original_resolution = $media->getVideoStream()->get('height'); // returns an array




        $video = new Videos();
        $video->title = $request->title;
        $video->slug = SlugService::createSlug(Videos::class, 'slug', $request->title);
        $video->description = $request->description;
        $video->playback_url =  $request->fileName;
        $video->uploaded_by = Auth::user()->id;
        $video->video_duration = $durationInSeconds;
        $video->original_filesize = $original_filesize;
        $video->original_resolution = $original_resolution;
        $video->original_bitrate = $bitrate;
        $video->original_video_codec = $codec;
        
       
        if($video->save()){
            return response()->json(['success'=>'true', 'lastInsertedId'=>$video->id]);
        }else{
            return response()->json(['success'=>'false', 'message'=>'Error saving video']);
        }
        
    }
}