<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmailSequenceController;

// public pages
// show that they are unsubscribed
Route::get('/unsbscribe',  [EmailSequenceController::class, 'unsubscribe']);

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/', [TestController::class, 'test'])->name('home');
    Route::get('/dashboard', [TestController::class, 'test'])->name('dashboard');
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



    Route::get('/testapi', [TestController::class, 'testApi']);
    Route::get('/email-sequence/{id}/edit', [EmailSequenceController::class, 'edit'])->name('email-sequences.edit');
    Route::get('/emailfirst/{email}',  [TestController::class, 'emailTest']);
    Route::get('/testmin', function () { return view('test'); });

});

require __DIR__.'/auth.php';
