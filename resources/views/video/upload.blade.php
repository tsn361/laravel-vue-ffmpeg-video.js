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
                            <input type="hidden" name="fileNameWithExt" id="fileNameWithExt" value="">
                            <input type="hidden" name="uploadDuration" id="uploadDuration" value="20">
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
    $("#videoFile").show();
    $("#videoFile").val('');
    $('#progress-bar').width(0 + '%');
    $('#progress-bar').html('');
    $('#uploadProgressBtn').hide();
    $('#uploadProgressBtn').html('');
    $('.UploadFormProgress').hide();
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
        //console.log("Percentage=> ",Percentage);
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


function validFile(filename, filetype) {

    filename.toLowerCase();
    const ext = ['.mp4', '.webm', '.mkv', '.wmv', '.avi', '.avchd', '.flv', '.ts', '.mov'];
    const mimes = ['video/x-flv', 'video/webm', 'video/ogg', 'video/mp4', 'application/x-mpegURL', 'ideo/3gpp',
        'video/quicktime', 'video/x-msvideo', 'video/x-ms-wmv', 'video/x-matroska', 'video/x-msvideo'
    ];


    const filenameIsValid = ext.some(el => filename.endsWith(el));
    const filetypeIsValid = mimes.indexOf(filetype);

    //console.log("filetypeIsValid=> ", filetypeIsValid, filetype);

    if (filenameIsValid && filetypeIsValid !== -1) {
        return true;
    } else {
        return false;
    }

}

$('#videoFile').change(function() {
    event.preventDefault();

    var file = $("#videoFile")[0].files[0];

    if (validFile(file.name, file.type)) {

        var startTime, EndTime;
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
            beforeSend: function() {
                startTime = new Date().getTime();
            },
            success: function(result, status, jqXHR) {
                console.log("jqXHR=> ", jqXHR);
                resetUploadForm()
                if (result.success == 'true') {
                    showVideoDetailsForm();
                    $('#fileName').val(result.fileName);
                    $('#fileNameWithExt').val(result.filePath);
                } else {
                    console.log(result.message);
                    //window.location.reload();
                    Swal.fire({
                        title: 'Error',
                        text: result.message,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    })
                    resetUploadForm()
                }
            },
            error: function(err) {
                console.log(err);
                Swal.fire({
                    title: 'Error',
                    text: err.responseJSON.message,
                    icon: 'error',
                    confirmButtonText: 'OK'
                })

                resetUploadForm()
            },
            complete: function() {
                endTime = new Date().getTime();
                var timeTaken = (endTime - startTime) / 1000;
                $('#uploadDuration').val(timeTaken);
            }
        });
    } else {
        Swal.fire({
            title: 'Error!',
            text: "Select a valid video file",
            icon: 'error',
            confirmButtonText: 'OK'
        })
    }
});


function saveVideoInfo() {
    var formData = new FormData();
    formData.append("fileName", $('#fileName').val());
    formData.append("fileNameWithExt", $('#fileNameWithExt').val());
    formData.append("title", $('#VideoTitle').val());
    formData.append("description", $('#VideoDescription').val());
    formData.append("uploadDuration", $('#uploadDuration').val());

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