<?php

use App\Models\Post;
use App\Models\Video;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;


Breadcrumbs::for('home', function (BreadcrumbTrail $trail) {
    $trail->push('Home', route('home'));
});

Breadcrumbs::for('video-manage', function (BreadcrumbTrail $trail) {
    $trail->push('Video Management', route('video.index'));
});

Breadcrumbs::for('video', function (BreadcrumbTrail $trail, Video $video) {
    $trail->parent('home');
    $trail->parent('video-manage');
    $trail->push($video->title, route('video.play', $video->slug));
});