@extends('layouts.app')
@section('style')
<style>
.container {
    padding: 10px
}

.pagination {
    float: right;
    margin-top: 10px;
}

.ms-n5 {
    margin-left: -40px;
}

input[type=search]::-ms-clear {
    display: none;
    width: 0;
    height: 0;
}

input[type=search]::-ms-reveal {
    display: none;
    width: 0;
    height: 0;
}

/* clears the 'X' from Chrome */
input[type="search"]::-webkit-search-decoration,
input[type="search"]::-webkit-search-cancel-button,
input[type="search"]::-webkit-search-results-button,
input[type="search"]::-webkit-search-results-decoration {
    display: none;
    width: 0;
    height: 0;
}
</style>
@endsection
@section('content')
<div class="container shadow-sm">
    <div class="row bg-dark rounded-top">
        <div class="col-md-4 p-2 text-white">
            <div class="mt-2">
                List of videos
            </div>
        </div>
        <div class="col-md-6 p-2 text-end ">
            <div class="input-group">
                <input class="form-control border-end-0 border rounded-pill" type="search" value="" placeholder="Search"
                    aria-label="Search" id="search-input">
                <span class="input-group-append">
                    <button class="btn btn-outline-secondary bg-white border-bottom-0 border rounded-pill ms-n5"
                        type="button">
                        <i class="fa fa-search"></i>
                    </button>
                </span>
            </div>
        </div>
        <div class="col-md-2 p-2 text-end">
            <a class="btn btn-primary btn-sm mt-1" href="{{ route('video.upload') }}"> <i class="fas fa-plus"></i>
                Create</a>
        </div>
    </div>
    <div class="row border-bottom">
        <div class="col-md-1 p-3"><strong>ID</strong></div>
        <div class="col-md-3">&nbsp;</div>
        <div class="col-md-8 p-3"><strong>Title</strong></div>
    </div>
    <div id="videosLists">
        @include('video.videoListSearchData')
    </div>
    <div class="text-center py-4" id="searchStart">
        <div class="spinner-grow text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <div class="spinner-grow text-secondary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <div class="spinner-grow text-success" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <div class="spinner-grow text-danger" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <div class="spinner-grow text-warning" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <div class="spinner-grow text-info" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <div class="spinner-grow text-light" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

</div>

@endsection

@section('script')

<script>
$('#searchStart').hide();
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});


$('#search-input').keyup(function() {
    $('#searchStart').show();
    $('#videosLists').hide();
});
$('#search-input').keyup(debounce(function() {
    var value = $(this).val();
    value = value.trim()
    if (value.length > 0) {
        getSeachData(value);
    } else {
        getSeachData('all');
    }

}, 1000));


function debounce(func, wait, immediate) {
    var timeout;
    return function() {
        var context = this,
            args = arguments;
        var later = function() {
            timeout = null;
            if (!immediate) func.apply(context, args);
        };
        var callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) func.apply(context, args);
    };
};

function getSeachData(value, page = 1) {
    $.ajax({
        url: '/video/get-search?page=' + page + '&search=' + value,
        type: 'Get',
        data: null,
        success: function(data) {
            $('#videosLists').html(data);
            setTimeout(() => {
                $('#searchStart').hide();
                $('#videosLists').show();
            }, 500);

        }
    });
}

$(function() {
    $(document).on("click", "#pagination a", function(e) {
        e.preventDefault();
        $('#searchStart').show();
        $('#videosLists').hide();
        var page = $(this).attr('href').split('page=')[1];
        var value = $('#search-input').val();
        getSeachData(value, page);
    })
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