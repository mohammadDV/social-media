<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register'])
                ->middleware('guest')
                ->name('register');

Route::post('/login', [AuthController::class, 'login'])
                ->middleware('guest')
                ->name('login');

Route::post('/google/verify', [AuthController::class, 'verify'])
                ->middleware('guest')
                ->name('verify');

Route::middleware(['auth:sanctum'])->post('/logout', [AuthController::class, 'logout'])
                ->middleware('auth')
                ->name('logout');
// Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
//                 ->middleware('guest')
//                 ->name('password.email');

// Route::post('/reset-password', [NewPasswordController::class, 'store'])
//                 ->middleware('guest')
//                 ->name('password.store');

// Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)
//                 ->middleware(['auth', 'signed', 'throttle:6,1'])
//                 ->name('verification.verify');

// Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
//                 ->middleware(['auth', 'throttle:6,1'])
//                 ->name('verification.send');