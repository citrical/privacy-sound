<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AudioController;
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
});

require __DIR__.'/auth.php';
