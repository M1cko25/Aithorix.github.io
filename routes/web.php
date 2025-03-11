<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MeetingConferenceController;

//routes
Route::inertia('/', 'Landing')->name('landing');
Route::get('/home', [HomeController::class, 'getUserDatas'])->name('home')->middleware('auth');
Route::inertia('/meeting/home', 'Meeting/Home')->name('meeting-home')->middleware('auth');
Route::inertia('/meeting/conference', 'Meeting/Conference')->name('meeting-conference')->middleware('auth');

Route::inertia('/eventplanning/eventdashboard', 'EventPlanning/EventDashboard')->name('eventplanning-eventdashboard')->middleware('auth');
Route::inertia('/eventplanning/schedule', 'EventPlanning/Schedule')->name('eventplanning-Schedule')->middleware('auth');
Route::inertia('/eventplanning/vendor', 'EventPlanning/Vendor')->name('eventplanning-vendor')->middleware('auth');
Route::inertia('/eventplanning/budgetoverview', 'EventPlanning/BudgetOverview' )->name('eventplanning-budgetoverview')->middleware('auth');
