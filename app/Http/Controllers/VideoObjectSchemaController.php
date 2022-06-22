<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Video;
use Illuminate\Http\Request;
use Spatie\SchemaOrg\Schema;

class VideoObjectSchemaController extends Controller
{
    public function generateVideoSchemaObject($videId){

        if(isset($videId)){
            $video = Video::where('file_name', $videId)
            ->where('status', 1)
            ->first();

            if(!$video){
                return response()->json('nope');
            }
            $host = request()->getSchemeAndHttpHost();
            $contentUrl = $host.'/video?v='.$videId;
            $embedUrl = $host.'/embed/'.$videId;
            $thumbUrl = $host.'/uploads/'.$video->user_id.'/'.$videId.'/poster.png';
            $name = $video->title;
            $desc = $video->description ? $video->description : 'Not secified';
            $duration = $video->video_duration_iso_format;
            $uploadDate = Carbon::parse($video->created_at)->toIso8601String();

            
            $localBusiness = Schema::VideoObject()
                ->name($name)
                ->description($desc)
                ->contentURL($contentUrl)
                ->duration($duration)
                ->uploadDate("2018-10-27T14:00:00+00:00")
                ->embedUrl($embedUrl)
                ->thumbnailUrl($thumbUrl);
                
            echo json_encode($localBusiness);
            // echo $localBusiness->toScript();
            return;
        }
        return response()->json('nope');
    }
    
}