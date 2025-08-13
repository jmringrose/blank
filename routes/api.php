<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

use App\Http\Controllers\API\APISequenceController;
use App\Http\Controllers\API\APIFormController;
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
Route::post('/login', function (Request $request) {
    $data = $request->validate([
        'email'       => ['required', 'email'],
        'password'    => ['required'],
        'device_name' => ['nullable', 'string'],
    ]);

    $user = User::where('email', $data['email'])->first();

    if (! $user || ! Hash::check($data['password'], $user->password)) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    // Create API token
    $token = $user->createToken($data['device_name'] ?? 'api')->plainTextToken;

    // Also log in via session for web routes
    auth()->login($user);
    $request->session()->regenerate();

    return response()->json([
        'token' => $token,
        'user' => $user
    ], 200);
})->middleware('web');

/*
|--------------------------------------------------------------------------
| Protected (Sanctum)
|--------------------------------------------------------------------------
*/
Route::post('/logout', function (Request $request) {
    $request->user()->currentAccessToken()->delete();
    return response()->json(['message' => 'Logged out']);
})->middleware('auth:sanctum');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    // session-ish helpers
    Route::post('/logout', [ApiLoginController::class, 'logout']);
    Route::get('/me', fn () => auth()->user());

    // queue and sequence management
    Route::get('/getsequence/{id}', [APISequenceController::class, 'getSequence']);
    Route::put('/updatesequence/{id}', [APISequenceController::class, 'updateSequence']);

    Route::get('/email-sequence/summary', [APISequenceController::class, 'dashboardSummary']);
    Route::get('/status/queue', [APISequenceController::class, 'queueStatus']);
    Route::get('/health/queue', [APISequenceController::class, 'queueHealth']);

    Route::post('/sequence/reset', [APISequenceController::class, 'resetSequence']);
    Route::delete('/sequence/{id}', [APISequenceController::class, 'destroy']);
    Route::delete('/sequence/bulk', [APISequenceController::class, 'bulkDestroy']);
    Route::post('/sequence/bulk-delete', [APISequenceController::class, 'bulkDelete']);

    // data tables
    Route::get('/sequences/data', [APISequenceController::class, 'sequencedata']);
    Route::get('/forms/data', [APIFormController::class, 'formdata']);

    Route::post('/theme', [ProfileController::class, 'theme']);
});
