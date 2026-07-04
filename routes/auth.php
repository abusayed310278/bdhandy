<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\ProviderOnboardingController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

// Guest-only routes
Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');

    // Social auth
    Route::get('auth/{provider}', [SocialAuthController::class, 'redirect'])->name('auth.social');
    Route::get('auth/{provider}/callback', [SocialAuthController::class, 'callback']);

    // Social role selection (after OAuth — session-gated inside controller)
    Route::get('register/social/role', [SocialAuthController::class, 'roleSelectForm'])->name('social.role-select');
    Route::post('register/social/role', [SocialAuthController::class, 'roleSelectStore'])->name('social.role-select.store');
});

// Auth-only routes
Route::middleware('auth')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)->name('verification.notice');
    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])->name('password.confirm');
    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // Provider onboarding — verified users only
    Route::prefix('provider/onboarding')->name('provider.onboarding.')->middleware('verified')->group(function () {
        Route::get('profile',      [ProviderOnboardingController::class, 'profile'])->name('profile');
        Route::post('profile',     [ProviderOnboardingController::class, 'profileStore'])->name('profile.store');
        Route::get('services',     [ProviderOnboardingController::class, 'services'])->name('services');
        Route::post('services',    [ProviderOnboardingController::class, 'servicesStore'])->name('services.store');
        Route::get('service-area', [ProviderOnboardingController::class, 'serviceArea'])->name('service-area');
        Route::post('service-area',[ProviderOnboardingController::class, 'serviceAreaStore'])->name('service-area.store');
        Route::get('documents',    [ProviderOnboardingController::class, 'documents'])->name('documents');
        Route::post('documents',   [ProviderOnboardingController::class, 'documentsStore'])->name('documents.store');
        Route::get('pending',      [ProviderOnboardingController::class, 'pending'])->name('pending');
    });
});
