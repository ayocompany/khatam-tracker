<?php

use App\Http\Controllers\Auth\OtpAuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProgressUpdateController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('welcome');

Route::get('/auth', [OtpAuthController::class, 'create'])->name('auth');
Route::get('/signin', [OtpAuthController::class, 'create'])->name('signin');
Route::post('/auth/otp/send', [OtpAuthController::class, 'sendOtp'])->name('auth.otp.send');
Route::post('/auth/otp/verify', [OtpAuthController::class, 'verifyOtp'])->name('auth.otp.verify');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/layout', [DashboardController::class, 'updateLayout'])->name('dashboard.updateLayout');

    Route::get('/onboarding', [App\Http\Controllers\OnboardingController::class, 'create'])->name('onboarding');
    Route::post('/onboarding', [App\Http\Controllers\OnboardingController::class, 'store'])->name('onboarding.store');

    Route::get('/progress/update', [ProgressUpdateController::class, 'create'])->name('progress.update');
    Route::post('/progress/update', [ProgressUpdateController::class, 'store'])->name('progress.update.store');

    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::post('/settings/layout', [SettingsController::class, 'updateLayout'])->name('settings.updateLayout');
    Route::post('/settings/reminders', [SettingsController::class, 'updateReminders'])->name('settings.updateReminders');
    Route::post('/settings/reset', [SettingsController::class, 'resetAll'])->name('settings.resetAll');
});
