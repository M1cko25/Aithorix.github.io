<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ScrumController;
use App\Http\Middleware\ProjectMiddleware;

Route::controller(ScrumController::class)->group(function(){
    Route::middleware(['auth', ProjectMiddleware::class])->group(function(){
        Route::get('/scrum/dashboard', 'getDashboardDatas')->name('scrum-dashboard');
        Route::get('/scrum/board', 'getBoardDatas')->name('scrum-board');
        Route::get('/scrum/timeline', 'getTimelineDatas')->name('scrum-timeline');
        Route::get('/scrum/backlog', 'getBacklogDatas')->name('scrum-backlog');
        Route::post('/scrum/epics-reorder', 'updateEpicOrder')->name('epics-reorder');
        Route::post('/scrum/backlog-delete', 'deleteBacklog')->name('backlog-delete');
        Route::post('/scrum/backlog-create', 'createBacklog')->name('backlog-create');
        Route::post('/scrum/epic-create', 'createEpic')->name('epic-create');
        Route::post('/scrum/epic-update', 'updateEpic')->name('epic-update');
        Route::post('/scrum/epic-delete', 'deleteEpic')->name('epic-delete');
        Route::post('/scrum/backlog-status-update', 'updateBacklogStatus')->name('backlog-status-update');
        Route::post('/scrum/backlog-update', 'updateBacklog')->name('backlog-update');
        Route::post('/scrum/epic-status-update', 'updateEpicStatus')->name('epic-status-update');
        Route::post('/scrum/start-sprint', 'startSprint')->name('start-sprint');
        Route::post('/scrum/complete-sprint', 'completeSprint')->name('complete-sprint');
        Route::post('/scrum/upload-attachment', 'uploadAttachment')->name('upload-attachment');
        Route::post('/scrum/add-comment', 'addComment')->name('add-comment');
        Route::get('/scrum/project-members/{projectId}', 'getProjectMembers')->name('get-project-members');
        Route::post('/scrum/update-assignees', 'updateTaskAssignees')->name('update-assignees');
        Route::post('/scrum/add-column', 'addColumn')->name('add-column');
        Route::post('/scrum/delete-column', 'deleteColumn')->name('delete-column');
        Route::post('/scrum/move-tasks', 'moveTasks')->name('move-tasks');
    });
});
// Route::inertia('/scrum/meeting', 'Scrum/Meeting')->name('scrum-meeting')->middleware('auth');