<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MeetingConferenceController;

//routes
Route::inertia('/', 'Landing')->name('landing');
Route::get('/home', [HomeController::class, 'getUserDatas'])->name('home')->middleware('auth');
Route::inertia('/meeting/home', 'Meeting/Home')->name('meeting-home')->middleware('auth');
Route::inertia('/meeting/conference', 'Meeting/Conference')->name('meeting-conference')->middleware('auth');