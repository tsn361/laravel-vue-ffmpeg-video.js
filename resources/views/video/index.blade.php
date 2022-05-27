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
        <div class="col-md-5 p-3 ts-sm">
            <div>
                <strong>{{$video->title}}</strong>
            </div>
            <div class="mt-2">Created by: {{$video->created_by}}</div>
            <div class="mt-1">Date: <strong>{{$video->created_at}}</strong></div>
            <div class="mt-1">Video Duration: <strong>{{$video->video_duration}}</strong></div>
            <div class="mt-1">Upload duration: <strong>{{$video->upload_duration}}</strong></div>
        </div>
        <div class="col-md-3 text-end p-3">
            <button class="btn btn-info btn-sm text-white">
                <strong><i class="fas fa-info"></i></strong>
            </button>
            <button class="btn btn-primary btn-sm"><i class="fas fa-pencil"></i></button>
            <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#staticBackdrop"><i
                    class="fas fa-trash-can"></i></button>
        </div>
    </div>
    @endforeach

    <!-- Vertically centered modal -->
    <div class="modal-dialog modal-dialog-centered" id="staticBackdrop" data-bs-backdrop="static"
        data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div>
            <p>Are sure about deleting this video?</p>
        </div>
    </div>
</div>
@endsection