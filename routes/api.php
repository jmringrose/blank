<?php


use App\Http\Controllers\API\SequenceController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmailSequenceController;


//Route::middleware([])->group(function () {

    Route::get('/email-sequence/summary', [\App\Http\Controllers\API\SequenceController::class, 'dashboardSummary']);
    Route::get('/status/queue', [\App\Http\Controllers\API\SequenceController::class, 'queueStatus']);

    Route::get('/unsubscribe/{token}', [SequenceController::class, 'unsubscribe'])->name('unsubscribe');
    Route::get('/email-sequence/{id}', [EmailSequenceController::class, 'show']);
    Route::put('/email-sequence/{id}', [EmailSequenceController::class, 'update']);

    Route::post('/sequence/add', [SequenceController::class, 'addToSequence']);
    Route::post('/sequence/reset', [SequenceController::class, 'resetSequence']);
    Route::delete('/sequence/{id}', [SequenceController::class, 'destroy']);
    Route::delete('/sequence/bulk', [SequenceController::class, 'bulkDestroy']);
    Route::post('/sequence/bulk-delete', [SequenceController::class, 'bulkDelete']);

//});
