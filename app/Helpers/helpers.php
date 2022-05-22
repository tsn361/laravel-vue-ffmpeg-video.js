<?php

namespace App\Http\Helpers;

use App\Models\Campaign;
use App\Models\Project;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Helpers {
    public static function uploadFileToS3($file)
    {

        $filename = uniqid() . "." . $file->extension();
        $path = "media/images/";

        $s3 = App::make('aws')->createClient('s3');
        $s3->putObject(array(
            'Bucket' => config('aws.bucket'),
            'Key' => $path . $filename,
            'SourceFile' => $file
        ));

        //example URL
        //https://{bucket_name}.s3.amazonaws.com/media/images/{filename}

        return $path . $filename;   
    }

    public static function deleteFileFromS3($file)
    {
        $s3 = App::make('aws')->createClient('s3');
        $s3->deleteObject(array(
            'Bucket' => config('aws.bucket'),
            'Key' => $file
        ));
    }

    public static function getFileName($url)
    {
        $file = explode('/', $url);
        return $file[count($file) - 1];
    }

    public static function getProjectIdBySlug($slug)
    {
        $project = Project::where('slug'. $slug)->first();
        return $$project->id;
    }

    public static function getProjectSlug($str){
        $slug = Str::slug($str, '-');
        $slug_count = Project::where('slug', $slug)->count();

        if($slug_count > 0){
            $slug = $slug . '-' . $slug_count+1;
        }

        return $slug;
    }

    public static function getCampaignSlug($str){

        $slug = strtolower(str_replace(" ", "-", $str));//Str::slug($str, '-');
        $slug_count = Campaign::where('slug', $slug)->count();

        if($slug_count > 0){
            $slug = $slug . '-' . $slug_count+1;
        }

        return $slug;
    }

    public static function getAuthUserId()
    {
        if (Auth::check()) {
            return Auth::user()->id;
        }
    }

    public static function uploadUserJsonToS3($filename, $project_id, $data){

        try{

            $path = "users/".$project_id."/";
            $s3 = App::make('aws')->createClient('s3');
            $s3->putObject([
                'Bucket' => config('aws.bucket'),
                'Key' => $path . $filename,
                'Body' => json_encode($data)
            ]);

            return 'success';   

        } catch (\Exception $e) {
                
            return $e->getMessage();
        }

        
    }

}
