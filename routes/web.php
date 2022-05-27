<?php

use Illuminate\Support\Facades\Route;
use ProtoneMedia\LaravelFFMpeg\Http\DynamicHLSPlaylist;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin', 'AdminController@index');
//Route::get('/superadmin', 'SuperAdminController@index');
Auth::routes();


Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    //Route::get('/project-create', [App\Http\Controllers\ProjectController::class, 'create_ui'])->name('project.create');
});

Route::prefix('video')->group(function () {
    Route::middleware(['auth'])->group(function () {
        Route::get('/index', [App\Http\Controllers\VideoController::class, 'index'])->name('video.index');
        Route::get('/upload', [App\Http\Controllers\VideoController::class, 'upload_UI'])->name('video.upload');
        Route::get('/{slug}', [App\Http\Controllers\VideoController::class, 'video_play_UI'])->name('video.play');
        Route::get('/edit/{slug}', [App\Http\Controllers\VideoController::class, 'edit_ui'])->name('video.edit.ui');
        Route::post('/delete/{slug}', [App\Http\Controllers\VideoController::class, 'videoDelete'])->name('video.delete');
        Route::post('/file-upload', [App\Http\Controllers\VideoController::class, 'fileUploadPost'])->name('video.fileupload');
        Route::post('/save-video-info', [App\Http\Controllers\VideoController::class, 'saveVideoInfo'])->name('video.save.info');
        Route::get('/{id}/status', [App\Http\Controllers\VideoController::class, 'videoTranscodeStatus'])->name('video.transcode.status');
        Route::post('/transcode/{id}', [App\Http\Controllers\VideoController::class, 'transcode'])->name('video.transcode');
        Route::get('/transcode-progress/{video_id}', [App\Http\Controllers\VideoController::class, 'getTranscodeProgress'])->name('video.transcode.progress');
    
        Route::get('/video/secret/{key}', function ($key) {
            return Storage::disk('uploads')->download($key);
        })->name('video.key');

        Route::get('/playback/{playlist}', function ($playlist) {
            return FFMpeg::dynamicHLSPlaylist()
                ->fromDisk('uploads')
                ->open("3/1653517596/master.m3u8")
                ->setKeyUrlResolver(function ($key) {
                    \Log::info("key: {$key} %\n");
                    return route('video.key', ['key' => $key]);
                })
                ->setMediaUrlResolver(function ($mediaFilename) {
                    \Log::info("mediaFilename: {$mediaFilename} %\n");
                    return Storage::disk('uploads')->url("3/1653517596/master.m3u8");
                })
                ->setPlaylistUrlResolver(function ($playlistFilename)  {
                    \Log::info("playlistFilename: {$playlistFilename} %\n");
                    return route('video.playback', ['playlist' => $playlistFilename]);
                });
            // return FFMpeg::dynamicHLSPlaylist()
            //     ->fromDisk('uploads')
            //     ->open("{$id}/{$file_name}/{$playlist}")
            //     ->setKeyUrlResolver(function ($key) {
            //         return route('video.key', ['key' => $key]);
            //     })
            //     ->setMediaUrlResolver(function ($mediaFilename) use ($id,$file_name) {
            //         return Storage::disk('uploads')->url("{$id}/{$file_name}/{$mediaFilename}");
            //     })
            //     ->setPlaylistUrlResolver(function ($playlistFilename) use ($id,$file_name) {
            //         return route('video.playback', ['playlist' => $playlistFilename]);
            //     });
        })->name('video.playback');
    }); 
});


Route::get('/set',[App\Http\Controllers\VideoController::class,'setcookie2']);