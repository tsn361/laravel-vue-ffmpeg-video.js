<?php

namespace App\Http\Controllers;


use App\Models\Video;
use App\Models\TmpTranscodeProgress;
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
use ProtoneMedia\LaravelFFMpeg\Exporters\HLSExporter;

use App\Jobs\VideoTranscode;


class VideoController extends Controller
{

    public function index(){
        $videos = Video::where('user_id', Auth::user()->id)->get();
        return view('video.index', compact('videos'));
    }

    public function upload_UI(){
        return view('video.upload');
    }

    public function video_play_UI(){
        $video = Video::where('slug', request()->slug)->first();
        return view('video.play', compact('video'));
    }

    public function videoTranscodeStatus($id){
        $video = Video::where('id',$id)->where('is_transcoded',0)->first();
        
        if($video){
            $array = array(0 => '1080', 1 => '720', 2 => '480', 3 => '360', 4 => '240');
            $key = array_search($video->original_resolution, $array);
            $newArray = array_slice($array, $key);
            sort($newArray);
            
            return view('video.transcodeStatus')->with('video', $video)->with('newArray', $newArray);
        }else{
            return redirect()->route('video.index');
        }
        
    }

    public function fileUploadPost(Request $request)
    {

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
        $video->origianl_file_url =  $request->fileNameWithExt;
        $video->playback_url =  'master.m3u8';
        $video->user_id = Auth::user()->id;
        $video->video_duration = $durationInSeconds;
        $video->original_filesize = $original_filesize;
        $video->original_resolution = $original_resolution;
        $video->original_bitrate = $bitrate;
        $video->original_video_codec = $codec;
        $video->file_name = $request->fileName;
        $video->is_transcoded = 0;
        $video->upload_duration = $request->uploadDuration;
        
       
        if($video->save()){
            $this->createTmpTranscodeEntry($original_resolution, $request->fileName, $video->id);
            return response()->json(['success'=>'true', 'lastInsertedId'=>$video->id]);
        }else{
            return response()->json(['success'=>'false', 'message'=>'Error saving video']);
        }
        
    }

