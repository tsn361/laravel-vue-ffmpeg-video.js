@if($video->id)
<div class="container">
    <div class="row mt-4 p-sm-2">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    {{ $video->title }}
                </div>
                <div class="card-body">
                    <p class="py-1">{{ $video->description }}</p>
                    <p class="py-1">Created at: {{$video->created_at}}</p>
                    <p class="py-1 playBackUrlMaster">Playback URL:
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
@endif