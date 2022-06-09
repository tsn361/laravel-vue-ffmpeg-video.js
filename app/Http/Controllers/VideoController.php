<?php

namespace App\Http\Controllers;


use App\Models\Video;
use App\Models\TmpTranscodeProgress;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
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
        $videos = Video::where('user_id', Auth::user()->id)
                    ->orderBy('id', 'DESC')
                    ->paginate(4);
        return view('video.index', compact('videos'));
    }

    public function GetSearchVideoData(Request $request){
        $videos = Video::where('user_id', Auth::user()->id);
        if($request->get('search') != 'all' && $request->get('search') != ''){
            $videos = $videos->where('title', 'like', '%'.$request->get('search').'%');
        }
        $videos = $videos->orderBy('id', 'DESC')
                    ->paginate(4);
        return view('video.videoListSearchData', compact('videos'));
    }

    public function upload_UI(){
        return view('video.upload');
    }

    public function video_play_UI(){
        $video = Video::where('slug', request()->slug)
        ->where('status', 1)
        ->first();

        //empty check and redirect to 404 page
        if(!$video){
            return view('video.404');
        }

        return view('video.play', compact('video'));
    }

    public function edit_ui(){
        $video = Video::where('slug', request()->slug)->first();
        return view('video.edit', compact('video'));
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

    public function fileUploadPost(Request $request){

        $allowed_file_types = ['mp4', 'webm', 'mkv', 'wmv', 'avi', 'avchd','flv', 'ts', 'mov'];
        $file = $request->file('file');
        $isValid = in_array(request()->file->getClientOriginalExtension(), $allowed_file_types);

        if($request->file() && $isValid) {

            $fileName = time();
            $filePath = $fileName.'.'.request()->file->getClientOriginalExtension();
            $save_path = Auth::user()->id.'/'.$fileName;

            //--old $request->file('file')->storeAs($save_path, $fileName,'uploads');
            request()->file->move(public_path('uploads/'.$save_path), $filePath);
            return response()->json(['success'=>'true', 'fileName'=>$fileName, 'filePath'=>$filePath]);
        }else{
            return response()->json(['success'=>'false', 'Fail upload failed']);
        }
    }

    public function updateVideoInfo(Request $request){
        $video = Video::where('slug', request()->slug)->first();

        $video->title = $request->title;
        $video->description = $request->description;
        $video->allow_hosts = $request->allow_host;
        $video->skip_intro_time = $request->skip_intro_time;
        
        if($video->save()){
            return response()->json(['success'=>'true', 'videoId'=>$video->id]);
        }else{
            return response()->json(['success'=>'false', 'message'=>'Error saving video']);
        }
    }

    public function saveVideoInfo(Request $request){
        $request->validate([
            'title' => 'required'
        ]);
        

        $path = Auth::user()->id.'/'.$request->fileName.'/'.$request->fileNameWithExt;
        $media = FFMpeg::fromDisk('uploads')->open($path);
        $durationInSeconds = $media->getDurationInSeconds(); // returns an integer
        $codec = $media->getVideoStream()->get('codec_name'); // returns a string
        $original_resolution = $media->getVideoStream()->get('height'); // returns an array
        $bitrate = $media->getVideoStream()->get('bit_rate'); // returns an integer

        $original_filesize = $size = Storage::disk('uploads')->size($path);

        $posterImage = null;
        if($request->hasFile('poster')) {
            // Process the new image
            $fileName = 'poster.'.request()->file('poster')->getClientOriginalExtension();
            $save_path = Auth::user()->id.'/'.$request->fileName;
            request()->file('poster')->move(public_path('uploads/'.$save_path), $fileName);
            $posterImage = $fileName;
        }else{
            $media->getFrameFromSeconds(8)
            ->export()
            ->save(Auth::user()->id.'/'.$request->fileName.'/'.'poster.png');
            $posterImage = 'poster.png';
        }

        $video = new Video();
        $video->title = $request->title;
        $video->slug = SlugService::createSlug(Video::class, 'slug', $request->title);
        $video->description = $request->description;
        $video->poster = $posterImage;
        $video->origianl_file_url =  $request->fileNameWithExt;
        $video->playback_url =  'master.m3u8';
        $video->user_id = Auth::user()->id;
        $video->video_duration = $durationInSeconds;
        $video->original_filesize = $original_filesize;
        $video->original_resolution = $original_resolution;
        $video->original_bitrate = $bitrate ? $bitrate : rand(1,600000);
        $video->original_video_codec = $codec;
        $video->file_name = $request->fileName;
        $video->is_transcoded = 0;
        $video->upload_duration = $request->uploadDuration  > 4 ? $request->uploadDuration : 10;
        
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
                'progress'     => 1,
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

    public function getAESKey(Request $request, $userid,$filename,$key){
        $Keypath = $userid.'/'.$filename.'/'.$key;
        //\Log::info("request => { $request->headers() } \n");
        \Log::info("secret => {$Keypath} \n");

        if (Storage::disk('uploads')->exists($Keypath)) {
            
            \Log::info("File exit \n");
            $contents = Storage::disk('uploads')->get($Keypath);
            \Log::info("File content: $contents \n");

            return $contents;
        }
        //return Storage::disk('uploads')->download($Keypath);
        return null;
    }


    public function videoDelete($slug){
        $video = Video::where('slug', $slug)->first();
        if ($video->delete()) {
            File::deleteDirectory(public_path('uploads/'.$video->user_id.'/'.$video->file_name));
        }
        return response()->json(['success'=>'true']);
    }


    public function test(){
        $ffprobe = '/usr/bin/ffprobe';
        $videoFile = '/var/www/html/upwork/laravel-vue-ffmpeg-video.js/public/uploads/3/1654623378/1654623378.mp4';
        $cmd = shell_exec($ffprobe .' -v quiet -print_format json -select_streams v:0  -show_streams "'.$videoFile.'"');
        $parsed = json_decode($cmd, true);
        $bitrate = @$parsed['streams'][0]['bit_rate'];
        $duration = @$parsed['streams'][0]['duration'];
        return response()->json($parsed['streams'][0]);
    }
}