    public function transcode(Request $request){
        dispatch(new VideoTranscode($request->id));

        return response()->json(['success'=>'true']);

        $video = Video::where('id',$request->id)->where('is_transcoded',0)->first();
        
        if($video){
            $array = array(0 => '1080', 1 => '720', 2 => '480', 3 => '360', 4 => '240');
            $key = array_search($video->original_resolution, $array);
            $newArray = array_slice($array, $key);
            sort($newArray);

            
            $path = $video->user_id.'/'.$video->file_name.'/'.$video->origianl_file_url;
            $Keypath = $video->user_id.'/'.$video->file_name;
            $masetPath = $video->user_id.'/'.$video->file_name.'/master.m3u8';

            $p240 = (new X264)->setKiloBitrate(350);
            $p360 = (new X264)->setKiloBitrate(800);
            $p480 = (new X264)->setKiloBitrate(1200);
            $p720 = (new X264)->setKiloBitrate(1900);
            $p1080 = (new X264)->setKiloBitrate(4000);

            $processOutput =  FFMpeg::fromDisk('uploads')->open($path)
                        ->exportForHLS()
                        ->setSegmentLength(20) // optional
                        ->withRotatingEncryptionKey(function ($filename, $contents) use($Keypath){
                            Storage::disk('uploads')->put("{$Keypath}/$filename", $contents);
                        });
                        
                foreach($newArray as $key => $value){
                    
                    if($value == '240'){
                        $processOutput->addFormat($p240, function($media) {
                            $media->scale(426, 240);
                        });
                    }else if($value == '360'){
                        $processOutput->addFormat($p360, function($media) {
                            $media->scale(640, 360);
                        });
                    }else if($value == '480'){
                        $processOutput->addFormat($p480, function($media) {
                            $media->scale(854, 480);
                        });
                    }else if($value == '720'){
                        $processOutput->addFormat($p720, function($media) {
                            $media->scale(1280, 720);
                        });
                    }else if($value == '1080'){
                        $processOutput->addFormat($p1080, function($media) {
                            $media->scale(1920, 1080);
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
                })
                ->onProgress(function ($percentage) use($video,$newArray) {
                    // echo "percent: {$percentage} %\n";
                    \Log::info("percent: {$percentage} %\n");
                    if ($percentage == 100) {
                        $this->updateTranscodeStatus($percentage, 1, $video->file_name,$newArray);
                    }else{
                        $this->updateTranscodeStatus($percentage, 0, $video->file_name,$newArray);
                    }
                })->save($masetPath)->cleanupTemporaryFiles();

                $this->updateVideoStatus($video->file_name,1,1);
                return response()->json(['success'=>'true']);
        }else{
            $this->deleteTranscodeStatus($video->file_name);
            return response()->json(['success'=>'false', 'message'=>'Video already transcoded']);
        }
    }       


    public function updateTranscodeStatus($progress, $is_complete, $file_name,$fileFormatArray){
        $lastFormat = last($fileFormatArray);
        foreach($fileFormatArray as $key => $format) {
            if($format == '240'){
                if($lastFormat == '240'){
                    $newProgress = $progress;
                }else{
                    $newProgress = ($progress + 20)  >= 99 ? 100 : ($progress + 20);
                }
                $query = TmpTranscodeProgress::where('file_name', $file_name)->where('file_format', $format)->update(['progress' => $newProgress, 'is_complete'=>$is_complete]);
            }elseif($format == '360'){
                if($lastFormat == '360'){
                    $newProgress = $progress;
                }else{
                    $newProgress = ($progress + 10) >= 99 ? 100 : ($progress + 10);
                }
                $query = TmpTranscodeProgress::where('file_name', $file_name)->where('file_format', $format)->update(['progress' => $newProgress, 'is_complete'=>$is_complete]);
            }elseif($format == '480'){
                if($lastFormat == '480'){
                    $newProgress = $progress;
                }else{
                    $newProgress = ($progress + 5) >= 99 ? 100 : ($progress + 5);
                }
                $query = TmpTranscodeProgress::where('file_name', $file_name)->where('file_format', $format)->update(['progress' => $newProgress, 'is_complete'=>$is_complete]);
            }elseif($format == '720'){
                if($lastFormat == '720'){
                    $newProgress = $progress;
                }else{
                    $newProgress = ($progress + 2) >= 99 ? 100 : ($progress + 2);
                }
                $query = TmpTranscodeProgress::where('file_name', $file_name)->where('file_format', $format)->update(['progress' => $newProgress, 'is_complete'=>$is_complete]);
            }elseif($format == '1080'){
                $query = TmpTranscodeProgress::where('file_name', $file_name)->where('file_format', $format)->update(['progress' => $newProgress, 'is_complete'=>$is_complete]);
            }
        }
    }
    public function createTmpTranscodeEntry($original_resolution, $file_name, $video_id){
        $array = array(0 => '1080', 1 => '720', 2 => '480', 3 => '360', 4 => '240');
        $key = array_search($original_resolution, $array);
        $newArray = array_slice($array, $key);
        sort($newArray);
        foreach($newArray as $key => $format){
            $newUser = TmpTranscodeProgress::updateOrCreate([
                'file_name'   => $file_name,
                'video_id'    => $video_id,
                'file_format' => $format
            ],[
                'progress'     => 0,
            ]);
        }
    }

    public function updateVideoStatus($file_name,$status,$is_transcoded){
        $query = Video::where('file_name', $file_name)->update(['status' => $status, 'is_transcoded'=> $is_transcoded ]);
        if ($query) {
            $this->deleteTranscodeStatus($file_name);
        }
    }

    public function deleteTranscodeStatus($file_name){
        
        $query = TmpTranscodeProgress::where('file_name', $file_name)->delete();
       
    }

    public function getTranscodeProgress($video_id){
        $data = TmpTranscodeProgress::where('video_id', $video_id)->where('is_complete', 0)->get();
        if(count($data) > 0){
            return response()->json($data);
        }else{
            return response()->json([]);
        }
    }


    public function setcookie2(){
        $anotherArray = array(0 => '1080', 1 => '720', 2 => '480', 3 => '360', 4 => '240');
        sort($anotherArray);
        $lastElement = last($anotherArray);
        return response()->json($lastElement);
    }
}