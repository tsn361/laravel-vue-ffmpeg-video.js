<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="{{ asset('css/video-js.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/videojs-hls-quality-selector.css') }}" rel="stylesheet">
    <script src="{{ asset('js/video.min.js') }}"></script>

    <style>
        body{
            margin: 0;
            padding: 0;
        }
        .player-wrapper {
            position: relative;
            width: 100%;
            height: 100vh;
        }

        .vj-player {
            position: absolute;
            top: 0;
            left: 0;
            width: 100% !important;
            height: 100% !important;
        }
    </style>
<body>

    <div class="player-wrapper">
        <video id="hls-video" class="video-js vj-player vjs-big-play-centered" controls preload="auto"
            poster="/uploads/{{$video->user_id}}/{{$video->file_name}}/{{$video->poster}}" data-setup="{}">
        </video>
    </div>

    <script src="{{ asset('js/videojs-hls-quality-selector.min.js') }}"></script>
    <script src="{{ asset('js/videojs-contrib-quality-levels.min.js') }}"></script>

    <script src="{{ asset('js/videojs-sprite-thumbnails.min.js') }}"></script>
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
        };

        const player = videojs(document.getElementById('hls-video'), options);
        player.src({
            src: "{{ route('embed.video.playback', ['userid' =>$video->user_id, 'filename'=> $video->file_name,'playlist' => $video->playback_url ])}}", // woring with hls and key
            type: 'application/x-mpegURL',
            withCredentials: true,
        });

        player.on('ready', function() {
            //this.addClass('my-example');
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
    </script>
</body>

</html>