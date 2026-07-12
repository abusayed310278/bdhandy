<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\Auth\RegisteredUserController;
use App\Http\Controllers\Api\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Api\Auth\PasswordResetController;
use App\Http\Controllers\Api\Auth\PasswordController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\SupportController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\SavedProviderController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\RequirementController;

// CMS Pages
Route::get('/about-us', [PageController::class, 'aboutUs']);
Route::get('/safety-center', [PageController::class, 'safetyCenter']);
Route::get('/how-it-works', [PageController::class, 'howItWorks']);
Route::get('/privacy-policy', [PageController::class, 'privacyPolicy']);
Route::get('/terms-conditions', [PageController::class, 'termsConditions']);

// Contact Form
Route::post('/contact', [ContactController::class, 'store']);

Route::post('/register', [RegisteredUserController::class, 'store']);
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail']);
Route::post('/reset-password', [PasswordResetController::class, 'reset']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy']);
    Route::post('/change-password', [PasswordController::class, 'update']);
    Route::post('/profile/update', [ProfileController::class, 'update']);
    
    // Support Tickets
    Route::get('/tickets', [SupportController::class, 'index']);
    Route::post('/tickets', [SupportController::class, 'store']);

    // User Data
    Route::get('/saved-providers', [SavedProviderController::class, 'index']);
    Route::post('/saved-providers/toggle', [SavedProviderController::class, 'toggle']);
    
    Route::get('/addresses', [AddressController::class, 'index']);
    Route::post('/addresses', [AddressController::class, 'store']);
    Route::delete('/addresses/{id}', [AddressController::class, 'destroy']);
    
    Route::get('/requirements', [RequirementController::class, 'index']);
    Route::post('/requirements', [RequirementController::class, 'store']);
});
