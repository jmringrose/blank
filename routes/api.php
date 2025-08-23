<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

use App\Http\Controllers\API\APISequenceController;
use App\Http\Controllers\API\APIFormController;
use App\Http\Controllers\API\APINewsletterSequenceController;
use App\Http\Controllers\API\NewsletterStepController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\Auth\ApiLoginController;
use App\Http\Controllers\ProfileController;

use Laravel\Sanctum\PersonalAccessToken;

/*
|--------------------------------------------------------------------------
| Debug (local only)
|--------------------------------------------------------------------------
*/
if (app()->environment('local')) {
    Route::get('/_debug/headers', fn () => request()->headers->all());

    Route::get('/_debug/sanctum', function () {
        $raw = request()->bearerToken();
        $pat = $raw ? PersonalAccessToken::findToken($raw) : null;

        return [
            'bearer'       => $raw,
            'token_found'  => (bool) $pat,
            'user'         => $pat ? $pat->tokenable()->first() : null,
        ];
    });
}

/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/
Route::post('/login', [AuthController::class, 'login'])->middleware('web');
Route::post('/sequence/add', [APISequenceController::class, 'saveSequence']);


https://cr.jringrose.com/api/sequence/add
/*
|--------------------------------------------------------------------------
| Protected (Sanctum)
|--------------------------------------------------------------------------
*/
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/user', [AuthController::class, 'user'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    // session-ish helpers
    Route::post('/logout', [ApiLoginController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // queue and sequence management
    Route::get('/getsequence/{id}', [APISequenceController::class, 'getSequence']);
    Route::put('/updatesequence/{id}', [APISequenceController::class, 'updateSequence']);

    Route::get('/email-sequence/summary', [APISequenceController::class, 'dashboardSummary']);
    Route::get('/status/queue', [APISequenceController::class, 'queueStatus']);
    Route::get('/health/queue', [APISequenceController::class, 'queueHealth']);

    Route::post('/sequence/reset', [APISequenceController::class, 'resetSequence']);
    Route::delete('/sequence/{id}', [APISequenceController::class, 'destroy']);
    Route::delete('/sequence/bulk', [APISequenceController::class, 'bulkDelete']);
    Route::post('/sequence/bulk-delete', [APISequenceController::class, 'bulkDelete']);

    // data tables
    Route::get('/sequences/data', [APISequenceController::class, 'sequencedata']);
    Route::get('/forms/data', [APIFormController::class, 'formdata']);
    Route::get('/forms/count', [APIFormController::class, 'formCount']);
    Route::get('/forms/user-summary', [APIFormController::class, 'userFormsSummary']);
    Route::get('/newsletter-sequences/data', [APINewsletterSequenceController::class, 'sequencedata']);
    Route::get('/newsletter-sequences/summary', [APINewsletterSequenceController::class, 'summary']);

    // newsletter sequence management
    Route::get('/newsletter-sequence/{id}', [APINewsletterSequenceController::class, 'getSequence']);
    Route::put('/newsletter-sequence/{id}', [APINewsletterSequenceController::class, 'updateSequence']);
    Route::post('/newsletter-sequences', [APINewsletterSequenceController::class, 'store']);
    Route::delete('/newsletter-sequence/{id}', [APINewsletterSequenceController::class, 'destroy']);
    Route::post('/newsletter-sequence/bulk-delete', [APINewsletterSequenceController::class, 'bulkDelete']);

    // newsletter steps management
    Route::apiResource('api-newsletter-steps', NewsletterStepController::class);

    Route::post('/theme', [ProfileController::class, 'theme']);
    Route::post('/send-test-email', [APISequenceController::class, 'sendTestEmail']);
    Route::post('/send-simple-test-email', [APISequenceController::class, 'sendSimpleTestEmail']);

    // Image routes
    Route::get('/images', [\App\Http\Controllers\ImageController::class, 'list']);
    Route::post('/upload-image', [\App\Http\Controllers\ImageController::class, 'upload']);
});
