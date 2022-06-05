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
        Route::post('/edit-video', [App\Http\Controllers\VideoController::class, 'updateVideoInfo'])->name('video.edit');
        Route::get('/{id}/status', [App\Http\Controllers\VideoController::class, 'videoTranscodeStatus'])->name('video.transcode.status');
        Route::post('/transcode/{id}', [App\Http\Controllers\VideoController::class, 'transcode'])->name('video.transcode');
        Route::get('/transcode-progress/{video_id}', [App\Http\Controllers\VideoController::class, 'getTranscodeProgress'])->name('video.transcode.progress');
        
        Route::get('/playback/{userid}/{filename}/{playlist}', function ($userid,$filename,$playlist) {
            return FFMpeg::dynamicHLSPlaylist()
                ->fromDisk('uploads')
                ->open("{$userid}/{$filename}/{$playlist}")
                ->setKeyUrlResolver(function ($key) use($userid,$filename) {
                    // \Log::info("setKeyUrlResolver key: {$key} %\n");
                    return route('video.key', ['userid' => $userid,'filename'=>$filename,'key' => $key]);
                })
                ->setPlaylistUrlResolver(function ($playlistFilename) use ($userid,$filename,$playlist) {
                    // \Log::info("playlistFilename: {$playlistFilename} %\n");
                    return route('video.playback', ['userid' => $userid,'filename'=>$filename,'playlist' => $playlistFilename]);
                })
                ->setMediaUrlResolver(function ($mediaFilename) use ($userid,$filename){
                    // \Log::info("mediaFilename: {$mediaFilename} %\n");
                    // return route('video.playback', ['userid' => $userid,'filename'=>$filename,'playlist' => $mediaFilename]);
                    return url("uploads/{$userid}/{$filename}/{$mediaFilename}");
                });
        })->name('video.playback');
        // ->middleware(['host']);

        Route::get('/secret/{userid}/{filename}/{key}', function ($userid,$filename,$key) {
            $Keypath = $userid.'/'.$filename.'/'.$key;
            return Storage::disk('uploads')->download($Keypath);
        })->name('video.key');
        // ->middleware(['host']);

    });

    
});


 Route::get('/playback/{userid}/{filename}/{playlist}', function ($userid,$filename,$playlist) {
    return FFMpeg::dynamicHLSPlaylist()
        ->fromDisk('uploads')
        ->open("{$userid}/{$filename}/{$playlist}")
        ->setKeyUrlResolver(function ($key) use($userid,$filename) {
            // \Log::info("setKeyUrlResolver key: {$key} %\n");
            return route('video.key', ['userid' => $userid,'filename'=>$filename,'key' => $key]);
        })
        ->setPlaylistUrlResolver(function ($playlistFilename) use ($userid,$filename,$playlist) {
            // \Log::info("playlistFilename: {$playlistFilename} %\n");
            return route('video.playback', ['userid' => $userid,'filename'=>$filename,'playlist' => $playlistFilename]);
        })
        ->setMediaUrlResolver(function ($mediaFilename) use ($userid,$filename){
            // \Log::info("mediaFilename: {$mediaFilename} %\n");
            // return route('video.playback', ['userid' => $userid,'filename'=>$filename,'playlist' => $mediaFilename]);
            return url("uploads/{$userid}/{$filename}/{$mediaFilename}");
        });
})->name('video.playback');
// ->middleware(['host']);

Route::get('/secret/{userid}/{filename}/{key}',  [App\Http\Controllers\VideoController::class, 'getAESKey'])->name('video.key');
Route::get('/embed/{slug}', [App\Http\Controllers\EmbedPlayerController::class, 'getEmbedPlayer'])->name('video.player.embed');