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

<link href="{{ asset('css/videojs-skip-intro.css') }}" rel="stylesheet">
<link href="{{ asset('css/videojs-seek-buttons.css') }}" rel="stylesheet">
<link href="{{ asset('css/videojs.sprite.thumbnails.css') }}" rel="stylesheet">
<link href="{{ asset('css/videojs.markers.min.css') }}" rel="stylesheet">
<!-- <link href="{{ asset('css/videojs-custom-playlist.css') }}" rel="stylesheet">
<link href="{{ asset('css/videojs-playlist-ui.css') }}" rel="stylesheet"> -->

<link href="{{ asset('css/videojs-contrib-ads.min.css') }}" rel="stylesheet">
<link href="{{ asset('css/videojs.ima.css') }}" rel="stylesheet">


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
                <track kind='captions' src='{{ asset("sample.vtt") }}' srclang='en' label='English' />
                <track kind='captions' src='https://dotsub.com/media/5d5f008c-b5d5-466f-bb83-2b3cfa997992/c/spa/vtt'
                    srclang='es' label='Spanish' />
                <track kind='captions' src='https://dotsub.com/media/5d5f008c-b5d5-466f-bb83-2b3cfa997992/c/fre_ca/vtt'
                    srclang='fr' label='French' />

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

<script src="{{ asset('js/video.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/videojs.ima.js') }}"></script>
<script src="{{ asset('js/videojs.ads.min.js') }}"></script>



<script src="{{ asset('js/videojs-overwrite.js') }}"></script>
<script src="{{ asset('js/playerSetting.js') }}"></script>
<script src="{{ asset('js/videojs-hls-quality-selector.min.js') }}"></script>
<script src="{{ asset('js/videojs-contrib-quality-levels.min.js') }}"></script>

<script src="{{ asset('js/videojs-sprite-thumbnails.min.js') }}"></script>

<script src="{{ asset('js/videojs-skip-intro.js') }}"></script>
<script src="{{ asset('js/videojs-show-hide-playlist.js') }}"></script>
<script src="{{ asset('js/videojs-seek-buttons.min.js') }}"></script>
<script src="{{ asset('js/videojs-double-tap-skip.js') }}"></script>
<script src="{{ asset('js/videojs-markers.min.js') }}"></script>
<!-- <script src="{{ asset('js/videojs-playlist.min.js') }}"></script>
<script src="{{ asset('js/videojs-playlist-ui.min.js') }}"></script> -->



<script>
$(window).on('load', function() {
    var allElements = document.querySelectorAll("*");
    for (var i = 0; i < allElements.length; i++) {
        var attVal = allElements[i].getAttribute("title");
        if (allElements[i].getAttribute("title")) {
            if (attVal !== 'Play Video' && attVal !== 'Captions' && attVal !== 'Subtitles') {
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
            "CaptionsButton",
            "SubtitlesButton",
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
        nativeTextTracks: false,
    },
    textTrackSettings: true
}

videojs.Hls.xhr.beforeRequest = function(options) {
    options.headers = {
        ActualDomain: getDomain()
    };
    return options;
};
const player = videojs(document.getElementById('hls-video'), options);

// player.ready(function() {
player.tech_.off('dblclick');


settings(player, videoObject)

player.src({
    src: "{{ route('video.playback', ['userid' =>$video->user_id, 'filename'=> $video->file_name,'playlist' => $video->playback_url ])}}",
    // src: "http://demo.unified-streaming.com/video/tears-of-steel/tears-of-steel.ism/.m3u8",
    // woring with hls and key
    type: 'application/x-mpegURL',
    withCredentials: true
});




var marker = [{
        time: 9.5,
        adsUrl: "https://pubads.g.doubleclick.net/gampad/ads?iu=/21775744923/external/single_preroll_skippable&sz=640x480&ciu_szs=300x250%2C728x90&gdfp_req=1&output=vast&unviewed_position_start=1&env=vp&impl=s&correlator=",
    },
    {
        time: 36,
        adsUrl: "https://pubads.g.doubleclick.net/gampad/ads?iu=/21775744923/external/single_ad_samples&sz=640x480&cust_params=sample_ct%3Dlinear&ciu_szs=300x250%2C728x90&gdfp_req=1&output=vast&unviewed_position_start=1&env=vp&impl=s&correlator=",
    },
    {
        time: 63.6,
        adsUrl: "https://pubads.g.doubleclick.net/gampad/ads?iu=/21775744923/external/vmap_ad_samples&sz=640x480&cust_params=sample_ar%3Dpostonly&ciu_szs=300x250&gdfp_req=1&ad_rule=1&output=vmap&unviewed_position_start=1&env=vp&impl=s&correlator=",
    },
    {
        time: 120,
        adsUrl: "https://pubads.g.doubleclick.net/gampad/ads?iu=/21775744923/external/single_preroll_skippable&sz=640x480&ciu_szs=300x250%2C728x90&gdfp_req=1&output=vast&unviewed_position_start=1&env=vp&impl=s&correlator=",
    },
];
var imaOptions = {
    id: 'hls-video',
    adLabel: 'AD',
    adTagUrl: null,
    adsRenderingSettings: {
        enablePreloading: true
    },
    playAdAlways: true,
    autoplay: true
};
player.ima(imaOptions);
player.markers({
    markerStyle: {
        width: "4px",
        "background-color": "yellow",
    },
    markerTip: {
        display: false,
    },
    onMarkerReached: function(marker, index) {

        console.log("onMarkerReached == ", index, marker);

        player.ima.changeAdTag(marker.adsUrl); // really null
        player.ima.requestAds();

    },
    markers: marker,
});

player.doubleTap(player)

player.spriteThumbnails({
    interval: 2,
    url: "{{ config('app.url')}}/uploads/{{$video->user_id}}/{{$video->file_name}}/preview_01.jpg",
    width: 160,
    height: 90
});

player.hlsQualitySelector({
    IsHd: "{{$video->original_resolution == '720' || $video->original_resolution == '1080' ? true : false}}",
});

player.skipIntro({
    label: 'Skip Intro',
    skipTime: playerSkipIntroTime,
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

//If you want to start English as the caption automatically
// player.one("play", function() {
//     player.textTracks()[0].mode = "showing";
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