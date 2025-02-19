<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ScrumController;

Route::controller(ScrumController::class)->group(function () {
    Route::middleware('auth')->group(function () {
        Route::get('/scrum/dashboard', 'getDashboardDatas')->name('scrum-dashboard');
        Route::get('/scrum/board', 'getBoardDatas')->name('scrum-board');
        Route::get('/scrum/timeline', 'getTimelineDatas')->name('scrum-timeline');
        Route::get('/scrum/backlog', 'getBacklogDatas')->name('scrum-backlog');
    });
});

Route::get('/scrum/upgrade-plan', function () {
    return inertia('Scrum/UpgradePlan', [
        'title' => 'Upgrade Plan',
    ]);
})->name('upgrade-plan');
