
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <video
                id="hls-video"
                class="video-js"
                controls
                preload="auto"
                poster="/uploads/{{{{$video->user_id}}/{{$video->file_name}}/{{$video->poster}}}}"
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
    </div>
</div>
