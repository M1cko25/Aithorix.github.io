<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmailController;
use App\Http\Middleware\AuthMiddleware;

Route::controller(AuthController::class)->group(function () {
    Route::post('/login', 'login');
    Route::post('/logout', 'logout')->name('logout')->middleware('auth');
    Route::post('/reset-password', 'resetPass')->middleware('guest');
    Route::post('/create-account', 'register')->name('create-account')->middleware('guest');
});
Route::middleware('guest')->group(function(){
    Route::inertia('/login', 'Auth/Login')->name('login');
    Route::inertia('/register', 'Auth/Register')->name('register');
    Route::inertia('/forgot-password', 'Auth/ForgotPassword')->name('forgot-password');
});
Route::middleware(AuthMiddleware::class)->group(function() {
    Route::inertia('/verification', 'Auth/Verification')->name('verification');
    Route::inertia('/account-setup', 'Auth/Setup')->name('setup');
});
Route::controller(EmailController::class)->group(function(){
    Route::post('/verify-code', 'verifyCode')->name('verify-code')->middleware('guest');
    Route::post('/resend-code', 'resendCode')->name('resend-code');
    Route::post('/register', 'sendEmail');
    Route::post('forgot-password', 'forgotPassword')->name('forgot-password');
    Route::get('/reset-password/{token}', 'resetPassword')->name('password.reset');
});


