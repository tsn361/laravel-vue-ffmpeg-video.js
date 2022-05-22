@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <projects :projects="{{json_encode($data)}}"></projects>
                </div>
            </div>
        </div>
    </div>
@endsection
