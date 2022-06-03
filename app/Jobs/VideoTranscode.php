<?php
namespace App\Jobs;
ini_set('memory_limit', '5G');//1 GIGABYTE
ini_set('max_execution_time', 0);
set_time_limit(0);

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;


use App\Models\Video;
use App\Models\TmpTranscodeProgress;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Auth;

use FFMpeg\FFProbe;
use FFMpeg;
use FFMpeg\Coordinate\Dimension;
use FFMpeg\Format\Video\X264;
use FFMpeg\Format\ProgressListener\AbstractProgressListener;
use ProtoneMedia\LaravelFFMpeg\FFMpeg\ProgressListenerDecorator;
use FFMpeg\Format\FormatInterface;
use ProtoneMedia\LaravelFFMpeg\Exporters\HLSVideoFilters;
use ProtoneMedia\LaravelFFMpeg\Exporters\HLSExporter;

use ProtoneMedia\LaravelFFMpeg\Filters\TileFactory;

class VideoTranscode implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 1000000;
    public $tries = 1;
    public $maxExceptions = 1;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    private $video_id;
    public function __construct($video_id)
    {
        $this->video_id = $video_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $video = Video::where('id',$this->video_id)->where('is_transcoded', 0)->first();
        
        if($video){
            $array = array(0 => '1080', 1 => '720', 2 => '480', 3 => '360', 4 => '240');
            $key = array_search($video->original_resolution, $array);
            $newArray = array_slice($array, $key);
            sort($newArray);

            
            $path = $video->user_id.'/'.$video->file_name.'/'.$video->origianl_file_url;
            $Keypath = $video->user_id.'/'.$video->file_name;
            $masetPath = $video->user_id.'/'.$video->file_name.'/master.m3u8';
            $vttPath = $video->user_id.'/'.$video->file_name.'/';

            $p240 = (new X264)->setKiloBitrate(350);
            $p360 = (new X264)->setKiloBitrate(800);
            $p480 = (new X264)->setKiloBitrate(1200);
            $p720 = (new X264)->setKiloBitrate(1900);
            $p1080 = (new X264)->setKiloBitrate(4000);

            $processOutput =  FFMpeg::fromDisk('uploads')->open($path)
                        ->exportTile(function (TileFactory $factory) use($vttPath) {
                            $factory->interval(2)
                                ->scale(160, 90)
                                ->grid(15, 350);
                        })->save($vttPath.'preview_%02d.jpg')
                        ->exportForHLS()
                        ->setSegmentLength(10);
                        
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
                        $this->updateTranscodeStatus($percentage, 1, $video->file_name, $newArray);
                    }else{
                        $this->updateTranscodeStatus($percentage, 0, $video->file_name, $newArray);
                    }
                })->save($masetPath)
                ->cleanupTemporaryFiles();

                $this->updateVideoStatus($video->id,1,1);
        }else{
            $this->updateVideoStatus($this->video_id,2,2);
            $this->fail();
        }
    }

    public function failed() 
    {
        \Log::info("VideoTranscode=> e ".$this->video_id);
        $this->updateVideoStatus($this->video_id, 2, 2);
        $this->fail();
    }

    public function updateTranscodeStatus($progress, $is_complete, $file_name,$fileFormatArray){
        $lastFormat = last($fileFormatArray);

        //iniatially set the progress to 1%
        $progress = $progress == 0 ? 1 : $progress;

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
                $query = TmpTranscodeProgress::where('file_name', $file_name)->where('file_format', $format)->update(['progress' => $progress, 'is_complete'=>$is_complete]);
            }
        }
    }
    public function updateVideoStatus($video_id, $status, $is_transcoded){
        $query = Video::where('id', $video_id)->update(['status' => $status, 'is_transcoded'=> $is_transcoded ]);
        if ($query) {
            $this->deleteTranscodeStatus($video_id);
        }
    }

    public function deleteTranscodeStatus($video_id){
        
        $query = TmpTranscodeProgress::where('video_id', $video_id)->delete();
       
    }

    // public function GenerateVtt(){
    //     $vttPath = $video->user_id.'/'.$video->file_name.'/vtt/';
    //             FFMpeg::fromDisk('uploads')->open($path)
    //                 ->exportTile(function (TileFactory $factory) use($vttPath) {
    //                     $factory->interval(1)
    //                     ->scale(160, 90)
    //                     ->generateVTT($vttPath.'master.vtt');
    //                 })
    //                 ->save($vttPath.'tile_%05d.jpg');
    // }
}