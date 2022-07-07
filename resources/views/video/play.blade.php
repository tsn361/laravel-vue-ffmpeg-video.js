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
<link href="{{ asset('css/videojs.sprite.thumbnails.css') }}" rel="stylesheet">
<!-- <link href="{{ asset('css/videojs-custom-playlist.css') }}" rel="stylesheet">
<link href="{{ asset('css/videojs-playlist-ui.css') }}" rel="stylesheet"> -->

<link rel="stylesheet" href="https://unpkg.com/videojs-overlay-buttons@latest/dist/videojs-overlay-buttons.css" />
<style>

</style>
@endsection

@section('content')
<div class="container">
    <div class="row m-0">
        <div class="container">
            <div class="col-md-12 p-2 text-end">
                {{-- @include('layouts.breadcrumbs') --}}
                {{ Breadcrumbs::render('video', $video) }}

            </div>
        </div>
        <div class="col-md-12 p-0 text-end">
            <video id="hls-video" class="video-js vjs-big-play-centered playsinline webkit-playsinline vjs-theme-forest"
                preload="{{$video->stg_preload_configration}}" controls height="560" poster="{{$video->poster}}">
            </video>
            <!-- <div class="main-preview-player">
                <div class="playlist-container vjs-fluid" id="sidebar">
                    <ol class="vjs-playlist"></ol>
                </div>
                <video id="hls-video"
                    class="video-js vjs-big-play-centered playsinline webkit-playsinline vjs-theme-forest"
                    preload="none" controls height="560" poster="{{$video->poster}}" data-setup="{}">
                </video>
            </div> -->
        </div>
    </div>
    <div>
        @include('video.playVideoInfos')
    </div>
</div>
@endsection

@section('script')

<script src="{{ asset('js/playerSetting.js') }}"></script>
<script src="{{ asset('js/videojs-hls-quality-selector.min.js') }}"></script>
<script src="{{ asset('js/videojs-contrib-quality-levels.min.js') }}"></script>

<script src="{{ asset('js/videojs-sprite-thumbnails.min.js') }}"></script>

<script src="{{ asset('js/videojs-skip-intro.js') }}"></script>
<script src="{{ asset('js/videojs-show-hide-playlist.js') }}"></script>
<script src="{{ asset('js/videojs-seek-buttons.min.js') }}"></script>
<!-- <script src="{{ asset('js/videojs-playlist.min.js') }}"></script>
<script src="{{ asset('js/videojs-playlist-ui.min.js') }}"></script> -->


<script>
$(window).on('load', function() {
    var attributes = [];

    var allElements = document.querySelectorAll("*");

    for (var i = 0; i < allElements.length; i++) {
        if (allElements[i].getAttribute("title")) {
            if (allElements[i].getAttribute("title") != 'Play Video') {
                var value = allElements[i].getAttribute("title")
                allElements[i].setAttribute('tooltip', value);
            }
            allElements[i].removeAttribute('title')
        }
    }
})


var playerSkipIntroTime = "{{$video->skip_intro_time}}";
var videoObject = @json($video);;
// var playlistData = [{
//     name: 'Sample from Apple',
//     duration: 123,
//     sources: [{
//         src: 'https://multiplatform-f.akamaihd.net/i/multi/will/bunny/big_buck_bunny_,640x360_400,640x360_700,640x360_1000,950x540_1500,.f4v.csmil/master.m3u8',
//         type: 'application/x-mpegURL'
//     }],
//     poster: 'https://picsum.photos/id/237/200/300',
//     thumbnail: [{
//         src: 'https://picsum.photos/id/237/200/300'
//     }]
// }, ];
const options = {
    techOrder: ['html5'],
    controlBar: {
        children: [
            "playToggle",
            "progressControl",
            "volumePanel",
            "volumeMenuButton",


            "CustomControlSpacer",


            "currentTimeDisplay",
            "timeDivider",
            "durationDisplay",
            "qualitySelector",
            "pictureInPictureToggle",
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
    }
}

videojs.Hls.xhr.beforeRequest = function(options) {
    options.headers = {
        ActualDomain: getDomain()
    };
    return options;
};
const player = videojs(document.getElementById('hls-video'), options);
player.ready(function() {

    $(".vjs-volume-panel-horizontal, .vjs-play-control, button.skip-forward").addClass('left-half')
    // $(".vjs-custom-control-spacer").addClass('middle-half')
    $(".vjs-quality-selector, .vjs-picture-in-picture-control, .vjs-fullscreen-control, .vjs-menu-button")
        .addClass('right-half');
    setTimeout(() => {
        settings(player, videoObject)
    }, 100);

    player.src({
        src: "{{ route('video.playback', ['userid' =>$video->user_id, 'filename'=> $video->file_name,'playlist' => $video->playback_url ])}}",
        // woring with hls and key
        type: 'application/x-mpegURL',
        withCredentials: true
    });


    player.spriteThumbnails({
        interval: 2,
        url: "{{ config('app.url')}}/uploads/{{$video->user_id}}/{{$video->file_name}}/preview_01.jpg",
        width: 160,
        height: 90
    });

    player.hlsQualitySelector({
        IsHd: "{{$video->original_resolution == '720' || $video->original_resolution == '1080' ? true : false}}",
    });

    player.on('play', function() {
        if (playerSkipIntroTime > 0) {
            player.skipIntro({
                label: 'Skip Intro',
                skipTime: playerSkipIntroTime,
            });
        }

    });
    // playlistData.unshift({
    //     name: '{{$video->title}}',
    //     duration: '{{$video->video_raw_duration}}',
    //     sources: [{
    //         src: "{{ route('video.playback', ['userid' =>$video->user_id, 'filename'=> $video->file_name,'playlist' => $video->playback_url ])}}",
    //         type: 'application/x-mpegURL'
    //     }],
    //     poster: "{{ config('app.url')}}{{$video->poster}}",
    //     thumbnail: [{
    //         src: "{{ config('app.url')}}{{$video->poster}}"
    //     }]
    // });


    // player.showHidePlaylist({
    //     iconClass: "fas fa-play fa-2x",
    //     playList: playlistData
    // });

    player.tech().on('usage', (e) => {
        console.log(e.name);
    });

    player.seekButtons({
        forward: 10,
        back: 10
    });

    player.on('ended', function() {
        player.poster(
            "{{ config('app.url')}}/{{$video->poster}}"
        );
        // player.bigPlayButton.show();
        player.src({
            src: "{{ route('video.playback', ['userid' =>$video->user_id, 'filename'=> $video->file_name,'playlist' => $video->playback_url ])}}",
            type: 'application/x-mpegURL',
            withCredentials: true,
        });
    });

});

function getDomain() {
    var domain = ''
    if (document.referrer) {
        var fullDomain = (new URL(document.referrer));
        domain = fullDomain.hostname
    } else {
        var fullDomain = (new URL(window.location.href));
        domain = fullDomain.hostname
    }
    return domain
}

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