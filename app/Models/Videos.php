<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class Videos extends Model
{
    use Sluggable;
    use HasFactory;
    protected $fillable = [
        'uploaded_by',
        'title',
        'slug',
        'description',
        'playback_url',
        'video_duration',
        'original_filesize',
        'original_resolution',
        'original_bitrate',
        'original_video_codec',
        'upload_duration',
        'upload_speed',
        'process_time',
    ];
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => ['title', 'id', 'uploaded_by'],
            ]
        ];
    }
}