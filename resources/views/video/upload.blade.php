@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <div class="row">
                        <div class="col-md-6 float-start pt-2">
                            <h4>Create New Video</h4>
                        </div>
                        <div class="col-md-6 text-end">
                            <button id="createBtn" style="display:none" class="btn btn-primary btn-sm" type="submit"
                                onclick="saveVideoInfo()">+Create</button>
                            <button id="uploadProgressBtn" style="display:none" class="btn btn-primary btn-sm"></button>
                        </div>
                    </div>
                </div>
                <div class="card-body">

                    <div class="UploadForm">
                        <div class="mb-3 row">
                            <label for="staticEmail" class="col-sm-2 col-form-label">Video</label>
                            <div class="col-sm-10">
                                <input name="file" id="videoFile" type="file" vlaue="" class="form-control">
                                <div class="progress UploadFormProgress" style="display:none;height:2rem">
                                    <div id="progress-bar" class="progress-bar" role="progressbar" aria-valuemin="0"
                                        aria-valuemax="100" style="width:0%;">
                                        0%
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="videoDetailsForm" style="display:none">
                        <form method="POST">
                            <input type="hidden" name="fileName" id="fileName" value="">
                            <div class="mb-3 row">
                                <label for="staticEmail" class="col-sm-2 col-form-label">Video</label>
                                <div class="col-sm-10">
                                    <div class="progress" style="height:2rem">
                                        <div class="progress-bar" role="progressbar" style="width: 100%;"
                                            aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">100%</div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="staticEmail" class="col-sm-2 col-form-label">Title</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="VideoTitle" value="">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="staticEmail" class="col-sm-2 col-form-label">Description</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" id="VideoDescription" rows="3"></textarea>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script type="text/javascript">
function resetUploadForm() {
    $("#videoFile").val('');
    $('#progress-bar').width(0 + '%');
    $('#progress-bar').html('');
    $('#uploadProgressBtn').hide();
    $('#uploadProgressBtn').html('');
}

function showVideoDetailsForm() {
    $('.UploadForm').hide();
    $('.videoDetailsForm').show();
    $('#createBtn').show();
}

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

$('#videoFile').change(function() {
    event.preventDefault();
    var file = $("#videoFile")[0].files[0];
    var formData = new FormData();
    formData.append("file", file);

    $.ajax({
        url: "{{route('video.fileupload')}}",
        method: 'POST',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        xhr: function() {
            console.log('xhr');
            var xhr = new window.XMLHttpRequest();
            xhr.upload.addEventListener("progress",
                uploadProgressHandler,
                false
            );
            return xhr;
        },
        success: function(result) {
            resetUploadForm()
            if (result.success == 'true') {
                showVideoDetailsForm();
                $('#fileName').val(result.fileName);
            } else {
                console.log(res.message);
                window.location.reload();
            }
        },
        error: function(err) {
            window.location.reload();
        }
    });
});


function saveVideoInfo() {
    var formData = new FormData();
    formData.append("fileName", $('#fileName').val());
    formData.append("title", $('#VideoTitle').val());
    formData.append("description", $('#VideoDescription').val());

    $.ajax({
        url: "{{route('video.save.info')}}",
        method: 'POST',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function(result) {
            if (result.success == 'true') {
                window.location.href = `/video/${result.lastInsertedId}/status`;
                console.log(result);
            } else {
                console.log(res.message);
            }
        },
        error: function(err) {
            // window.location.reload();
        }
    });
}
</script>
@endsection