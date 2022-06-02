@extends('layouts.app')

@section('style')
<link href="{{ asset('css/video-js.min.css') }}" rel="stylesheet">
<link href="{{ asset('css/quality-selector.css') }}" rel="stylesheet">

<script src="{{ asset('js/video.min.js') }}"></script>
<script src="{{ asset('js/videojs-hls-quality-selector.min.js') }}"></script>
<script src="{{ asset('js/videojs-contrib-quality-levels.min.js') }}"></script>


<script src="{{ asset('js/videojs-http-streaming.js') }}"></script>
<script src="{{ asset('js/videojs-sprite-thumbnails.min.js') }}"></script>
<style>
.offscreen {
    /* position: absolute;
    left: -999em; */
}
</style>
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
                <source
                    src="{{ route('video.playback', ['userid' =>$video->user_id, 'filename'=> $video->file_name,'playlist' => $video->playback_url ])}}"
                    type="application/x-mpegURL">
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
                    <p class="py-1">{{ $video->description }}</p>
                    <p class="py-1">Created at: {{$video->created_at}}</p>
                    <p class="py-1">Playback URL:
                        <a class="badge bg-dark text-start"
                            href="{{ config('app.playback_url')}}/video/playback/{{$video->user_id}}/{{$video->file_name}}/master.m3u8">
                            {{ config('app.playback_url')}}/video/playback/{{$video->user_id}}/{{$video->file_name}}/master.m3u8
                        </a>
                    </p>
                    <p class="py-1">
                    <div>
                        Embed Code: <a class="badge bg-danger text-start text-light" href="javascript:void(0)"
                            onclick="copyEmbedCode()">Click to Copy </a>
                    </div>
                    <div class="mt-3">
                        <textarea id="embedCode" class="offscreen" rows="5"
                            cols="40"><iframe width="565" height="320" src="{{ config('app.url')}}/embed/{{$video->slug}}/540/300" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></textarea>
                    </div>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="d-flex rounded bg-white shadow-sm p-3">
                <div class="bg-success rounded p-2" style="width: 55px;height: 48px; text-align: center;">
                    <i class="fas fa-hourglass-half fa-2x text-light"></i>
                </div>
                <div class="px-3">
                    <p class="mb-2">Video Duration</p>
                    <span>{{$video->video_duration}}</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="d-flex rounded bg-white shadow-sm p-3">
                <div class="bg-danger rounded p-2" style="width: 55px;height: 48px; text-align: center;">
                    <i class="fas fa-database fa-2x text-light"></i>
                </div>
                <div class="px-3">
                    <p class="mb-2">Video Filesize</p>
                    <span>{{$video->original_filesize}}</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="d-flex rounded bg-white shadow-sm p-3">
                <div class="bg-warning rounded p-2" style="width: 55px;height: 48px; text-align: center;">
                    <i class="fas fa-stopwatch fa-2x text-light"></i>
                </div>
                <div class="px-3">
                    <p class="mb-2">Processing Time</p>
                    <span>{{$video->process_time}}</span>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="d-flex rounded bg-white shadow-sm p-3">
                <div class="bg-info rounded p-2" style="width: 55px;height: 48px; text-align: center;">
                    <i class="fa-solid fa-video fa-2x text-light"></i>
                </div>
                <div class="px-3">
                    <p class="mb-2">Video Resulation</p>
                    <span>{{$video->original_resolution}}</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="d-flex rounded bg-white shadow-sm p-3">
                <div class="bg-info rounded p-2" style="width: 55px;height: 48px; text-align: center;">
                    <i class="fas fa-hourglass-half fa-2x text-light"></i>
                </div>
                <div class="px-3">
                    <p class="mb-2">Upload Duration</p>
                    <span>{{$video->upload_duration}}</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="d-flex rounded bg-white shadow-sm p-3">
                <div class="bg-warning rounded p-2" style="width: 55px;height: 48px; text-align: center;">
                    <i class="fa-solid fa-video fa-2x text-light"></i>
                </div>
                <div class="px-3">
                    <p class="mb-2">Video Speed</p>
                    <span>{{$video->video_duration}}</span>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="d-flex rounded bg-white shadow-sm p-3">
                <div class="bg-danger rounded p-2" style="width: 55px;height: 48px; text-align: center;">
                    <i class="fa-solid fa-video fa-2x text-light"></i>
                </div>
                <div class="px-3">
                    <p class="mb-2">Original Codec</p>
                    <span>{{$video->original_video_codec}}</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="d-flex rounded bg-white shadow-sm p-3">
                <div class="bg-success rounded p-2" style="width: 55px;height: 48px; text-align: center;">
                    <i class="fa-solid fa-video fa-2x text-light"></i>
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
            'qualitySelector',
        ],
    },
    html5: {
        hls: {
            overrideNative: true, // add this line
        }
    },

};

const player = videojs(document.getElementById('hls-video'), options);
// player.src({
//     src: '/uploads/{{$video->user_id}}/{{$video->file_name}}/{{$video->playback_url}}', // woring with hls and key
//     type: 'application/x-mpegURL'
// });


player.on('ready', function() {
    console.log("Player is ready to play")
});

player.play();
player.spriteThumbnails({
    interval: 2,
    url: "{{ config('app.url')}}/uploads/{{$video->user_id}}/{{$video->file_name}}/preview_01.jpg",
    width: 160,
    height: 90
});

player.hlsQualitySelector({
    displayCurrentQuality: false,
});

function copyEmbedCode() {
    var copyText = document.getElementById("embedCode");
    copyText.select();
    document.execCommand("copy");
    Swal.fire({
        title: 'Player Embed Code Copied',
        text: 'Copy the code and paste it in your website',
        icon: 'success',
        confirmButtonText: 'OK'
    })
}
</script>
@endsection