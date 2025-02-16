<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TemplatesController;
use App\Http\Controllers\UserController;

Route::controller(TemplatesController::class)->group(function (){
    Route::middleware('auth')->group(function() {
        Route::get('/create-project', 'templateSelected')->name('template-selected');
        Route::post('/create-project', 'createProject')->name('create-project');
        Route::post('/project-members', 'addProjectMembers')->name('add-members');
    });
});
Route::middleware('auth')->group(function(){
    Route::inertia('/select-template', 'Template')->name('template');
    Route::inertia('/scrum/create-project', 'ProjectCreation')->name('scrum-create-project');
    Route::get('/search-users', [UserController::class, 'searchUsers'])->name('search-users');
});
