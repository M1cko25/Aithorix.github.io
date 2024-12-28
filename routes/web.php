<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

//routes
Route::inertia('/', 'Landing');
Route::inertia('/signup', 'SignUp')->name('signup');
Route::inertia('/signin', 'SignIn')->name('signin');


//posts
Route::post('signin', [AuthController::class, 'signin'])->name(('signin'););