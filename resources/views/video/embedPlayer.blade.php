   <!DOCTYPE html>
   <html lang="en">

   <head>
       <meta charset="UTF-8">
       <meta http-equiv="X-UA-Compatible" content="IE=edge">
       <meta name="viewport" content="width=device-width, initial-scale=1.0">

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
               preload="{{$video->stg_preload_configration}}" poster="{{$video->poster}}">

               <track kind='captions' src='{{ asset("sample.vtt") }}' srclang='en' label='English' />
               <track kind='captions' src='https://dotsub.com/media/5d5f008c-b5d5-466f-bb83-2b3cfa997992/c/spa/vtt'
                   srclang='es' label='Spanish' />
               <track kind='captions' src='https://dotsub.com/media/5d5f008c-b5d5-466f-bb83-2b3cfa997992/c/fre_ca/vtt'
                   srclang='fr' label='French' />
           </video>
       </div>

       <script src="{{ asset('js/playerSetting.js') }}"></script>
       <script src="{{ asset('js/videojs-hls-quality-selector.min.js') }}"></script>
       <script src="{{ asset('js/videojs-contrib-quality-levels.min.js') }}"></script>

       <script src="{{ asset('js/videojs-sprite-thumbnails.min.js') }}"></script>

       <script src="{{ asset('js/videojs-skip-intro.js') }}"></script>
       <script src="{{ asset('js/videojs-seek-buttons.min.js') }}"></script>
       <script src="{{ asset('js/videojs-double-tap-skip.js') }}"></script>

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
       player.ready(function() {
           player.tech_.off('dblclick');
           setTimeout(() => {
               settings(player, videoObject)
           }, 100);

           player.src({
               src: "{{ route('embed.video.playback', ['userid' =>$video->user_id, 'filename'=> $video->file_name,'playlist' => $video->playback_url ])}}", // woring with hls and key
               type: 'application/x-mpegURL',
               withCredentials: true,
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

           player.seekButtons({
               forward: 10,
               back: 10
           });
           player.skipIntro({
               label: 'Skip Intro',
               skipTime: playerSkipIntroTime,
           });

           //If you want to start English as the caption automatically
           // player.one("play", function() {
           //     player.textTracks()[0].mode = "showing";
           // });

           player.on('ended', function() {
               console.log('ended == ');
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