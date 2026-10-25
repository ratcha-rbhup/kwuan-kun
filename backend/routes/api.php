<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GroupMediaController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MasterDataController;

/*
|--------------------------------------------------------------------------
| KwuanKun API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// 1. Profile Endpoints
Route::prefix('profile')->group(function () {
    Route::get('/', [ProfileController::class, 'getProfile']);
    Route::post('/update', [ProfileController::class, 'updateProfile']);
    Route::post('/avatar', [ProfileController::class, 'uploadAvatar']);
    Route::post('/reset-avatar', [ProfileController::class, 'resetAvatar']);
});

// 2. Groups (Public & Member)
Route::prefix('groups')->group(function () {
    Route::get('/', [GroupController::class, 'index']);
    Route::get('/{slug}', [GroupController::class, 'show']);
    Route::post('/upload-image', [GroupMediaController::class, 'uploadImage']);
});
Route::post('/group-creation-requests', [GroupController::class, 'createGroupRequest']);

// 3. Activities / Sessions (Live Badminton Matching)
Route::prefix('activities')->group(function () {
    Route::get('/', [ActivityController::class, 'index']);
    Route::get('/{id}', [ActivityController::class, 'show']);
    Route::post('/', [ActivityController::class, 'store']);
    Route::put('/{id}', [ActivityController::class, 'update']);
    Route::delete('/{id}', [ActivityController::class, 'destroy']);
    Route::post('/{id}/join', [ActivityController::class, 'join']);
    Route::post('/{id}/leave', [ActivityController::class, 'leave']);
});

// 4. Kwuan Owner Portal Endpoints
Route::prefix('owner/groups/{id}')->group(function () {
    Route::get('/dashboard', [OwnerController::class, 'dashboard']);
    Route::get('/sessions', [OwnerController::class, 'sessions']);
    Route::get('/members', [OwnerController::class, 'members']);
    Route::post('/members/{memberId}/skill', [OwnerController::class, 'updateMemberSkill']);
    Route::delete('/members/{memberId}', [OwnerController::class, 'removeMember']);
    Route::post('/requests/{requestId}/approve', [OwnerController::class, 'approveMemberRequest']);
    Route::post('/requests/{requestId}/reject', [OwnerController::class, 'rejectMemberRequest']);
    Route::get('/expenses', [OwnerController::class, 'expenses']);
    Route::post('/expenses/toggle-paid', [OwnerController::class, 'togglePaid']);
    Route::post('/settings', [OwnerController::class, 'updateSettings']);
});

// 5. Platform Admin Portal Endpoints
Route::prefix('admin')->group(function () {
    Route::get('/group-creation-requests', [AdminController::class, 'groupRequests']);
    Route::post('/group-creation-requests/{id}/approve', [AdminController::class, 'approveGroupRequest']);
    Route::post('/group-creation-requests/{id}/reject', [AdminController::class, 'rejectGroupRequest']);
    Route::get('/groups', [AdminController::class, 'groups']);
    Route::post('/groups/{id}/toggle-status', [AdminController::class, 'toggleGroupStatus']);
    Route::get('/reports', [AdminController::class, 'reports']);
    Route::get('/settings', [AdminController::class, 'settings']);
    Route::post('/settings', [AdminController::class, 'updateSettings']);
});

// 6. Member & Master Data Endpoints
Route::prefix('member')->group(function () {
    Route::get('/my-groups', [MasterDataController::class, 'myGroups']);
    Route::post('/join-code', [MasterDataController::class, 'joinWithCode']);
});

Route::prefix('master')->group(function () {
    Route::get('/sport-categories', [MasterDataController::class, 'sportCategories']);
    Route::get('/skill-levels', [MasterDataController::class, 'skillLevels']);
    Route::get('/provinces', [MasterDataController::class, 'provinces']);
    Route::get('/provinces/{provinceId}/districts', [MasterDataController::class, 'districts']);
    Route::get('/venues', [MasterDataController::class, 'venues']);
});

// Backward-compatible Root Master Endpoints
Route::get('/provinces', [MasterDataController::class, 'provinces']);
Route::get('/provinces/{provinceId}/districts', [MasterDataController::class, 'districts']);
Route::get('/badminton-skill-levels', [MasterDataController::class, 'skillLevels']);
Route::get('/sport-categories', [MasterDataController::class, 'sportCategories']);
