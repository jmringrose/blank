<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SequenceController;
use App\Http\Controllers\FormDataController;
use App\Http\Controllers\HomeController;

// public pages - unsubscribe routes
Route::get('/unsubscribe/marketing/{token}', [\App\Http\Controllers\MarketingUnsubscribeController::class, 'unsubscribe']);
Route::get('/unsubscribe/newsletter/{token}', [\App\Http\Controllers\NewsletterUnsubscribeController::class, 'unsubscribe']);
Route::get('/unsubscribe/question/{token}', [\App\Http\Controllers\QuestionUnsubscribeController::class, 'unsubscribe'])->name('unsubscribe');

// email previews (auth required)
Route::middleware(['auth', 'verified'])->group(function () {



});

// auth pages
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/', [HomeController::class, 'dashboard']);
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
    Route::post('/newsletter-sequences/create', [\App\Http\Controllers\NewsletterSequenceController::class, 'store'])->name('newsletter-sequences.store');
    Route::get('/newsletter-sequence/{id}/edit', [\App\Http\Controllers\NewsletterSequenceController::class, 'edit'])->name('newsletter-sequences.edit');
    Route::put('/newsletter-sequence/{id}', [\App\Http\Controllers\NewsletterSequenceController::class, 'update'])->name('newsletter-sequences.update');

    // newsletter steps
    Route::get('/newsletter-steps/data', [\App\Http\Controllers\NewsletterStepController::class, 'data']);
    Route::get('/newsletter-steps', [\App\Http\Controllers\NewsletterStepController::class, 'index'])->name('newsletter-steps.index');
    Route::post('/newsletter-steps', [\App\Http\Controllers\NewsletterStepController::class, 'store'])->name('newsletter-steps.store');
    Route::put('/newsletter-steps/{id}', [\App\Http\Controllers\NewsletterStepController::class, 'update'])->name('newsletter-steps.update');
    Route::delete('/newsletter-steps/{id}', [\App\Http\Controllers\NewsletterStepController::class, 'destroy'])->name('newsletter-steps.destroy');

    // marketing steps
    Route::get('/marketing-steps', [\App\Http\Controllers\MarketingStepController::class, 'index'])->name('marketing-steps.index');
    Route::post('/marketing-steps', [\App\Http\Controllers\MarketingStepController::class, 'store'])->name('marketing-steps.store');
    Route::get('/marketing-steps/toggle/{id}', [\App\Http\Controllers\MarketingStepController::class, 'toggle'])->name('marketing-steps.toggle');
    Route::put('/marketing-steps/{id}', [\App\Http\Controllers\MarketingStepController::class, 'update'])->name('marketing-steps.update');
    Route::delete('/marketing-steps/{id}', [\App\Http\Controllers\MarketingStepController::class, 'destroy'])->name('marketing-steps.destroy');

    // Sequence routes
    Route::get('/getsequence/{id}', [\App\Http\Controllers\API\APISequenceController::class, 'getSequence']);
    Route::put('/updatesequence/{id}', [\App\Http\Controllers\API\APISequenceController::class, 'updateSequence']);
    Route::delete('/sequence/{id}', [\App\Http\Controllers\API\APISequenceController::class, 'destroy']);
    Route::post('/sequence/bulk-delete', [\App\Http\Controllers\API\APISequenceController::class, 'bulkDelete']);

    // API routes for dashboard
    Route::get('/marketing-steps/data', [\App\Http\Controllers\DataController::class, 'marketingSteps']);
    Route::get('/sequences/data', [\App\Http\Controllers\DataController::class, 'sequences']);
    Route::get('/newsletter-sequences/data', [\App\Http\Controllers\DataController::class, 'newsletterSequences']);

    // Test email routes
    Route::post('/send-test-email', [\App\Http\Controllers\API\APISequenceController::class, 'sendTestEmail']);
    Route::post('/send-simple-test-email', [\App\Http\Controllers\API\APISequenceController::class, 'sendSimpleTestEmail']);
    Route::post('/send-test-all-emails', [\App\Http\Controllers\API\APISequenceController::class, 'sendTestAllEmails']);
    Route::post('/send-all-marketing-emails', [\App\Http\Controllers\API\APISequenceController::class, 'sendAllMarketingEmails']);
    Route::post('/send-all-newsletter-emails', [\App\Http\Controllers\API\APISequenceController::class, 'sendAllNewsletterEmails']);
    Route::post('/send-all-question-emails', [\App\Http\Controllers\API\APISequenceController::class, 'sendAllQuestionEmails']);

    // Email logs
    // Forms routes (moved from API to avoid auth)
    Route::get('/forms/data', [\App\Http\Controllers\API\APIFormController::class, 'formdata']);
    Route::get('/forms/count', [\App\Http\Controllers\API\APIFormController::class, 'formCount']);
    Route::get('/forms/user-summary', [\App\Http\Controllers\API\APIFormController::class, 'userFormsSummary']);

    // Queue health route (moved from API to avoid auth)
    Route::get('/health/queue', [\App\Http\Controllers\API\APISequenceController::class, 'queueHealth']);
    Route::get('/dashboard/summary', [\App\Http\Controllers\API\APISequenceController::class, 'dashboardSummary']);

    // Question steps data route
    Route::get('/question-steps/data', [\App\Http\Controllers\QuestionStepController::class, 'data']);

    // Preview routes
    Route::get('/preview/marketing/{step}', [\App\Http\Controllers\EmailPreviewController::class, 'marketing']);
    Route::get('/preview/newsletter/{step}', [\App\Http\Controllers\EmailPreviewController::class, 'newsletter']);
    Route::get('/preview/question/{step}', [\App\Http\Controllers\EmailPreviewController::class, 'question']);

    Route::get('/email-logs', [\App\Http\Controllers\EmailLogController::class, 'index']);

    // Image upload routes
    Route::post('/upload-image', [\App\Http\Controllers\ImageController::class, 'upload']);
    Route::get('/images', [\App\Http\Controllers\ImageController::class, 'list']);

    // Newsletter sequences
    Route::get('/newsletter-sequence/{id}', [\App\Http\Controllers\NewsletterSequenceController::class, 'show']);
    Route::post('/newsletter-sequences', [\App\Http\Controllers\NewsletterSequenceController::class, 'store']);
    Route::delete('/newsletter-sequence/{id}', [\App\Http\Controllers\NewsletterSequenceController::class, 'destroy']);

    // Image API routes
    Route::get('/api/images', [\App\Http\Controllers\ImageApiController::class, 'publicImages'])->name('api.images');
    Route::get('/storage-images', [\App\Http\Controllers\ImageApiController::class, 'storageImages']);

    // newsletter editor
    Route::get('/newsletter-editor/create', [\App\Http\Controllers\NewsletterEditorController::class, 'create'])->name('newsletter-editor.create');
    Route::get('/newsletter-editor/{id}/edit', [\App\Http\Controllers\NewsletterEditorController::class, 'edit'])->name('newsletter-editor.edit');
    Route::post('/newsletter-editor', [\App\Http\Controllers\NewsletterEditorController::class, 'store'])->name('newsletter-editor.store');
    Route::put('/newsletter-editor/{id}', [\App\Http\Controllers\NewsletterEditorController::class, 'update'])->name('newsletter-editor.update');
    Route::get('/newsletter-editor/toggle/{id}', [\App\Http\Controllers\NewsletterEditorController::class, 'toggle'])->name('newsletter-editor.toggle');
    Route::delete('/newsletter-editor/{id}', [\App\Http\Controllers\NewsletterEditorController::class, 'destroy'])->name('newsletter-editor.destroy');

    // marketing editor
    Route::get('/marketing-editor/create', [\App\Http\Controllers\MarketingEditorController::class, 'create'])->name('marketing-editor.create');
    Route::get('/marketing-editor/toggle/{id}', [\App\Http\Controllers\MarketingEditorController::class, 'toggle'])->name('marketing-editor.toggle');
    Route::get('/marketing-editor/{id}/edit', [\App\Http\Controllers\MarketingEditorController::class, 'edit'])->name('marketing-editor.edit');
    Route::post('/marketing-editor', [\App\Http\Controllers\MarketingEditorController::class, 'store'])->name('marketing-editor.store');
    Route::put('/marketing-editor/{id}', [\App\Http\Controllers\MarketingEditorController::class, 'update'])->name('marketing-editor.update');
    Route::delete('/marketing-editor/{id}', [\App\Http\Controllers\MarketingEditorController::class, 'destroy'])->name('marketing-editor.destroy');

    // question sequences
    Route::get('/question-sequences', [\App\Http\Controllers\QuestionSequenceController::class, 'index'])->name('question-sequences.index');
    Route::post('/question-sequences', [\App\Http\Controllers\QuestionSequenceController::class, 'store'])->name('question-sequences.store');
    Route::post('/question-sequences/send', [\App\Http\Controllers\QuestionSequenceController::class, 'send']);
    Route::get('/question-sequence/{id}', [\App\Http\Controllers\QuestionSequenceController::class, 'show']);
    Route::put('/question-sequence/{id}', [\App\Http\Controllers\QuestionSequenceController::class, 'update']);
    Route::delete('/question-sequence/{id}', [\App\Http\Controllers\QuestionSequenceController::class, 'destroy']);
    Route::post('/question-sequence/bulk-delete', [\App\Http\Controllers\QuestionSequenceController::class, 'bulkDelete']);
    Route::get('/question-sequences/data', [\App\Http\Controllers\QuestionSequenceController::class, 'data']);

    // question steps
    Route::get('/question-steps', [\App\Http\Controllers\QuestionStepController::class, 'index'])->name('question-steps.index');
    Route::post('/question-steps', [\App\Http\Controllers\QuestionStepController::class, 'store'])->name('question-steps.store');
    Route::put('/question-steps/{id}', [\App\Http\Controllers\QuestionStepController::class, 'update']);
    Route::delete('/question-steps/{id}', [\App\Http\Controllers\QuestionStepController::class, 'destroy']);
    Route::get('/question-steps/toggle/{id}', [\App\Http\Controllers\QuestionStepController::class, 'toggle']);
    Route::get('/question-steps/data', [\App\Http\Controllers\QuestionStepController::class, 'data']);

    // question editor
    Route::get('/question-editor/create', [\App\Http\Controllers\QuestionEditorController::class, 'create'])->name('question-editor.create');
    Route::get('/question-editor/{id}/edit', [\App\Http\Controllers\QuestionEditorController::class, 'edit'])->name('question-editor.edit');
    Route::post('/question-editor', [\App\Http\Controllers\QuestionEditorController::class, 'store'])->name('question-editor.store');
    Route::put('/question-editor/{id}', [\App\Http\Controllers\QuestionEditorController::class, 'update'])->name('question-editor.update');
    Route::get('/question-editor/toggle/{id}', [\App\Http\Controllers\QuestionEditorController::class, 'toggle'])->name('question-editor.toggle');
    Route::delete('/question-editor/{id}', [\App\Http\Controllers\QuestionEditorController::class, 'destroy'])->name('question-editor.destroy');

});

// WordPress route
Route::get('/wordpress', function () {
    return view('wordpress');
});

require __DIR__.'/auth.php';
