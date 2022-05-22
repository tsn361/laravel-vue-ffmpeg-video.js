<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/player-key', function (Request $request) {
//     Route::get('/video-create', [App\Http\Controllers\PlayerController::class, 'getKey'])->name('project.store');
// });

//Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/get-key', [App\Http\Controllers\PlayerController::class, 'getKey'])->name('project.store');
//});

