<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

use App\Models\User;

class Video extends Model
{
    use Sluggable;
    use HasFactory;
    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'description',
        'origianl_file_url',
        'playback_url',
        'video_duration',
        'original_filesize',
        'original_resolution',
        'original_bitrate',
        'original_video_codec',
        'upload_duration',
        'upload_speed',
        'process_time',
        'poster',
        'file_name',
        'is_transcoded',
        'status',
    ];
    
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => ['title', 'id', 'user_id'],
            ]
        ];
    }

    protected function uploadDuration(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => gmdate("h:i:s", $value). ' Seconds',
        );
    }
    protected function videoDuration(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => gmdate("h:i:s", $value). ' Seconds',
        );
    }

    protected function getCreatedByAttribute($value)
    {
        return User::find($this->user_id)->name;
    }
    

    // {
    //     $user = User::where('id',3)->first(['first_name', 'last_name']);
    //     return $user->first_name . ' ' . $user->last_name;
    // }


}