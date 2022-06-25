@extends('layouts.app')
@section('title', $video->title)
@section('description', $video->description)

@section('style')
<script type="application/ld+json">
<?= json_encode($video->videoObjectSchema) ?>
</script>

<link href="{{ asset('css/video-js.min.css') }}" rel="stylesheet">
<!-- Fantasy -->
<link href="{{ asset('css/player.css') }}" rel="stylesheet" />
<link href="{{ asset('css/videojs-hls-quality-selector.css') }}" rel="stylesheet">
<script src="{{ asset('js/video.min.js') }}"></script>
<link href="{{ asset('css/videojs-skip-intro.css') }}" rel="stylesheet">
<link href="{{ asset('css/videojs-seek-buttons.css') }}" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/videojs-playlist-ui@3.0.5/dist/videojs-playlist-ui.css" rel="stylesheet">
<style>
.main-preview-player {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
}

.video-js,
.playlist-container {
    position: relative;
    min-width: 300px;
    min-height: 150px;
    height: 0;
}

.video-js {
    flex: 3 1 80%;
}

.playlist-container {
    flex: 1 1 20%;
}

.vjs-playlist {
    margin: 0;
    position: absolute;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    overflow-x: auto;
}

.vjs-playlist .vjs-playlist-item-list {
    padding: 20px;
}

/* sidebar */

#sidebar .vjs-playlist {
    -ms-overflow-style: none;
    /* Internet Explorer 10+ */
    scrollbar-width: none;
    /* Firefox */
}

#sidebar .vjs-playlist::-webkit-scrollbar {
    display: none;
    /* Safari and Chrome */
}

#sidebar {
    min-width: 250px;
    max-width: 250px;
    left: 0;
    /* top layer */
    z-index: 9999;
    display: none;

}

/* Shrinking the sidebar from 250px to 80px and center aligining its content*/
#sidebar.active {
    min-width: 80px;
    max-width: 250px;
    text-align: center;
    transition: 0.5s;
}
</style>
@endsection

@section('content')
<div class="containers">
    <div class="row m-0">
        <div class="container">
            <div class="col-md-12 p-2 text-end">
                {{-- @include('layouts.breadcrumbs') --}}
                {{ Breadcrumbs::render('video', $video) }}
            </div>
        </div>
        <div class="col-md-12 p-0 text-end">
            <div class="main-preview-player">
                <div class="playlist-container vjs-fluid" id="sidebar">
                    <ol class="vjs-playlist"></ol>
                </div>
                <video id="hls-video"
                    class="video-js vjs-fluid vjs-big-play-centered playsinline webkit-playsinline vjs-theme-forest"
                    preload="none" controls height="560" widthw="995"
                    poster="/uploads/{{$video->user_id}}/{{$video->file_name}}/{{$video->poster}}" data-setup="{}">
                </video>

            </div>
        </div>
    </div>
    <div class="container">
        <div class="row mt-4 p-2">
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
                                cols="40"><iframe src="{{ config('app.url')}}/embed/{{$video->file_name}}" frameborder="0"  allow="accelerometer; autoplay; encrypted-media;" allowfullscreen style="width:100%; height:100%; border:none; margin:0; padding:0; overflow:hidden; z-index:999999;"></iframe></textarea>
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
                        <span>{{$video->original_resolution}}p</span>
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
                    <div class="bg-danger rounded p-2" style="width: 55px;height: 48px; text-align: center;">
                        <i class="fa-solid fa-video fa-2x text-light"></i>
                    </div>
                    <div class="px-3">
                        <p class="mb-2">Original Codec</p>
                        <span>{{$video->original_video_codec}}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
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
            <div class="col-md-4">
                <div class="d-flex rounded bg-white shadow-sm p-3">
                    <div class="bg-info rounded p-2" style="width: 55px;height: 48px; text-align: center;">
                        <i class="fa-solid fa-video fa-2x text-light"></i>
                    </div>
                    <div class="px-3">
                        <p class="mb-2">Original Video Type</p>
                        <span>{{$video->video_original_type}}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')

