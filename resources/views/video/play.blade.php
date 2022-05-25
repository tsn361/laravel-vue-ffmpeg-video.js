@extends('layouts.app')

@section('style')
<link href="{{ asset('css/video-js.css') }}" rel="stylesheet">
<link href="{{ asset('css/quality-selector.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 p-2 text-end">
            Breadcrumb
        </div>

        <video
                id="hls-video"
                class="video-js"
                controls
                preload="auto"
                height="560"
                poster="/uploads/{{$video->user_id}}/{{$video->file_name}}/{{$video->poster}}"
                data-setup="{}"
            >
            <p class="vjs-no-js">
              To view this video please enable JavaScript, and consider upgrading to a
              web browser that
              <a href="https://videojs.com/html5-video-support/" target="_blank"
                >supports HTML5 video</a
              >
            </p>
          </video>

    </div>
    <div class="row rounded-top shadow-sm">
        <div class="col-md-12 p-2">
            video details
        </div>
        <div class="col-md-6 p-2">W
            <div class="mt-2">
                List of videos
            </div>
        </div>
        <div class="col-md-6 p-2 text-end ">
            <button class="btn btn-primary btn-sm"> <i class="fas fa-plus"></i> Create</button>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ asset('js/video.min.js') }}"></script>
<script src="{{ asset('js/silvermine-videojs-quality-selector.min.js') }}"></script>
<script>

    const options = {
            controlBar: {
                children: [
                    'playToggle',
                    'progressControl',
                    'volumePanel',
                    'qualitySelector',
                    'fullscreenToggle',
                ],
            },
        };
        


    const player = videojs(document.getElementById('hls-video'), options);
    // player.src({
    //     src: '/uploads/{{$video->user_id}}/{{$video->playback_url}}',
    //     type: 'application/x-mpegURL'
    // });

    player.src([
        {
            src: '/uploads/{{$video->user_id}}/{{$video->playback_url}}',
            type: 'application/x-mpegURL',
            label: '720P',
        },
        {
            src: '/uploads/{{$video->user_id}}/{{$video->playback_url}}',
            type: 'application/x-mpegURL',
            label: '480P',
            selected: true,
        },
        {
            src: '/uploads/{{$video->user_id}}/{{$video->playback_url}}',
            type: 'application/x-mpegURL',
            label: '360P',
        },
    ]);

    player.on('ready', function() {
        //this.addClass('my-example');
    });

    player.play();
</script>
@endsection