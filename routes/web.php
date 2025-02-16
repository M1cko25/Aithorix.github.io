<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

//routes
Route::inertia('/', 'Landing')->name('landing');
Route::get('/home', [HomeController::class, 'getUserDatas'])->name('home');