<?php

use Illuminate\Support\Facades\Route;

Route::get('/educ/assignment-tracker', function () {
    return inertia('Educ/AssTracker');
})->name('assignment-tracker');
