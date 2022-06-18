@extends('layouts.app')
@section('style')
<link href="{{ asset('css/jquery.dm-uploader.css') }}" rel="stylesheet">

<style>
hr {
    margin-top: 2rem;
    margin-bottom: 2rem;
}

#filesDiv {
    overflow-y: auto !important;
    min-height: 100px;
    max-height: 300px;
}

@media (max-width: 768px) {
    #filesDiv {
        min-height: 0;
    }
}

.dm-uploader {
    border: 0.25rem dashed #A5A5C7;
    text-align: center;
}

.dm-uploader.active {
    border-color: red;

    border-style: solid;
}
</style>
@endsection
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <div class="row">
                        <div class="col-md-6 float-start pt-2">
                            <h4>Upload Video Here</h4>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <!-- Our markup, the important part here! -->
                            <div id="drag-and-drop-zone" class="dm-uploader">
                                <h3 class="mb-3 mt-3 text-muted">Drag &amp; drop video files here</h3>

                                <div class="btn btn-primary btn-block mb-3">
                                    <span>Open the file Browser</span>
                                    <input type="file" title="Click to add Files" />
                                </div>
                            </div>
                            <!-- /uploader -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-11 mt-3">
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
                    <div class="row">
                        <div class="col-md-12" id="filesDiv">
                            <ul class="list-unstyled p-2 d-flex flex-column col" id="files">
                                <li class="text-muted text-center empty">No files uploaded.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <!-- File item template -->
                            <script type="text/html" id="files-template">
                            <li class="media">
                                <div class="media-body mb-1">
                                    <p class="mb-2">Status: <span class="text-muted">Waiting</span></p>
                                    <div class="progress mb-2">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                                            role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0"
                                            aria-valuemax="100"></div>
                                    </div>
                                    <form method="POST" id="videoInfo" enctype="multipart/form-data">
                                        <input type="hidden" name="fileName" id="fileName" value="">
                                        <input type="hidden" name="fileNameWithExt" id="fileNameWithExt" value="">
                                        <input type="hidden" name="uploadDuration" id="uploadDuration" value="20">
                                        <div class="mb-3 row">
                                            <label for="staticEmail" class="col-sm-2 col-form-label">Title</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" name="title" id="VideoTitle"
                                                    value="">
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <label for="staticEmail" class="col-sm-2 col-form-label">Description</label>
                                            <div class="col-sm-10">
                                                <textarea class="form-control" name="description" id="VideoDescription"
                                                    rows="3"></textarea>
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <label for="staticEmail" class="col-sm-2 col-form-label">Poster</label>
                                            <div class="col-sm-10">
                                                <input name="poster" id="posterImage" type="file" vlaue=""
                                                    class="form-control">
                                            </div>
                                        </div>
                                    </form>
                                    <hr class="mt-2 mb-5" />
                                </div>
                            </li>
                            </script>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ asset('js/jquery.dm-uploader.js') }}"></script>
<script src="{{ asset('js/demo-ui.js') }}"></script>
<!-- <script src="{{ asset('js/demo-config.js') }}"></script> -->
<script type="text/javascript">
$(function() {
    /*
     * For the sake keeping the code clean and the examples simple this file
     * contains only the plugin configuration & callbacks.
     * 
     * UI functions ui_* can be located in: demo-ui.js
     */
    $('#drag-and-drop-zone').dmUploader({ //
        url: "{{route('video.fileupload')}}",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        maxFileSize: 300000000000, // 3 Megs 
        onDragEnter: function() {
            // Happens when dragging something over the DnD area
            this.addClass('active');
        },
        onDragLeave: function() {
            // Happens when dragging something OUT of the DnD area
            this.removeClass('active');
        },
        onInit: function() {
            console.log('Initializing ...');
            // Plugin is ready to use
            ui_add_log('Penguin initialized :)', 'info');
        },
        onComplete: function() {
            console.log('All pending transfers completed');
            // All files in the queue are processed (success or error)
            $('#createBtn').show();
        },
        onNewFile: function(id, file) {
            console.log('New file added with ID: ' + id);
            // When a new file is added using the file selector or the DnD area
            ui_multi_add_file(id, file);
        },
        onBeforeUpload: function(id) {
            // about tho start uploading a file
            ui_multi_update_file_status(id, 'uploading', 'Uploading...');
            ui_multi_update_file_progress(id, 0, '', true);
        },
        onUploadCanceled: function(id) {
            // Happens when a file is directly canceled by the user.
            ui_multi_update_file_status(id, 'warning', 'Canceled by User');
            ui_multi_update_file_progress(id, 0, 'warning', false);
        },
        onUploadProgress: function(id, percent) {
            console.log('Upload progress for file #' + id + ': ' + percent + '%');
            // Updating file progress
            ui_multi_update_file_progress(id, percent);
        },
        onUploadSuccess: function(id, data) {
            // A file was successfully uploaded
            ui_multi_update_file_status(id, 'success', 'Upload Complete');
            ui_multi_update_file_progress(id, 100, 'success', false);

            setTimeout(() => {
                $("#uploaderFile" + id).find("#fileName").val(data.fileName);
                $("#uploaderFile" + id).find("#fileNameWithExt").val(data.filePath);
            }, 100);
            console.log("onUploadSuccess", data);
        },
        onUploadError: function(id, xhr, status, message) {
            ui_multi_update_file_status(id, 'danger', message);
            ui_multi_update_file_progress(id, 0, 'danger', false);
        },
        onFallbackMode: function() {
            // When the browser doesn't support this plugin :(
            ui_add_log('Plugin cant be used here, running Fallback callback', 'danger');
        },
        onFileSizeError: function(file) {
            ui_add_log('File \'' + file.name + '\' cannot be added: size excess limit', 'danger');
        }
    });
});
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

function saveVideoInfo() {
    var postData = [];
    var videoInfo = new FormData();
    $('li.media').each(function() {
        var formData = $(this).find('form').serializeArray();
        // formData.push({
        //     name: 'poster',
        //     value: $(this).find("#posterImage")[0].files[0]
        // });
        // postData.push(formData);
        // appand form data to videoInfo

        $.each(formData, function(key, input) {
            videoInfo.append(input.name, input.value);
            videoInfo.append('poster', $("#posterImage")[0].files[0]);
        });
    });
    // postData to key value pair
    // var dataArray = [];
    // $.each(postData, function(index, value) {
    //     var data = {};
    //     $.each(value, function(index, value) {
    //         data[value.name] = value.value;
    //     });
    //     dataArray.push(data);
    // });
    // console.log(dataArray);


    // ajax post array to server
    $.ajax({
        url: "{{route('video.save.info')}}",
        method: 'POST',
        type: 'POST',
        data: videoInfo,
        success: function(result) {
            if (result.success == 'true') {
                // window.location.href = `/video/${result.lastInsertedId}/status`;
                console.log(result);
            } else {
                console.log(result.message);
            }
        },
        error: function(err) {
            // window.location.reload();
        }
    });
}
</script>
@endsection