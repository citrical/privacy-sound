<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AudioController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\NoteController;
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
    Route::get('/audios/{audio}', [AudioController::class, 'show'])->name('audios.show');
    Route::delete('/audios/{audio}', [AudioController::class, 'destroy'])->name('audios.destroy');
    Route::patch('/audios/{audio}', [AudioController::class, 'update'])->name('audios.update');
    Route::get('/audios/{audio}/stream', [AudioController::class, 'stream'])->name('audios.stream');
    Route::post('/audios/{audio}/process', [AudioController::class, 'process'])->name('audios.process');
    Route::get('/audios/{audio}/stream-processed', [AudioController::class, 'streamProcessed'])->name('audios.stream-processed');
    Route::get('/audios/{audio}/download', [AudioController::class, 'download'])->name('audios.download');
    Route::post('/audios/{audio}/tags', [AudioController::class, 'attachTag'])->name('audios.tags.store');
    Route::delete('/audios/{audio}/tags/{tag}', [AudioController::class, 'detachTag'])->name('audios.tags.destroy');
    Route::delete('/segments/{segment}', [\App\Http\Controllers\SegmentController::class, 'destroy'])->name('segments.destroy');
    Route::post('/audios/{audio}/notes', [NoteController::class, 'store'])->name('notes.store');
    Route::resource('tags', TagController::class)->except(['create', 'edit', 'show']);
    Route::delete('/notes/{note}', [NoteController::class, 'destroy'])->name('notes.destroy');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('index');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');
    Route::delete('/orphans/{filename}', [AdminController::class, 'destroyOrphan'])->name('orphans.destroy');
    Route::post('/purge-processed', [AdminController::class, 'purgeProcessed'])->name('purge-processed');

});

require __DIR__ . '/auth.php';
