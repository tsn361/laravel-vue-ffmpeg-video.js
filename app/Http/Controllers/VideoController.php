<?php

namespace App\Http\Controllers;

//call the controller you want to use its methods
use App\Http\Controllers\VideoObjectSchemaController;
use App\Models\Video;
use App\Models\TmpTranscodeProgress;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Auth;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Carbon\Carbon;
use Spatie\SchemaOrg\Schema;

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

        if( !empty(request()->get('v')) ){
            return $this->video_play_UI(request()->get('v'));
        }

        $videos = Video::where('user_id', Auth::user()->id)
                    ->orderBy('sequence', 'ASC')
                    ->paginate(4);
        return view('video.index', compact('videos'));
    }

    public function GetSearchVideoData(Request $request){
        $videos = Video::where('user_id', Auth::user()->id);
        if($request->get('search') != 'all' && $request->get('search') != ''){
            $videos = $videos->where('title', 'like', '%'.$request->get('search').'%');
        }
        $videos = $videos->orderBy('sequence', 'ASC')
                    ->paginate(4);
        return view('video.videoListSearchData', compact('videos'));
    }

    public function upload_UI(){
        return view('video.upload');
    }

    public function video_play_UI($videoId){

        

        $video = Video::where('file_name', $videoId)
        ->where('status', 1)
        ->first();

        if(!$video){
            return redirect()->route('notfound');
        }
        $schema =  VideoObjectSchemaController::generateVideoSchemaObject($video);
        $video->videoObjectSchema = $schema;
        return view('video.play', compact('video'));
    }

    public function edit_ui(){

        $video = Video::where('file_name', request()->file_name)->first();
        if(!$video){
            return redirect()->route('notfound');
        }

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

            $fileName = $this->getUniqueVideoId();//time();
            $filePath = $fileName.'.'.request()->file->getClientOriginalExtension();
            $save_path = Auth::user()->id.'/'.$fileName;

            //--old $request->file('file')->storeAs($save_path, $fileName,'uploads');
            request()->file->move(public_path('uploads/'.$save_path), $filePath);
            return response()->json(['success'=>'true', 'fileName'=>$fileName, 'filePath'=>$filePath,'status' => 'ok','path' => $filePath]);
        }else{
            return response()->json(['success'=>'false', 'Fail upload failed']);
        }
    }

    public function updateVideoInfo(Request $request){
        $video = Video::where('slug', request()->slug)->first();
        $host = trim($request->allow_host);
        $newHostArray =[];
        if(isset($host)){
            $hosts = explode(",", $host);
            foreach($hosts as $h){
                $newHostArray []= $this->trimUrlProtocol($h);
            }
        }
        $newHosts = implode(', ', $newHostArray);
        $video->title = $request->title;
        $video->description = $request->description;
        $video->allow_hosts = $newHosts;
        $video->skip_intro_time = $request->skip_intro_time;
        $video->stg_autoplay = $request->stg_autoplay == 1 ? $request->stg_autoplay:false;
        $video->stg_muted	 = $request->stg_muted == 1 ? $request->stg_muted : false;
        $video->stg_loop = $request->stg_loop == 1 ? $request->stg_loop : false;
        $video->stg_autopause = $request->stg_autopause == 1 ? $request->stg_autopause:false;
        $video->stg_preload_configration = isset($request->stg_preload_configration) ? $request->stg_preload_configration:'none';

        if (isset($request->custom_script_one)) {
           $video->custom_script_one = $request->custom_script_one;
        }
        if (isset($request->custom_script_two)) {
           $video->custom_script_two = $request->custom_script_two;
        }
        

        $posterImage = null;
        if($request->hasFile('poster')) {
            File::delete(File::glob('uploads/'.$video->user_id.'/'.$video->file_name.'/poster.*'));
            // Process the new image
            $fileName = 'poster.'.request()->file('poster')->getClientOriginalExtension();
            $save_path = $video->user_id.'/'.$video->file_name;
            request()->file('poster')->move(public_path('uploads/'.$save_path), $fileName);
            $posterImage = $fileName;
        }
        if ($posterImage !=null) {
            $video->poster = $posterImage;
        }
        
        if($video->save()){
            return response()->json(['success'=>'true', 'videoId'=>$video->id]);
        }else{
            return response()->json(['success'=>'false', 'message'=>'Error saving video']);
        }
    }
    public function trimUrlProtocol($url) {
        if ( substr($url, 0, 8) == 'https://' ) {
            $url = substr($url, 8);
        }
        if ( substr($url, 0, 7) == 'http://' ) {
            $url = substr($url, 7);
        }
        if ( substr($url, 0, 4) == 'www.' ) {
            $url = substr($url, 4);
        }
        if ( strpos($url, '/') !== false ) {
            $explode = explode('/', $url);
            $url     = $explode['0'];
        }
        return $url;
    }

    public function saveVideoInfo(Request $request){

        $request->validate([
            'title' => 'required'
        ]);
        

        $path = Auth::user()->id.'/'.$request->fileName.'/'.$request->fileNameWithExt;
        $media = FFMpeg::fromDisk('uploads')->open($path);
        try {
            $durationInSeconds = $media->getDurationInSeconds(); // returns an integer
        } catch (\Throwable $th) {
           $durationInSeconds = 0;
        }
        
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
        $video->video_duration =  $durationInSeconds;
        $video->original_filesize = $original_filesize;
        $video->original_resolution = $original_resolution;
        $video->original_bitrate = $bitrate ? $bitrate : rand(1,600000);
        $video->original_video_codec = $codec;
        $video->file_name = $request->fileName;
        $video->is_transcoded = 0;
        $video->upload_duration = $request->uploadDuration ? $request->uploadDuration: 10;
        
        if($video->save()){
            $this->createTmpTranscodeEntry($original_resolution, $request->fileName, $video->id);
            $this->transcode($video->id);
            return response()->json(['success'=>'true', 'lastInsertedId'=>$video->id,'formId' => $request->formId]);
        }else{
            return response()->json(['success'=>'false', 'message'=>'Error saving video']);
        }
    }

    public function transcode($id){
        dispatch(new VideoTranscode($id));
        return response()->json(['success'=>'true']);
    }  
    public function transcodeOld(Request $request){
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

    public function deleteMultipleVideos(Request $request){
        try {
            $videos = $request->get('deleteSelected');
            $videos = json_decode($videos);
            foreach($videos as $video){
                $video = Video::where('id', $video)->first();
                if ($video->delete()) {
                    File::deleteDirectory(public_path('uploads/'.$video->user_id.'/'.$video->file_name));
                }
            }
            return response()->json(['success'=>'true']);
        } catch (\Throwable $th) {
            return response()->json(['success'=>'false', 'message'=>'Error deleting videos']);
        }
        
    }

    public function UpdateVideoOrder(Request $request){
        try {
            $videos = $request->get('order');
            $sortArray = explode(',', $videos);
            $i = 1;
            foreach($sortArray as $video){
                $video = Video::where('id', $video)->update(['sequence' => $i]);
                $i++;
            }
            return response()->json(['success'=>'true']);
        } catch (\Throwable $th) {
            return response()->json(['success'=>'false']);
        }
        
    }

    public function getUniqueVideoId(): string{
        // $bytes = random_bytes(8);
        // $base64 = base64_encode($bytes);
        // return rtrim(strtr($base64, '+/', '-_'), '=');
        $chars = "bcdfghjklmnpqrstvwxyz";
        $chars .= "BCDFGHJKLMNPQRSTVWXYZ";
        $chars .= "0123456789";
        while(1){
            $key = '';
            srand((double)microtime()*1000000);
            for($i = 0; $i < 10; $i++){
                $key .= substr($chars,(rand()%(strlen($chars))), 1);
            }
            break;
        }
        return $key;
    }

    public function checkUserPendingVideoTranscoded(){
        $data = Video::select('file_name','is_transcoded')->where('user_id', Auth::user()->id)->orderBy('id', 'desc')->take(10)->get();
        if(count($data) > 0){
            return response()->json($data);
        }else{
            return response()->json([]);
        }
    }

    public function test(){
        // $ffprobe = '/usr/bin/ffprobe';
        // $videoFile = '/var/www/html/upwork/laravel-vue-ffmpeg-video.js/public/uploads/3/0nD0AHMQP_g/0nD0AHMQP_g.webm';
        // $cmd = shell_exec($ffprobe .' -v quiet -print_format json -select_streams v:0  -show_streams "'.$videoFile.'"');
        // $parsed = json_decode($cmd, true);
        // $bitrate = @$parsed['streams'][0]['bit_rate'];
        // $duration = @$parsed['streams'][0]['duration'];
        // return response()->json($parsed);

        $video = Video::where('id','=','326')->first();
         $keyTime = 10;
                switch (true) {
                    case round($video->original_filesize_raw / 1024 / 1024) >= 5120:
                        $keyTime = 240;
                        break;
                    case round($video->original_filesize_raw / 1024 / 1024) >= 3072:
                        $keyTime = 180;
                        break;
                    case round($video->original_filesize_raw / 1024 / 1024) >= 2048:
                        $keyTime = 120;
                        break;
                    case round($video->original_filesize_raw / 1024 / 1024) >= 1024:
                        $keyTime = 60;
                        break;
                    case round($video->original_filesize_raw / 1024 / 1024) >= 500:
                        $keyTime = 20;
                        break;
                    default:
                        $keyTime = 10;
                        break;
                }
                echo json_encode($keyTime);
    }
}