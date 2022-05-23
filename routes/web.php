<?php

use Illuminate\Support\Facades\Route;

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
        Route::get('/detail/{slug}', [App\Http\Controllers\VideoController::class, 'view'])->name('video.detail');
        Route::get('/edit/{slug}', [App\Http\Controllers\VideoController::class, 'edit_ui'])->name('video.edit.ui');
        Route::post('/file-upload', [App\Http\Controllers\VideoController::class, 'fileUploadPost'])->name('video.fileupload');
        Route::post('/save-video-info', [App\Http\Controllers\VideoController::class, 'saveVideoInfo'])->name('video.save.info');
        Route::get('/{id}/status', [App\Http\Controllers\VideoController::class, 'videoTranscodeStatus'])->name('video.transcode.status');
        Route::get('/transcode/{id}', [App\Http\Controllers\VideoController::class, 'transcode'])->name('video.transcode');
    }); 
});