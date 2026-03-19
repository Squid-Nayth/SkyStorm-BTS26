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

Route::post('/posts', [App\Http\Controllers\PostController::class, 'store'])->name('posts.store')->middleware('auth');
Route::delete('/posts/{post}', [App\Http\Controllers\PostController::class, 'destroy'])->name('posts.destroy')->middleware('auth');
