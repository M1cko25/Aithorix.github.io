<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MeetingConferenceController;

Route::controller(MeetingConferenceController::class)->group(function () {
    Route::middleware('auth')->group(function () {
        Route::get('/meeting', 'index')->name('meeting.home');
        Route::post('/daily/create-room', 'createRoom');
        Route::post('/daily/join-room', 'joinRoom')->name('meeting-room-post');
        Route::get('/meeting/{name}', 'joinRoom')->name('meeting-room');
    });
});