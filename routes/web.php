<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\RecordController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmailSequenceController;


Route::get('/', function () { return view('welcome'); });
Route::get('/testmin', function () { return view('test'); });
Route::get('/dashboard', function () { return view('dashboard'); })->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/emailfirst/{email}',  [TestController::class, 'emailTest']);

Route::middleware('auth')->group(function () {

    Route::get('/home', [TestController::class, 'test'])->name('home');
    Route::get('/test', [TestController::class, 'test'])->name('test');
    Route::get('/test2', [TestController::class, 'secondTest'])->name('test2');
    Route::get('/test3', [TestController::class, 'thirdTest'])->name('test3');
    Route::get('/test4', [TestController::class, 'fourthTest'])->name('test4');
    Route::get('/history', [TestController::class, 'history'])->name('records.history');


    Route::post('/theme', [ProfileController::class, 'theme'])->name('profile.theme');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/email-sequences/{id}/edit', [EmailSequenceController::class, 'edit'])->name('email-sequences.edit');


});

require __DIR__.'/auth.php';
