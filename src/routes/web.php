<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AudioController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/audios', [AudioController::class, 'index'])->name('audios.index');
    Route::post('/audios', [AudioController::class, 'store'])->name('audios.store');
    Route::delete('/audios/{audio}', [AudioController::class, 'destroy'])->name('audios.destroy');
    Route::get('/audios/{audio}/stream', [AudioController::class, 'stream'])->name('audios.stream');
    Route::get('/audios/{audio}', [AudioController::class, 'show'])->name('audios.show');
    Route::post('/audios/{audio}/process', [AudioController::class, 'process'])->name('audios.process');
    Route::get('/audios/{audio}/stream-processed', [AudioController::class, 'streamProcessed'])->name('audios.stream-processed');
    Route::get('/audios/{audio}/download', [AudioController::class, 'download'])->name('audios.download');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('index');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');
    Route::delete('/orphans/{filename}', [AdminController::class, 'destroyOrphan'])->name('orphans.destroy');
});

require __DIR__.'/auth.php';
