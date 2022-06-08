@extends('layouts.app')
@section('style')
<style>
@import url(https://fonts.googleapis.com/css?family=Questrial);
@import url(https://fonts.googleapis.com/css?family=Dancing+Script:700);

.pageContent {
    padding: 0 !important;
}

a {
    color: #9c27b0;
    -webkit-transition: all .35s;
    -moz-transition: all .35s;
    transition: all .35s;
}

a:hover,
a:focus {
    color: #9c27c1;
    outline: 0;
}

.cursive {
    font-family: 'Dancing Script', cursive;
    text-transform: none;
}


#hero {
    overflow: hidden;
    position: relative;
    min-height: auto;
    text-align: center;
    color: #fff;
    width: 100%;
    background-color: #c9c9c9;
    background-image: url('https://splashbase.s3.amazonaws.com/lifeofpix/regular/Life-of-Pix-free-stock-photos-Slate-Stone-Xeromatic-1440x953.jpg');
    background-position: center;
    -webkit-background-size: cover;
    -moz-background-size: cover;
    background-size: cover;
    -o-background-size: cover;
    font-family: 'Questrial', 'Helvetica Neue', Arial, sans-serif;
    background-color: #282828;
    color: #d3d3d3;
    webkit-tap-highlight-color: #222;
}


.hero-content .inner h1 {
    margin-top: 0;
    margin-bottom: 0;
}

.hero-content .inner p {
    margin-bottom: 50px;
    font-size: 16px;
    font-weight: 300;
    color: rgba(255, 255, 255, 0.7);
}

.header-content {
    position: absolute;
    top: 50%;
    padding: 0 50px;
    -webkit-transform: translateY(-50%);
    -ms-transform: translateY(-50%);
    transform: translateY(-50%);
    text-align: center;
}

.btn.btn-primary {
    background-color: transparent;
    border: 1px solid #f0f0f0;
    border-color: #f0f0f0;
    -webkit-transition: all .35s;
    -moz-transition: all .35s;
    transition: all .35s;
}

.btn.btn-primary:hover {
    opacity: 0.7;
}

.btn {
    border: 0;
    border-radius: 290px;
    font-family: 'Helvetica Neue', Arial, sans-serif;
}

.btn-xl {
    padding: 15px 30px;
    font-size: 20px;
}
</style>
@endsection
@section('content')
<div id="hero" style="min-height: 100vh;">
    <div class="header-content">
        <div class="inner">
            <h1 class="cursive">Simple, One Page Design</h1>
            <h4>A free landing page theme with video background</h4>

            <a href="#" class="btn btn-primary btn-xl">Toggle Video</a> &nbsp; <a href="#one"
                class="btn btn-primary btn-xl page-scroll">Get Started</a>
        </div>
    </div>
</div>

@endsection