@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <div class="row">
                        <div class="col-md-6 float-start pt-2">
                            <h4>Encoding in progress</h4>
                        </div>
                        <div class="col-md-6 text-end">
                            <button id="uploadProgressBtn" class="btn btn-primary btn-sm">Transcoding...</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @foreach($newArray as $key => $value)
                    <div class="row my-3">
                        <div class="col-md-1 text-start">
                            <button class="btn btn-danger btn-sm">{{$value}}p</button>
                        </div>
                        <div class="col-md-10">
                            <div class="progress" style="height:2rem">
                                <div id="progress-bar-{{$value}}" class="progress-bar" role="progressbar"
                                    style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">100%
                                </div>
                            </div>
                        </div>
                        <div class="col-md-1 float-end">
                            <button class="btn btn-dark btn-sm">100%</button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script type="text/javascript">
var myInterval = setInterval(function() {
    getEncodingProgress()
}, 1000);

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$.ajax({
    url: "{{route('video.transcode',['id' => request()->id])}}",
    method: 'Post',
    type: 'Post',
    data: JSON.stringify({
        id: "{{request()->id}}",
        userId: "{{$video->user_id}}",
        fileName: "{{$video->file_name}}",
        fileNameWithExtension: "{{$video->playback_url}}",
    }),
    dataType: 'json',
    contentType: 'application/json',
    processData: false,
    success: function(result) {
        if (result.success == 'true') {
            clearInterval(myInterval);
            $('.progress-bar').css('width', '100%');
            $('.progress-bar').text('100%');
            setTimeout(() => {
                window.location.href = "{{route('video.index')}}";
            }, 2000);
        }
    },
    error: function(err) {
        // window.location.reload();
    }
});

function updateProgress(id, progress) {
    $('#progress-bar-' + id).css('width', progress + '%');
    $('#progress-bar-' + id).text(progress + '%');
}



function getEncodingProgress() {
    $.ajax({
        url: "{{route('video.transcode.progress',['video_id' => request()->id])}}",
        method: 'Get',
        type: 'Get',
        dataType: 'json',
        contentType: 'application/json',
        processData: false,
        success: function(result) {
            var progress = JSON.parse(result);
            if (progress.length > 0) {
                $.each(progress, function(key, value) {
                    updateProgress(value.file_format, value.progress);
                });
            } else {
                clearInterval(myInterval);
                $('progress-bar').css('width', '100%');
                $('progress-bar').text('100%');
            }
        }
    });
}
</script>
@endsection