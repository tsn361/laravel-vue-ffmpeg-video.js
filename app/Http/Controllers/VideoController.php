<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Auth;
use Cviebrock\EloquentSluggable\Services\SlugService;

use FFMpeg\FFProbe;
use FFMpeg;
use FFMpeg\Coordinate\Dimension;
use FFMpeg\Format\Video\X264;
use FFMpeg\Format\ProgressListener\AbstractProgressListener;
use ProtoneMedia\LaravelFFMpeg\FFMpeg\ProgressListenerDecorator;
use FFMpeg\Format\FormatInterface;
use ProtoneMedia\LaravelFFMpeg\Exporters\HLSVideoFilters;

class VideoController extends Controller
{
    
    public function index(){
        $videos = Video::where('user_id', Auth::user()->id)->get();
        return view('video.index', compact('videos'));
    }

    public function upload_UI(){
        return view('video.upload');
    }

    public function videoTranscodeStatus($id){
        $video = Video::find($id);
        
        if($video){
            $array = array(0 => '1080', 1 => '720', 2 => '480', 3 => '360', 4 => '240');
            $key = array_search($video->original_resolution, $array);
            $newArray = array_slice($array, $key);
            return view('video.transcodeStatus')->with('video', $video)->with('newArray', $newArray);
        }else{
            return redirect()->route('video.index');
        }
        
    }

    public function fileUploadPost(Request $request)
    {
        //dd($request->file('file'));
        \Log::info("fileUploadPost => ". $request->file('file'));

        $request->validate([
        'file' => 'required|mimes:mp4,ogx,oga,ogv,ogg,webm'
        ]);

        $file = $request->file('file');
        if($request->file()) {

            //\Log::info("fileUploadPost =>yes ". request()->file->getClientOriginalExtension());

            $fileName = time();
            $filePath = $fileName.'.'.request()->file->getClientOriginalExtension();
            $save_path = Auth::user()->id.'/'.$fileName;

            // $request->file('file')->storeAs($save_path, $fileName,'uploads');
            request()->file->move(public_path('uploads/'.$save_path), $filePath);
    
            return response()->json(['success'=>'true', 'fileName'=>$fileName, 'filePath'=>$filePath]);
        
        }
    }

    public function saveVideoInfo(Request $request){
        $request->validate([
            'title' => 'required'
        ]);

        $path = Auth::user()->id.'/'.$request->fileName.'/'.$request->fileNameWithExt;
        $media = FFMpeg::fromDisk('uploads')->open($path);
        $durationInSeconds = $media->getDurationInSeconds(); // returns an integer
        $bitrate = $media->getVideoStream()->get('bit_rate'); // returns an integer
        $codec = $media->getVideoStream()->get('codec_name'); // returns a string
        $original_filesize = $size = Storage::disk('uploads')->size($path);
        $original_resolution = $media->getVideoStream()->get('height'); // returns an array

        $media->getFrameFromSeconds(10)
        ->export()
        ->save(Auth::user()->id.'/'.$request->fileName.'/'.'poster.png');




        $video = new Video();
        $video->title = $request->title;
        $video->slug = SlugService::createSlug(Video::class, 'slug', $request->title);
        $video->description = $request->description;
        $video->poster = 'poster.png';
        $video->playback_url =  $request->fileNameWithExt;
        $video->user_id = Auth::user()->id;
        $video->video_duration = $durationInSeconds;
        $video->original_filesize = $original_filesize;
        $video->original_resolution = $original_resolution;
        $video->original_bitrate = $bitrate;
        $video->original_video_codec = $codec;
        $video->file_name = $request->fileName;
        $video->is_transcoded = 0;
        
       
        if($video->save()){
            return response()->json(['success'=>'true', 'lastInsertedId'=>$video->id]);
        }else{
            return response()->json(['success'=>'false', 'message'=>'Error saving video']);
        }
        
    }

    public function transcode(Request $request){

        $video = Video::find($request->id);
        
        if($video){
            $array = array(0 => '1080', 1 => '720', 2 => '480', 3 => '360', 4 => '240');
            $key = array_search($video->original_resolution, $array);
            $newArray = array_slice($array, $key);
            sort($newArray);

            
            $path = $video->user_id.'/'.$video->file_name.'/'.$video->playback_url;
            $masetPath = $video->user_id.'/'.$video->file_name.'/master.m3u8';

            $p240 = (new X264)->setKiloBitrate(350);
            $p360 = (new X264)->setKiloBitrate(800);
            $p480 = (new X264)->setKiloBitrate(1200);
            $p720 = (new X264)->setKiloBitrate(1900);
            $p1080 = (new X264)->setKiloBitrate(4000);
            
            $processOutput =  FFMpeg::fromDisk('uploads')->open($path)
                        ->exportForHLS();
                        
                foreach($newArray as $key => $value){
                    
                    if($value == '240'){
                        $processOutput->addFormat($p240, function($media) {
                            $media->scale(426, 240);
                        })
                        ->onProgress(function ($percentage, $remaining, $value) {
                            echo "{$value} seconds left  percent: {$percentage} %\n";
                        });
                    }else if($value == '360'){
                        $processOutput->addFormat($p360, function($media) {
                            $media->scale(640, 360);
                        })
                        ->onProgress(function ($percentage, $remaining, $value) {
                            echo "{$value} seconds left  percent: {$percentage} %\n";
                        });
                    }else if($value == '480'){
                        $processOutput->addFormat($p480, function($media) {
                            $media->scale(854, 480);
                        })
                        ->onProgress(function ($percentage, $remaining, $value) {
                            echo "{$value} seconds left  percent: {$percentage} %\n";
                        });
                    }else if($value == '720'){
                        $processOutput->addFormat($p720, function($media) {
                            $media->scale(1280, 720);
                        })
                        ->onProgress(function ($percentage, $remaining, $value) {
                            echo "{$value} seconds left  percent: {$percentage} %\n";
                        });
                    }else if($value == '1080'){
                        $processOutput->addFormat($p1080, function($media) {
                            $media->scale(1920, 1080);
                        })
                        ->onProgress(function ($percentage, $remaining, $value) {
                            echo "{$value} seconds left  percent: {$percentage} %\n";
                        });
                    }
                    
                 }
                $processOutput->useSegmentFilenameGenerator(function ($name, $format, $key, callable $segments, callable $playlist) {

                if($format->getKiloBitrate() == 350){
                    $segments("{$name}-240-%03d.ts");
                    $playlist("{$name}-240.m3u8");
                }else if($format->getKiloBitrate() == 800){
                    $segments("{$name}-360-%03d.ts");
                    $playlist("{$name}-360.m3u8");
                }else if($format->getKiloBitrate() == 1200){
                    $segments("{$name}-480-%03d.ts");
                    $playlist("{$name}-480.m3u8");
                }else if($format->getKiloBitrate() == 1900){
                    $segments("{$name}-720-%03d.ts");
                    $playlist("{$name}-720.m3u8");
                }else if($format->getKiloBitrate() == 4000){
                    $segments("{$name}-720-%03d.ts");
                    $playlist("{$name}-1080.m3u8");
                }
                
            })->save($masetPath);
                return response()->json(['success'=>'true', 'message'=>$processOutput]);
        }
    }       
            
}