<?php


use App\Http\Controllers\API\SequenceController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmailSequenceController;

Route::get('/email-sequences/{id}', [EmailSequenceController::class, 'show']);
Route::put('/email-sequences/{id}', [EmailSequenceController::class, 'update']);

Route::middleware([])->group(function () {
    Route::post('/sequence/add', [SequenceController::class, 'addToSequence']);
    Route::post('/sequence/reset', [SequenceController::class, 'resetSequence']);
    Route::delete('/sequences/{id}', [SequenceController::class, 'destroy']);
    Route::delete('/sequences/bulk', [SequenceController::class, 'bulkDestroy']);
    Route::post('/sequences/bulk-delete', [SequenceController::class, 'bulkDelete']);
});
