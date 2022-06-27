   <!DOCTYPE html>
   <html lang="en">

   <head>
       <meta charset="UTF-8">
       <meta http-equiv="X-UA-Compatible" content="IE=edge">
       <meta name="viewport" content="width=device-width, initial-scale=1.0">

       <link href="{{ asset('css/video-js.min.css') }}" rel="stylesheet">
       <!-- Fantasy -->
       <link href="{{ asset('css/player.css') }}" rel="stylesheet" />
       <link href="{{ asset('css/videojs-hls-quality-selector.css') }}" rel="stylesheet">
       <link href="{{ asset('css/videojs-skip-intro.css') }}" rel="stylesheet">
       <link href="{{ asset('css/videojs-seek-buttons.css') }}" rel="stylesheet">
       <script src="{{ asset('js/jquery.min.js') }}"></script>
       <script src="{{ asset('js/video.min.js') }}"></script>
       <style>
       body {
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
           <video id="hls-video"
               class="video-js vj-player vjs-big-play-centered playsinline webkit-playsinline vjs-theme-forest" controls
               preload="none" poster="{{$video->poster}}" data-setup="{}">
           </video>
       </div>

       <script src="{{ asset('js/videojs-hls-quality-selector.min.js') }}"></script>
       <script src="{{ asset('js/videojs-contrib-quality-levels.min.js') }}"></script>

       <script src="{{ asset('js/videojs-sprite-thumbnails.min.js') }}"></script>

       <script src="{{ asset('js/videojs-skip-intro.js') }}"></script>
       <script src="{{ asset('js/videojs-seek-buttons.min.js') }}"></script>

       <script>
       var playerSkipIntroTime = "{{$video->skip_intro_time}}";
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

       videojs.Hls.xhr.beforeRequest = function(options) {
           options.headers = {
               ActualDomain: getDomain()
           };
           return options;
       };

       const player = videojs(document.getElementById('hls-video'), options);
       player.ready(function() {
           player.src({
               src: "{{ route('embed.video.playback', ['userid' =>$video->user_id, 'filename'=> $video->file_name,'playlist' => $video->playback_url ])}}", // woring with hls and key
               type: 'application/x-mpegURL',
               withCredentials: true,
           });

           player.spriteThumbnails({
               interval: 2,
               url: "{{ config('app.url')}}/uploads/{{$video->user_id}}/{{$video->file_name}}/preview_01.jpg",
               width: 160,
               height: 90
           });
           player.hlsQualitySelector();

           player.seekButtons({
               forward: 10,
               back: 10
           });
           player.on('play', function() {
               if (playerSkipIntroTime > 0) {
                   player.skipIntro({
                       label: 'Skip Intro',
                       skipTime: playerSkipIntroTime,
                   });
               }
           });
           player.on('ended', function() {
               console.log('ended == ');
               player.reset();

               player.poster(
                   "{{ config('app.url')}}/{{$video->poster}}"
               );
               player.bigPlayButton.show();
               player.src({
                   src: "{{ route('video.playback', ['userid' =>$video->user_id, 'filename'=> $video->file_name,'playlist' => $video->playback_url ])}}",
                   type: 'application/x-mpegURL',
                   withCredentials: true,
               });
           });
       })

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
       </script>
   </body>

   </html>