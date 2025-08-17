<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SequenceController;
use App\Http\Controllers\FormDataController;
use App\Http\Controllers\HomeController;

// public pages
// show that they are unsubscribed
Route::get('/unsubscribe/marketing/{token}', [\App\Http\Controllers\MarketingUnsubscribeController::class, 'unsubscribe']);
Route::get('/unsubscribe/newsletter/{token}', [\App\Http\Controllers\NewsletterUnsubscribeController::class, 'unsubscribe']);
// legacy routes
Route::get('/unsubscribe', [SequenceController::class, 'unsubscribe']);
Route::get('/unsbscribe',  [SequenceController::class, 'unsubscribe']);


// email previews (auth required)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/preview/marketing/{step?}', [\App\Http\Controllers\EmailPreviewController::class, 'marketing'])->name('email.preview.marketing');
    Route::get('/preview/newsletter/{step?}', [\App\Http\Controllers\EmailPreviewController::class, 'newsletter'])->name('email.preview.newsletter');
});

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
    
    // newsletter sequences
    Route::get('/newsletter-sequences', [\App\Http\Controllers\NewsletterSequenceController::class, 'index'])->name('newsletter-sequences.index');
    Route::get('/newsletter-sequence/{id}/edit', [\App\Http\Controllers\NewsletterSequenceController::class, 'edit'])->name('newsletter-sequences.edit');
    Route::put('/newsletter-sequence/{id}', [\App\Http\Controllers\NewsletterSequenceController::class, 'update'])->name('newsletter-sequences.update');
    
    // newsletter steps
    Route::get('/newsletter-steps', [\App\Http\Controllers\NewsletterStepController::class, 'index'])->name('newsletter-steps.index');
    
    // newsletter editor
    Route::get('/newsletter-editor', [\App\Http\Controllers\NewsletterEditorController::class, 'index'])->name('newsletter-editor.index');
    Route::get('/newsletter-editor/create', [\App\Http\Controllers\NewsletterEditorController::class, 'create'])->name('newsletter-editor.create');
    Route::get('/newsletter-editor/{id}/edit', [\App\Http\Controllers\NewsletterEditorController::class, 'edit'])->name('newsletter-editor.edit');
    Route::post('/newsletter-editor', [\App\Http\Controllers\NewsletterEditorController::class, 'store'])->name('newsletter-editor.store');
    Route::put('/newsletter-editor/{id}', [\App\Http\Controllers\NewsletterEditorController::class, 'update'])->name('newsletter-editor.update');
    
    // marketing editor
    Route::get('/marketing-editor', [\App\Http\Controllers\MarketingEditorController::class, 'index'])->name('marketing-editor.index');
    Route::get('/marketing-editor/create', [\App\Http\Controllers\MarketingEditorController::class, 'create'])->name('marketing-editor.create');
    Route::get('/marketing-editor/{id}/edit', [\App\Http\Controllers\MarketingEditorController::class, 'edit'])->name('marketing-editor.edit');
    Route::post('/marketing-editor', [\App\Http\Controllers\MarketingEditorController::class, 'store'])->name('marketing-editor.store');
    Route::put('/marketing-editor/{id}', [\App\Http\Controllers\MarketingEditorController::class, 'update'])->name('marketing-editor.update');

});

require __DIR__.'/auth.php';
