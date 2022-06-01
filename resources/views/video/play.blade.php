@extends('layouts.app')

@section('style')
<link href="{{ asset('css/video-js.min.css') }}" rel="stylesheet">
<link href="{{ asset('css/quality-selector.css') }}" rel="stylesheet">
{{-- <link href="{{ asset('js/videojs-vtt-thumbnails/dist/videojs-vtt-thumbnails.css') }}" rel="stylesheet"> --}}
<style>
.offscreen {
    position: absolute;
    left: -999em;
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
                    <p class="py-1" onclick="copyEmbedCode()">Embed Code:
                        <a class="badge bg-danger text-start text-light" href="javascript:void(0)">Click to Copy </a>
                        <textarea id="embedCode" class="offscreen"><iframe width="565" height="320"
                                src="{{ config('app.url')}}/embed/{{$video->slug}}/540/300" frameborder="0"
                                allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen></iframe></textarea>
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
<script src="{{ asset('js/video.min.js') }}"></script>
<script src="{{ asset('js/videojs-contrib-quality-levels.min.js') }}"></script>

<script src="{{ asset('js/videojs-hls-quality-selector.min.js') }}"></script>
<script src="{{ asset('js/videojs-http-streaming.js') }}"></script>
<script src="{{ asset('js/videojs-preview-thumbnails.min.js') }}"></script>
<script src="{{ asset('js/videojs-sprite-thumbnails/dist/videojs-sprite-thumbnails.min.js') }}"></script>
<!-- <script src="https://cdn.jsdelivr.net/npm/videojs-vtt-thumbnails@0.0.13/dist/videojs-vtt-thumbnails.cjs.min"></script>  -->
<script>
/*! @name videojs-sprite-thumbnails @version 0.5.3 @license MIT */ ! function(e, t) {
    "object" == typeof exports && "undefined" != typeof module ? module.exports = t(require("video.js")) : "function" ==
        typeof define && define.amd ? define(["video.js"], t) : (e = e || self).videojsSpriteThumbnails = t(e.videojs)
}(this, function(e) {
    "use strict";
    var t = (e = e && Object.prototype.hasOwnProperty.call(e, "default") ? e.default : e).getPlugin("plugin"),
        o = {
            url: "",
            width: 0,
            height: 0,
            interval: 1,
            responsive: 600
        },
        r = function(t) {
            var r, i;

            function n(r, i) {
                var n;
                return (n = t.call(this, r) || this).options = e.mergeOptions(o, i), n.player.ready(function() {
                    ! function(t, o) {
                        var r = o.url,
                            i = o.height,
                            n = o.width,
                            a = o.responsive,
                            p = e.dom || e,
                            s = t.controlBar,
                            u = s.progressControl,
                            l = u.seekBar,
                            d = l.mouseTimeDisplay;
                        if (r && i && n && d) {
                            var c = p.createEl("img", {
                                    src: r
                                }),
                                f = function(e) {
                                    Object.keys(e).forEach(function(t) {
                                        var o = e[t],
                                            r = d.timeTooltip.el().style;
                                        "" !== o ? r.setProperty(t, o) : r.removeProperty(t)
                                    })
                                },
                                h = function() {
                                    var e = c.naturalWidth,
                                        u = c.naturalHeight;
                                    if (t.controls() && e && u) {
                                        var h = parseFloat(d.el().style.left);
                                        if (h = t.duration() * (h / l.currentWidth()), !isNaN(h)) {
                                            h /= o.interval;
                                            var g = t.currentWidth(),
                                                m = a && g < a ? g / a : 1,
                                                v = e / n,
                                                b = n * m,
                                                y = i * m,
                                                x = Math.floor(h % v) * -b,
                                                k = Math.floor(h / v) * -y,
                                                j = e * m + "px " + u * m + "px",
                                                w = p.getBoundingClientRect(s.el()).top,
                                                O = p.getBoundingClientRect(l.el()).top,
                                                P = -y - Math.max(0, O - w);
                                            f({
                                                width: b + "px",
                                                height: y + "px",
                                                "background-image": "url(" + r + ")",
                                                "background-repeat": "no-repeat",
                                                "background-position": x + "px " + k + "px",
                                                "background-size": j,
                                                top: P + "px",
                                                color: "#fff",
                                                "text-shadow": "1px 1px #000",
                                                border: "1px solid #000",
                                                margin: "0 1px"
                                            })
                                        }
                                    }
                                };
                            f({
                                width: "",
                                height: "",
                                "background-image": "",
                                "background-repeat": "",
                                "background-position": "",
                                "background-size": "",
                                top: "",
                                color: "",
                                "text-shadow": "",
                                border: "",
                                margin: ""
                            }), u.on("mousemove", h), u.on("touchmove", h), t.addClass(
                                "vjs-sprite-thumbnails")
                        }
                    }(n.player, n.options)
                }), n
            }
            return i = t, (r = n).prototype = Object.create(i.prototype), r.prototype.constructor = r, r.__proto__ =
                i, n
        }(t);
    return r.defaultState = {}, r.VERSION = "0.5.3", e.registerPlugin("spriteThumbnails", r), r
});
</script>
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


player.hlsQualitySelector({
    displayCurrentQuality: false,
});

player.on('ready', function() {
    console.log("Player is ready to play")
});


// player.spriteThumbnails({
//     url: 'http://localhost:8000/uploads/{{$video->user_id}}/{{$video->file_name}}/tile_00001.jpg',
//     width: 320,
//     height: 180,
// });

player.play();
// 'https://raw.githubusercontent.com/GiriAakula/samuel-miller-task/master/openvideo.png'
player.spriteThumbnails({
    interval: 2,
    url: 'http://localhost:8000/uploads/{{$video->user_id}}/{{$video->file_name}}/tile_00001.jpg',
    width: 160,
    height: 90
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