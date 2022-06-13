@if(count($videos) > 0)
@foreach ($videos as $key => $video)
<div class="row border-bottom d-flex align-items-center" id="individualList" index="{{$video->id}}">
    <div class="col-md-1 p-3">
        <div class="d-flex align-items-center">
            <div class="form-check">
                <input class="form-check-input checkItem" type="checkbox" value="{{$video->id}}" name="checkItem">
            </div>
            <button class="btn btn-dark btn-sm">
                <strong>{{$key+1}}</strong>
            </button>
        </div>
    </div>
    <div class="col-md-3 p-3">
        <a href="{{ route('video.index', ['v' => $video->file_name])}}">
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
        <div class="mt-1">Status: <strong>
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
        <a href="{{ route('video.index',['v' => $video->file_name])}}">
            <button class="btn btn-info btn-sm text-white">
                <strong><i class="fas fa-info"></i></strong>
            </button>
        </a>
        <a href="/video/edit/{{$video->file_name}}">
            <button class="btn btn-primary btn-sm"><i class="fas fa-pencil"></i></button>
        </a>
        <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#staticBackdrop-{{$video->id}}"><i
                class="fas fa-trash-can"></i></button>
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
                <button type="button" class="btn btn-danger" onclick="deleteVideo('{{ $video->slug }}')">Delete</button>
            </div>
        </div>
    </div>
</div>
@endforeach
<div class="d-flex justify-content-center" id="pagination">
    {!! $videos->links() !!}
</div>
@else
<div class="row py-4">
    <div class="col-md-12">
        <div class="alert alert-danger text-center">
            <strong>No videos found!</strong>
        </div>
    </div>
</div>
@endif