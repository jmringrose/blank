<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RecordController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () { return view('welcome'); });
Route::get('/home', function () { return view('welcome'); });

Route::get('/dashboard', function () { return view('dashboard'); })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    Route::get('/test', [HomeController::class, 'test'])->name('test');
    Route::get('/test2', [HomeController::class, 'secondTest'])->name('test2');
    Route::get('/test3', [HomeController::class, 'thirdTest'])->name('test3');


    Route::post('/theme', [ProfileController::class, 'theme'])->name('profile.theme');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/history', [RecordController::class, 'history'])->name('records.history');
});

require __DIR__.'/auth.php';
