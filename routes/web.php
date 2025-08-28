<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrameworkController;
use App\Http\Controllers\ContentFrameworkController;

Route::get('/', function () {
    return view('welcome');
});
Auth::routes();

Route::get('/home', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();

Route::get('/home', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::resource('frameworks', FrameworkController::class);
Route::resource('frameworks.contents', ContentFrameworkController::class)->shallow();

