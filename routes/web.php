<?php

use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Landing');
Route::inertia('/signup', 'SignUp')->name('signup');
Route::inertia('/signin', 'SignIn')->name('signin');
