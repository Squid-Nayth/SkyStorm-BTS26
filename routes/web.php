<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminReportController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ExploreController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfileSettingsController;
use App\Http\Controllers\ReportController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/explore', [ExploreController::class, 'index'])->name('explore');
Route::get('/members', [ProfileController::class, 'index'])->name('members.index');
Route::get('/users/{user}', [ProfileController::class, 'show'])->name('users.show');
Route::get('/users/{user}/followers', [ProfileController::class, 'followers'])->name('users.followers');
Route::get('/users/{user}/following', [ProfileController::class, 'following'])->name('users.following');
Route::get('/users/{user}/favorites', [FavoriteController::class, 'show'])->name('favorites.show');

Route::get('/register/verify', [RegisterController::class, 'showVerificationForm'])->name('register.verify.form');
Route::post('/register/verify', [RegisterController::class, 'verifyCode'])->name('register.verify.store');
Route::post('/register/verify/resend', [RegisterController::class, 'resendCode'])->name('register.verify.resend');
Route::get('/password/code', [ResetPasswordController::class, 'showCodeForm'])->name('password.code.form');
Route::post('/password/code', [ResetPasswordController::class, 'resetWithCode'])->name('password.code.update');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::resource('notes', App\Http\Controllers\NoteController::class)
    ->only(['index', 'create', 'store', 'edit', 'update', 'destroy'])
    ->middleware('auth');

Route::resource('posts', App\Http\Controllers\PostController::class)
    ->only(['index', 'create', 'store', 'edit', 'update', 'destroy'])
    ->middleware('auth');

Route::post('/users/{user}/follow', [App\Http\Controllers\FollowController::class, 'store'])->name('users.follow')->middleware('auth');
Route::delete('/users/{user}/unfollow', [App\Http\Controllers\FollowController::class, 'destroy'])->name('users.unfollow')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/favorites/visibility', [FavoriteController::class, 'updateVisibility'])->name('favorites.visibility');

    Route::get('/profile/edit', [ProfileSettingsController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileSettingsController::class, 'update'])->name('profile.update');

    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{user}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/{user}', [MessageController::class, 'store'])->name('messages.store');

    Route::post('/posts/{post}/likes', [LikeController::class, 'store'])->name('posts.likes.store');
    Route::delete('/posts/{post}/likes', [LikeController::class, 'destroy'])->name('posts.likes.destroy');

    Route::post('/posts/{post}/favorites', [FavoriteController::class, 'store'])->name('posts.favorites.store');
    Route::delete('/posts/{post}/favorites', [FavoriteController::class, 'destroy'])->name('posts.favorites.destroy');

    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('posts.comments.store');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

    Route::post('/posts/{post}/reports', [ReportController::class, 'store'])->name('posts.reports.store');

    Route::get('/admin/reports', [AdminReportController::class, 'index'])->name('admin.reports.index');
    Route::patch('/admin/reports/{report}', [AdminReportController::class, 'update'])->name('admin.reports.update');
});
