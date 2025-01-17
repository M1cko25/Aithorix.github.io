<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Inertia\Inertia;
use App\Http\Controllers\SocialiteController;
use Laravel\Socialite\Facades\Socialite;

//routes
Route::inertia('/', 'Landing')->name('landing');
Route::inertia('/register', 'Register')->name('register');
Route::inertia('/login', 'Login')->name('login');
Route::inertia('/verification', 'Verification')->name('verification');
Route::inertia('/account-setup', 'Setup')->name('setup');
Route::inertia('/template-selection', 'Template')->name('template');

//posts
Route::post('signin', [AuthController::class, 'signin'])->name(('signin'));

//Socialite routes
Route::controller(SocialiteController::class)->group(function () {
    Route::get('/googleLogin', 'googleLogin')->name('googleLogin');
    Route::get('/googleAuth', 'googleAuth')->name('googleauth');
});