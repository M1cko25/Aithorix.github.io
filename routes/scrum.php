<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ScrumController;
use App\Http\Middleware\ProjectMiddleware;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\EpicController;
use App\Http\Controllers\SprintController;

Route::controller(ScrumController::class)->group(function(){
    Route::middleware(['auth', ProjectMiddleware::class])->group(function(){
        Route::get('/scrum/dashboard', 'getDashboardDatas')->name('scrum-dashboard');
        Route::get('/scrum/board', 'getBoardDatas')->name('scrum-board');
        Route::get('/scrum/timeline', 'getTimelineDatas')->name('scrum-timeline');
        Route::get('/scrum/backlog', 'getBacklogDatas')->name('scrum-backlog');
        Route::get('/scrum/project-members/{projectId}', 'getProjectMembers')->name('get-project-members');
        Route::post('/scrum/add-column', 'addColumn')->name('add-column');
        Route::post('/scrum/delete-column', 'deleteColumn')->name('delete-column');
    });
});

Route::controller(EpicController::class)->group(function(){
    Route::middleware(['auth', ProjectMiddleware::class])->group(function(){
        Route::post('/scrum/epic-create', 'createEpic')->name('epic-create');
        Route::post('/scrum/epic-update', 'updateEpic')->name('epic-update');
        Route::post('/scrum/epic-delete', 'deleteEpic')->name('epic-delete');
        Route::post('/scrum/epics-reorder', 'updateEpicOrder')->name('epics-reorder');
        Route::post('/scrum/epic-status-update', 'updateEpicStatus')->name('epic-status-update');
    });
});

Route::controller(SprintController::class)->group(function(){
    Route::middleware(['auth', ProjectMiddleware::class])->group(function(){
        Route::post('/scrum/sprint-task-update', 'updateSprintTask')->name('sprint-task-update');
        Route::post('/scrum/add-sprint-task', 'addSprintTask')->name('add-sprint-task');
        Route::post('/scrum/complete-sprint', 'completeSprint')->name('complete-sprint');
        Route::post('/scrum/start-sprint', 'startSprint')->name('start-sprint');
    });
});

Route::controller(TaskController::class)->group(function(){
    Route::middleware(['auth', ProjectMiddleware::class])->group(function(){
        Route::post('/scrum/move-tasks', 'moveTasks')->name('move-tasks');
        Route::post('/scrum/update-assignees', 'updateTaskAssignees')->name('update-assignees');
        Route::post('/scrum/backlog-create', 'createBacklog')->name('backlog-create');
        Route::post('/scrum/backlog-status-update', 'updateBacklogStatus')->name('backlog-status-update');
        Route::post('/scrum/backlog-update', 'updateBacklog')->name('backlog-update');
        Route::post('/scrum/upload-attachment', 'uploadAttachment')->name('upload-attachment');
        Route::post('/scrum/delete-attachment', 'deleteAttachment')->name('delete-attachment');
        Route::post('/scrum/backlog-delete', 'deleteBacklog')->name('backlog-delete');
        Route::post('/scrum/add-comment', 'addComment')->name('add-comment');
    });
});

Route::get('/scrum/upgrade-plan', function () {
    return inertia('Scrum/UpgradePlan', [
        'title' => 'Upgrade Plan',
    ]);
})->name('upgrade-plan');