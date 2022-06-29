@extends('layouts.app')
@section('style')
<style>
.cp {
    cursor: pointer;
}
</style>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <div class="row">
                        <div class="col-md-6 float-start pt-2">
                            <h4>Edit Video</h4>
                        </div>
                        <div class="col-md-6 text-end">
                            <button id="createBtn" class="btn btn-primary btn-sm" type="submit"
                                onclick="saveVideoInfo()">Save</button>

                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="videoDetailsForm">
                        <form method="POST">
                            <input type="hidden" name="slug" id="slug" value="{{$video->slug}}">
                            <div class="mb-3 row">
                                <label for="staticEmail" class="col-sm-4 col-form-label">Title* </label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="VideoTitle" value="{{$video->title}}"
                                        required>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="staticEmail" class="col-sm-4 col-form-label">Description</label>
                                <div class="col-sm-8">
                                    <textarea class="form-control" id="VideoDescription"
                                        rows="3">{{$video->description}}</textarea>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="staticEmail" class="col-sm-4 col-form-label">Player Skip Intro Timer
                                    (sec)</label>
                                <div class="col-sm-8">
                                    <input type="number" class="form-control" min="5" id="VideoSkipIntroTimer"
                                        value="{{$video->skip_intro_time}}">
                                    <div class="mt-2">
                                        <small>
                                            <code>* Add minium 5 Second</code>
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="mb-3 row">
                                <h2>Player settings:</h3>
                                    <div class="col-sm-6">
                                        <div class="mt-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="1"
                                                    <?php if($video->stg_autoplay == '1') {echo 'checked' ;} ?>
                                                    id="stg_autoplay">
                                                <label class="form-check-label mt-1"
                                                    for="flexSwitchCheckChecked">Autoplay
                                                </label>
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="1"
                                                    <?php if($video->stg_muted == '1') {echo 'checked' ;} ?>
                                                    id="stg_muted">
                                                <label class="form-check-label mt-1"
                                                    for="flexSwitchCheckChecked">Muted</label>
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="1"
                                                    <?php if($video->stg_loop == '1') {echo 'checked' ;} ?>
                                                    id="stg_loop">
                                                <label class="form-check-label mt-1"
                                                    for="flexSwitchCheckChecked">Loop</label>
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="1"
                                                    <?php if($video->stg_autopause == '1') {echo 'checked' ;} ?>
                                                    id="stg_autopause">
                                                <label class="form-check-label mt-1 cp"
                                                    for="flexSwitchCheckChecked">Autopause
                                                    <i class="fas fa-question text-info" data-bs-toggle="tooltip"
                                                        data-bs-placement="top" title="Pause video on page scroll">
                                                    </i></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="mt-2">
                                            <p class="form-check-label my-1 cp" for="flexSwitchCheckChecked">Preload
                                                configration
                                                <i class="fas fa-question text-info" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" title="Pause video on page scroll">
                                                </i>
                                            </p>
                                            <select class="form-select form-select-sm" id="preloadConfigration">
                                                <option value="none"
                                                    <?php if($video->stg_preload_configration == 'none') {echo 'selected' ;} ?>>
                                                    None</option>
                                                <option value="auto"
                                                    <?php if($video->stg_preload_configration == 'auto') {echo 'selected' ;} ?>>
                                                    Auto</option>
                                                <option value="metadata"
                                                    <?php if($video->stg_preload_configration == 'metadata') {echo 'selected' ;} ?>>
                                                    Metadata</option>
                                            </select>
                                        </div>
                                    </div>
                            </div>
                            <hr>
                            <div class="mb-3 row">
                                <label for="staticEmail" class="col-sm-4 col-form-label">Allowed remote host</label>
                                <div class="col-sm-8">
                                    <div>
                                        <textarea class="form-control" id="allowHost"
                                            rows="3">{{$video->allow_hosts}}</textarea>
                                    </div>
                                    <div class="mt-2">
                                        <small>
                                            <p><code>* Add comma separated values: abc.com,google.com</code></p>
                                            <p><code>* If empty then all hosts are allowed</code></p>
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="staticEmail" class="col-sm-4 col-form-label">Custom Thumbnails</label>
                                <div class="col-sm-5">
                                    <input name="file" id="posterImage" type="file" vlaue="" class="form-control">
                                </div>
                                <div class="col-sm-3">
                                    <img id="posterImagePreview" src="{{$video->poster}}" alt="Poster Image"
                                        class="img-thumbnail" style="max-height:300px">
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

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});


function validFile(filename, filetype) {

    filename.toLowerCase();
    const ext = ['.mp4', '.webm', '.mkv', '.wmv', '.avi', '.avchd', '.flv', '.ts', '.mov'];
    const mimes = ['video/x-flv', 'video/webm', 'video/ogg', 'video/mp4', 'application/x-mpegURL', 'ideo/3gpp',
        'video/quicktime', 'video/x-msvideo', 'video/x-ms-wmv'
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


function saveVideoInfo() {
    var file = $("#posterImage")[0].files[0];
    var formData = new FormData();

    var title = $('#VideoTitle').val();

    if (title === '') {
        Swal.fire({
            title: 'Error',
            text: "Video Title can't be empty",
            icon: 'error',
            confirmButtonText: 'OK'
        })
        return false;
    }

    formData.append("slug", $('#slug').val());
    formData.append("title", title);
    formData.append("description", $('#VideoDescription').val());
    formData.append("allow_host", $('#allowHost').val());
    formData.append("stg_autoplay", $('#stg_autoplay:checked').val());
    formData.append("stg_muted", $('#stg_muted:checked').val());
    formData.append("stg_loop", $('#stg_loop:checked').val());
    formData.append("stg_autopause", $('#stg_autopause:checked').val());
    formData.append("stg_preload_configration", $('#preloadConfigration').val());

    formData.append("poster", file);
    if ($('#VideoSkipIntroTimer').val() <= 0) {
        formData.append("skip_intro_time", 0);
    } else {
        formData.append("skip_intro_time", $('#VideoSkipIntroTimer').val());
    }


    $.ajax({
        url: "{{route('video.edit')}}",
        method: 'POST',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function(result) {
            if (result.success == 'true') {

                Swal.fire({
                    title: 'Success',
                    text: "Video updated successfully",
                    icon: 'success',
                    confirmButtonText: 'OK'
                })

                setTimeout(() => {
                    window.location.href = `/video`;
                }, 2000);
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