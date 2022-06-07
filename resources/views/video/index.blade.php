@extends('layouts.app')

@section('content')
<div class="container shadow-sm">
    <div class="row bg-dark rounded-top">
        <div class="col-md-6 p-2 text-white">
            <div class="mt-2">
                List of videos
            </div>
        </div>
        <div class="col-md-6 p-2 text-end ">
            <a class="btn btn-primary btn-sm" href="{{ route('video.upload') }}"> <i class="fas fa-plus"></i> Create</a>
        </div>
    </div>
    <div class="row border-bottom">
        <div class="col-md-1 p-3"><strong>ID</strong></div>
        <div class="col-md-3">&nbsp;</div>
        <div class="col-md-8 p-3"><strong>Title</strong></div>
    </div>

    @foreach ($videos as $video)
    <div class="row border-bottom">
        <div class="col-md-1 p-3">
            <button class="btn btn-dark btn-sm">
                <strong>{{$video->id}}</strong>
            </button>
        </div>
        <div class="col-md-3 p-3">
            <a href="{{ route('video.play',['slug' => $video->slug])}}">
                <img src="/uploads/{{$video->user_id}}/{{$video->file_name}}/{{$video->poster}}" />
            </a>
        </div>
        <div class="col-md-6 p-3 ts-sm">
            <div>
                <strong>{{$video->title}}</strong>
            </div>
            <div class="mt-2">Created by: {{$video->created_by}}</div>
            <div class="mt-1">Date: <strong>{{$video->created_at}}</strong></div>
            <div class="mt-1">Video Duration: <strong>{{$video->video_duration}}</strong></div>
            <div class="mt-1">Upload duration: <strong>{{$video->upload_duration}}</strong></div>
            <div class="mt-1">Video Transcode Status: <strong>
                    @if($video->is_transcoded == 0)
                    <a href="javascript:void(0)" class="badge bg-warning text-center text-light">Transcoding Not
                        Attempted</a>
                    @elseif($video->is_transcoded == 1)
                    <a href="javascript:void(0)" class="badge bg-success text-center text-light">Transcoded</a>
                    @elseif($video->is_transcoded == 2)
                    <a href="javascript:void(0)" class="badge bg-danger text-center text-light">Transcoding Failed</a>
                    @endif

                </strong></div>
        </div>
        <div class="col-md-2 text-end p-3">
            <a href="{{ route('video.play',['slug' => $video->slug])}}">
                <button class="btn btn-info btn-sm text-white">
                    <strong><i class="fas fa-info"></i></strong>
                </button>
            </a>
            <a href="/video/edit/{{$video->slug}}">
                <button class="btn btn-primary btn-sm"><i class="fas fa-pencil"></i></button>
            </a>
            <button class="btn btn-danger btn-sm" data-bs-toggle="modal"
                data-bs-target="#staticBackdrop-{{$video->id}}"><i class="fas fa-trash-can"></i></button>
        </div>
    </div>
    <!-- Vertically centered modal -->
    <div class="modal fade" id="staticBackdrop-{{$video->id}}" data-bs-backdrop="static" data-bs-keyboard="false"
        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Video</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this '{{$video->title}}' video?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                    <button type="button" class="btn btn-danger"
                        onclick="deleteVideo('{{ $video->slug }}')">Delete</button>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection

@section('script')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script>
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

function deleteVideo(slug) {
    $.ajax({
        url: '/video/delete/' + slug,
        type: 'Post',
        success: function(result) {
            if (result.success == 'true') {
                window.location.href = "{{route('video.index')}}";
            }

        }
    })
}
</script>
@endsection