<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ChirpController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [ChirpController::class,'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});



Route::group(['prefix' => 'chirps'], function () {
    Route::middleware('auth')->group(function () {
        Route::get('/create', [ChirpController::class, 'create'])->name('chirps.create');
        Route::post('/create', [ChirpController::class, 'store'])->name('chirps.store');
        Route::delete('/destroy/{id}', [ChirpController::class, 'destroy'])->name('chirps.destroy');
        Route::get('/edit/{id}', [ChirpController::class, 'edit'])->name('chirps.edit');
        Route::put('/update/{id}', [ChirpController::class, 'update'])->name('chirps.update');
        Route::get('/{id}', [ChirpController::class, 'show'])->name('chirps.show');
        Route::post('comment/{id}', [ChirpController::class, 'comment'])->name('chirps.comment');
    });
});


require __DIR__ . '/auth.php';
