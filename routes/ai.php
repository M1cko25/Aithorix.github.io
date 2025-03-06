<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AIController;

Route::post('/ai/process', [AIController::class, 'processPrompt']);
Route::post('/ai/getProjectData', [AIController::class, 'getProjectData']);
