@extends('layouts.app')

@section('style')
<link href="{{ asset('css/video-js.min.css') }}" rel="stylesheet">
<link href="{{ asset('css/quality-selector.css') }}" rel="stylesheet">

<script src="{{ asset('js/video.min.js') }}"></script>
<script src="{{ asset('js/videojs-contrib-quality-levels.min.js') }}"></script>

<script src="{{ asset('js/videojs-hls-quality-selector.min.js') }}"></script>

@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 p-2 text-end">
            {{-- @include('layouts.breadcrumbs') --}}
            {{ Breadcrumbs::render('video', $video) }}
        </div>
        <div class="col-md-12 p-2 text-end">

            <video id="hls-video" class="video-js vjs-big-play-centered" controls preload="auto" height="560"
                poster="/uploads/{{$video->user_id}}/{{$video->file_name}}/{{$video->poster}}" data-setup="{}">
                <!-- <source src="{{ route('video.playback', ['playlist'=> $video->playback_url ])}}"
                    type="application/x-mpegURL"> -->
                <p class="vjs-no-js">
                    To view this video please enable JavaScript, and consider upgrading to a
                    web browser that
                    <a href="https://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
                </p>
            </video>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    {{ $video->title }}
                </div>
                <div class="card-body">
                    <p>{{ $video->description }}</p>
                    <p>Created at: {{$video->created_at}}</p>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="d-flex rounded bg-white shadow-sm p-3">
                <div>
                    <i class="fa-solid fa-video fa-3x"></i>
                </div>
                <div class="px-3">
                    <p class="mb-2">Video Duration</p>
                    <span>{{$video->video_duration}}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="d-flex rounded bg-white shadow-sm p-3">
                <div>
                    <i class="fa-solid fa-video fa-3x"></i>
                </div>
                <div class="px-3">
                    <p class="mb-2">Video Filesize</p>
                    <span>{{$video->original_filesize}}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="d-flex rounded bg-white shadow-sm p-3">
                <div>
                    <i class="fa-solid fa-video fa-3x"></i>
                </div>
                <div class="px-3">
                    <p class="mb-2">Processing Time</p>
                    <span>{{$video->process_time}}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="d-flex rounded bg-white shadow-sm p-3">
                <div>
                    <i class="fa-solid fa-video fa-3x"></i>
                </div>
                <div class="px-3">
                    <p class="mb-2">Video Resulation</p>
                    <span>{{$video->original_resolution}}</span>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="d-flex rounded bg-white shadow-sm p-3">
                <div>
                    <i class="fa-solid fa-video fa-3x"></i>
                </div>
                <div class="px-3">
                    <p class="mb-2">Upload Duration</p>
                    <span>{{$video->upload_duration}}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="d-flex rounded bg-white shadow-sm p-3">
                <div>
                    <i class="fa-solid fa-video fa-3x"></i>
                </div>
                <div class="px-3">
                    <p class="mb-2">Video Speed</p>
                    <span>{{$video->video_duration}}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="d-flex rounded bg-white shadow-sm p-3">
                <div>
                    <i class="fa-solid fa-video fa-3x"></i>
                </div>
                <div class="px-3">
                    <p class="mb-2">Original Codec</p>
                    <span>{{$video->original_video_codec}}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="d-flex rounded bg-white shadow-sm p-3">
                <div>
                    <i class="fa-solid fa-video fa-3x"></i>
                </div>
                <div class="px-3">
                    <p class="mb-2">Original Bitrate</p>
                    <span>{{$video->original_bitrate}}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
const options = {
    controlBar: {
        children: [
            'playToggle',
            'progressControl',
            'volumePanel',
            'fullscreenToggle',
        ],
    },

};


const player = videojs(document.getElementById('hls-video'), options);

player.src({
    src: '/uploads/{{$video->user_id}}/{{$video->file_name}}/{{$video->playback_url}}', // woring with hls and key
    type: 'application/x-mpegURL'
});
player.hlsQualitySelector({
    displayCurrentQuality: false,
});

player.on('ready', function() {
    //this.addClass('my-example');
});

player.play();
</script>
@endsection