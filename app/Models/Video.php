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
            get: fn ($value) => $value >0 && $value !=null ? gmdate("H:i:s", $value). ' Seconds' : '00:00:00 Seconds',
        );
    }
    protected function videoDuration(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value >0 && $value !=null ? gmdate("H:i:s", $value). ' Seconds' : '00:00:00 Seconds',
        );
    }

    protected function 	processTime(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value >0 && $value !=null ? gmdate("H:i:s", $value). ' Seconds' : '00:00:00 Seconds',
        );
    }

    protected function getCreatedByAttribute($value)
    {
        return User::find($this->user_id)->name;
    }

    protected function originalFilesize(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => round($value / 1024 / 1024, 2) . ' MB',
        );
    }

    protected function originalBitrate(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => round($value / 1000 / 1000, 2) * 0.125 . ' Mbps',
        );
    }

}