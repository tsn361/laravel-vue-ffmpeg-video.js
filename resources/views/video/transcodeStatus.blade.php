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
                    {{ request()->id }}
                    @foreach($newArray as $key => $value)
                    <div class="row my-3">
                        <div class="col-md-1 text-start">
                            <button class="btn btn-danger btn-sm">{{$value}}p</button>
                        </div>
                        <div class="col-md-10">
                            <div class="progress" style="height:2rem">
                                <div id="progress-bar-{{$value}}" class="progress-bar" role="progressbar"
                                    style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">100%
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
function uploadProgressHandler(event) {
    if (event.lengthComputable) {
        $("#videoFile").hide();
        var max = event.total;
        var current = event.loaded;
        var Percentage = Math.round((current * 100) / max);
        console.log(Percentage);

        $('.UploadFormProgress').show();
        $('#progress-bar').width(Percentage + '%');
        $('#progress-bar').html(Percentage + '%');
        $('#uploadProgressBtn').show();
        $('#uploadProgressBtn').html('Uploading: ' + Percentage + '%');
    }
}

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$.ajax({
    url: "{{route('video.transcode',['id' => request()->id])}}",
    method: 'Get',
    type: 'Get',
    data: null,
    contentType: false,
    processData: false,
    success: function(result) {

    },
    error: function(err) {
        // window.location.reload();
    }
});
</script>
@endsection