<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MeetingConferenceController;
use App\Http\Controllers\NotificationController;

//routes
Route::inertia('/', 'Landing')->name('landing');
Route::get('/home', [HomeController::class, 'getUserDatas'])->name('home')->middleware('auth');
Route::inertia('/meeting/home', 'Meeting/MeetingHome')->name('meeting-home')->middleware('auth');
Route::inertia('/meetings', 'Meeting/MeetingSummaries')->name('meetings')->middleware('auth');

// Notification Routes
Route::middleware('auth')->group(function () {
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/{id}/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
        Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    });
});
