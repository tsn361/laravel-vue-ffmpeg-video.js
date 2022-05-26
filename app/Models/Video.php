<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

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

    public function createdBy(){
        return $this->belongsTo(User::class);
    }


}