<!-- <script src="https://unpkg.com/@videojs/http-streaming/dist/videojs-http-streaming.js"></script> -->

<!-- <script src="{{ asset('js/videojs-contrib-hlsjs.min.js') }}"></script> -->
<script src="{{ asset('js/videojs-hls-quality-selector.min.js') }}"></script>
<script src="{{ asset('js/videojs-contrib-quality-levels.min.js') }}"></script>

<script src="{{ asset('js/videojs-sprite-thumbnails.min.js') }}"></script>

<script src="{{ asset('js/videojs-skip-intro.js') }}"></script>
<script src="{{ asset('js/videojs-show-hide-playlist.js') }}"></script>
<script src="{{ asset('js/videojs-seek-buttons.min.js') }}"></script>

<script src="https://cdn.jsdelivr.net/npm/videojs-playlist@5.0.0/dist/videojs-playlist.min.js"
    integrity="sha256-K0Uz7Frsk0virhC2mKXgDYODHjfYIx+Yl6B3Cu6ICcU=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/videojs-playlist-ui@3.0.5/dist/videojs-playlist-ui.min.js"></script>
<script>
var playerSkipIntroTime = "{{$video->skip_intro_time}}";
const options = {
    controlBar: {
        children: [
            'playToggle',
            'progressControl',
            'volumePanel',
            "volumeMenuButton",
            "durationDisplay",
            "timeDivider",
            "currentTimeDisplay",
            "remainingTimeDisplay",

            "CustomControlSpacer",
            'qualitySelector',
            "fullscreenToggle",
        ],
    },
    html5: {
        vhs: {
            withCredentials: true,
            overrideNative: !videojs.browser.IS_SAFARI,
            smoothQualityChange: true,

        },
        nativeAudioTracks: false,
        nativeVideoTracks: false,
        // hlsjsConfig: {
        //     debug: true,
        // }
    }
}

const player = videojs(document.getElementById('hls-video'), options);



