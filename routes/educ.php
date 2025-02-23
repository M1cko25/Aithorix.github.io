<?php

use Illuminate\Support\Facades\Route;

Route::get('/educ/course-management', function () {
    return inertia('Educ/CourseManage');
})->name('course-management');
