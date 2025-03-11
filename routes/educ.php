<?php

use Illuminate\Support\Facades\Route;

Route::get('/educ/course-management', function () {
    return inertia('Educ/CourseManage');
})->name('course-management');

Route::get('/educ/assignment-tracker', function () {
    return inertia('Educ/AssTracker');
})->name('assignment-tracker');

Route::get('/educ/calendar-view', function () {
    return inertia('Educ/CalView');
})->name('calendar-view');

Route::get('/educ/resources', function () {
    return inertia('Educ/Resources');
})->name('resources');