player.ready(function() {

    $(".vjs-volume-panel-horizontal, .vjs-play-control, button.skip-forward").addClass('left-half')
    $(".vjs-custom-control-spacer").addClass('middle-half')
    $(".vjs-quality-selector, .vjs-picture-in-picture-control, .vjs-fullscreen-control")
        .addClass('right-half');

    player.src({
        src: "{{ route('video.playback', ['userid' =>$video->user_id, 'filename'=> $video->file_name,'playlist' => $video->playback_url ])}}",
        // woring with hls and key
        type: 'application/x-mpegURL',
        withCredentials: true
    });

    player.hlsQualitySelector();
    player.spriteThumbnails({
        interval: 2,
        url: "{{ config('app.url')}}/uploads/{{$video->user_id}}/{{$video->file_name}}/preview_01.jpg",
        width: 160,
        height: 90
    });

    player.on('play', function() {
        if (playerSkipIntroTime > 0) {
            player.skipIntro({
                label: 'Skip Intro',
                skipTime: playerSkipIntroTime,
            });
        }

    });

    player.showHidePlaylist({
        iconClass: "fas fa-play fa-2x",
        playList: [{
                name: 'Disney\'s Oceans 2',
                duration: 123,
                sources: [{
                    src: 'http://localhost:8000/video/playback/3/B51Yf8dzlZ/master.m3u8',
                    type: 'application/x-mpegURL'
                }],
                poster: 'http://localhost:8000/uploads/3/B51Yf8dzlZ/poster.png',
                thumbnail: [{
                    src: 'http://localhost:8000/uploads/3/B51Yf8dzlZ/poster.png'
                }]
            },
            {
                name: 'Disney\'s Oceans 1',
                duration: 45,
                sources: [{
                    src: 'http://localhost:8000/video/playback/3/KtPHkgPsC6/master.m3u8',
                    type: 'application/x-mpegURL'
                }],
                thumbnail: [{
                    src: 'http://localhost:8000/uploads/3/KtPHkgPsC6/poster.png'
                }],
                poster: 'http://localhost:8000/uploads/3/KtPHkgPsC6/poster.png'
            },
            {
                name: 'Disney\'s Oceans 2',
                duration: 123,
                sources: [{
                    src: 'http://localhost:8000/video/playback/3/B51Yf8dzlZ/master.m3u8',
                    type: 'application/x-mpegURL'
                }],
                poster: 'http://localhost:8000/uploads/3/B51Yf8dzlZ/poster.png',
                thumbnail: [{
                    src: 'http://localhost:8000/uploads/3/B51Yf8dzlZ/poster.png'
                }]
            },
            {
                name: 'Disney\'s Oceans 1',
                duration: 45,
                sources: [{
                    src: 'http://localhost:8000/video/playback/3/KtPHkgPsC6/master.m3u8',
                    type: 'application/x-mpegURL'
                }],
                thumbnail: [{
                    src: 'http://localhost:8000/uploads/3/KtPHkgPsC6/poster.png'
                }],
                poster: 'http://localhost:8000/uploads/3/KtPHkgPsC6/poster.png'
            },
            {
                name: 'Disney\'s Oceans 2',
                duration: 123,
                sources: [{
                    src: 'http://localhost:8000/video/playback/3/B51Yf8dzlZ/master.m3u8',
                    type: 'application/x-mpegURL'
                }],
                poster: 'http://localhost:8000/uploads/3/B51Yf8dzlZ/poster.png',
                thumbnail: [{
                    src: 'http://localhost:8000/uploads/3/B51Yf8dzlZ/poster.png'
                }]
            },
            {
                name: 'Disney\'s Oceans 1',
                duration: 45,
                sources: [{
                    src: 'http://localhost:8000/video/playback/3/KtPHkgPsC6/master.m3u8',
                    type: 'application/x-mpegURL'
                }],
                thumbnail: [{
                    src: 'http://localhost:8000/uploads/3/KtPHkgPsC6/poster.png'
                }],
                poster: 'http://localhost:8000/uploads/3/KtPHkgPsC6/poster.png'
            },
            {
                name: 'Disney\'s Oceans 2',
                duration: 123,
                sources: [{
                    src: 'http://localhost:8000/video/playback/3/B51Yf8dzlZ/master.m3u8',
                    type: 'application/x-mpegURL'
                }],
                poster: 'http://localhost:8000/uploads/3/B51Yf8dzlZ/poster.png',
                thumbnail: [{
                    src: 'http://localhost:8000/uploads/3/B51Yf8dzlZ/poster.png'
                }]
            },
            {
                name: 'Disney\'s Oceans 2',
                duration: 123,
                sources: [{
                    src: 'http://localhost:8000/video/playback/3/B51Yf8dzlZ/master.m3u8',
                    type: 'application/x-mpegURL'
                }],
                poster: 'http://localhost:8000/uploads/3/B51Yf8dzlZ/poster.png',
                thumbnail: [{
                    src: 'http://localhost:8000/uploads/3/B51Yf8dzlZ/poster.png'
                }]
            },
        ]
    });

    player.tech().on('usage', (e) => {
        console.log(e.name);
    });

    player.seekButtons({
        forward: 10,
        back: 10
    });

    player.on('ended', function() {
        console.log('ended == ');
        player.reset();

        player.poster(
            "{{ config('app.url')}}/uploads/{{$video->user_id}}/{{$video->file_name}}/{{$video->poster}}"
        );
        player.bigPlayButton.show();
        player.src({
            src: "{{ route('video.playback', ['userid' =>$video->user_id, 'filename'=> $video->file_name,'playlist' => $video->playback_url ])}}",
            type: 'application/x-mpegURL',
            withCredentials: true,
        });
    });






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