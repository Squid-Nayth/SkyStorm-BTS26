<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::resource('notes', App\Http\Controllers\NoteController::class)
    ->only(['index', 'create', 'store', 'edit', 'update', 'destroy'])
    ->middleware('auth');

Route::resource('posts', App\Http\Controllers\PostController::class)
    ->only(['index', 'create', 'store', 'edit', 'update', 'destroy'])
    ->middleware('auth');
