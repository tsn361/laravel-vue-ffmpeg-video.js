<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Video;
use Illuminate\Http\Request;
use Spatie\SchemaOrg\Schema;

class VideoObjectSchemaController extends Controller
{
    public static function generateVideoSchemaObject($video){

        if(isset($video)){
            
            $host = request()->getSchemeAndHttpHost();
            $contentUrl = $host.'/video?v='.$video->file_name;
            $embedUrl = $host.'/embed/'.$video->file_name;
            $thumbUrl = $host.'/uploads/'.$video->user_id.'/'.$video->file_name.'/poster.png';
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
                
            // echo json_encode($localBusiness->toArray());
            // echo $localBusiness->toScript();
            return $localBusiness->toArray();
        }
    }
    
    
}