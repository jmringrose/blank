<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SequenceController;
use App\Http\Controllers\FormDataController;
use App\Http\Controllers\HomeController;

// public pages
// show that they are unsubscribed
Route::get('/unsbscribe',  [SequenceController::class, 'unsubscribe']);


// auth pages
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/', [TestController::class, 'test'])->name('home');
    Route::get('/home', [TestController::class, 'test'])->name('home');
    Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');

    // user stuff
    Route::post('/theme', [ProfileController::class, 'theme'])->name('profile.theme');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // the two data tables
    Route::get('/sequences', [SequenceController::class, 'index'])->name('sequences');
    Route::get('/formdata', [FormDataController::class, 'index'])->name('formdata');

    // display and or edit the marketing emails
    Route::get('/email-sequence/{id}/edit', [SequenceController::class, 'edit'])->name('email-sequences.edit');

});

require __DIR__.'/auth.php';
