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
            <button class="btn btn-primary btn-sm"> <i class="fas fa-plus"></i> Create</button>
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
            <img src="/uploads/{{$video->user_id}}/{{$video->file_name}}/{{$video->poster}}" />
        </div>
        <div class="col-md-5 p-3 ts-sm">
            <div>
                <strong>{{$video->title}}</strong>
            </div>
            <div class="mt-2">Created by: {{$video->createdBy}}</div>
            <div class="mt-1">Date: <strong>{{$video->created_at}}</strong></div>
            <div class="mt-1">Duration: <strong>{{$video->video_duration}}</strong></div>
            <div class="mt-1">Proccesed in: <strong>{{$video->process_time}}</strong></div>
        </div>
        <div class="col-md-3 text-end p-3">
            <button class="btn btn-info btn-sm text-white">
                <strong><i class="fas fa-info"></i></strong>
            </button>
            <button class="btn btn-primary btn-sm"><i class="fas fa-pencil"></i></button>
            <button class="btn btn-danger btn-sm"><i class="fas fa-trash-can"></i></button>
        </div>
    </div>
    @endforeach
</div>
@endsection