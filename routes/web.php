<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\BootyRequestController;
use App\Http\Controllers\FilesController;
use App\Http\Controllers\JoinSoundsController;
use App\Http\Controllers\SoundsController;
use App\Http\Controllers\TorrentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VideosController;
use Laravel\Socialite\Facades\Socialite;

use Illuminate\Support\Facades\Auth;

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
    return view('home');
});

Route::get('/request', [BootyRequestController::class, 'index']);
Route::get('/request/fill/{id}', [BootyRequestController::class, 'fill']);
Route::get('/request/delete/{id}', [BootyRequestController::class, 'delete']);

Route::get('/files', [FilesController::class, 'index']);
Route::get('/files/upload', function(){
    return view('files.upload');
})->name('files.upload');
Route::post('/files/upload', [FilesController::class, 'upload']);
Route::post('/files/delete', [FilesController::class, 'delete']);

Route::get('/videos', [VideosController::class, 'index']);
Route::get('/videos/download', [VideosController::class, 'getDownloadInfo']);
Route::get('/videos/upload', function(){
    return view('videos.upload');
})->name('videos.upload');
Route::post('/videos/upload', [VideosController::class, 'upload']);
Route::post('/videos/download', [VideosController::class, 'download']);
Route::post('/videos/delete', [VideosController::class, 'delete']);

Route::get('/joinsounds', [JoinSoundsController::class, 'index']);
Route::get('/joinsounds/upload', function(){
    return view('joinsounds.upload');
})->name('joinsounds.upload');
Route::post('/joinsounds/upload', [JoinSoundsController::class, 'upload']);
Route::post('/joinsounds/delete', [JoinSoundsController::class, 'delete']);
Route::get('/joinsounds/check/{id}', [JoinSoundsController::class, 'toggleCheck']);




Route::get('/sounds', [SoundsController::class, 'index']);
Route::get('/sounds/upload', function(){
    return view('sounds.upload');
})->name('sounds.upload');
Route::post('/sounds/upload', [SoundsController::class, 'upload']);
Route::post('/sounds/delete', [SoundsController::class, 'delete']);
Route::post('/sounds/editCommandName', [SoundsController::class, 'editCommandName']);

Route::get('/torrents', function() {
    return view('torrents.index');
})->name('torrents.index');
Route::post('/torrents/search', [TorrentController::class, 'doSearch']);
Route::post('/torrents/download', [TorrentController::class, 'downloadLink']);


Route::get('/auth/redirect', function () {
    return Socialite::driver('discord')->redirect();
});
Route::get('/auth/logout', function () {
    Auth::logout();
    return back();
});

Route::get('/auth/callback', [UserController::class, 'update']);