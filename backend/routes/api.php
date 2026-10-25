<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GroupMediaController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Profile endpoints (avatar upload, profile update, reset to LINE)
Route::prefix('profile')->group(function () {
    Route::get('/', [ProfileController::class, 'getProfile']);
    Route::post('/update', [ProfileController::class, 'updateProfile']);
    Route::post('/avatar', [ProfileController::class, 'uploadAvatar']);
    Route::post('/reset-avatar', [ProfileController::class, 'resetAvatar']);
});

// Group media upload endpoints (logo & cover)
Route::prefix('groups')->group(function () {
    Route::post('/upload-image', [GroupMediaController::class, 'uploadImage']);
});

