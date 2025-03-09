<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MeetingController;

Route::controller(MeetingController::class)->group(function(){
    Route::post('/zego-token', 'generateToken')->name('meeting.generateToken');
});

Route::inertia('/video-meeting', 'Meeting');

