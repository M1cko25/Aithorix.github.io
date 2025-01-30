<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Inertia\Inertia;
use App\Http\Controllers\SocialiteController;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\EmailController;
use App\Http\Middleware\AuthMiddleware;
use Illuminate\Validation\Rules\Email;
use App\Http\Controllers\TemplatesController;
use App\Http\Controllers\UserController;

//routes
Route::inertia('/', 'Landing')->name('landing');
Route::inertia('/register', 'Register')->name('register')->middleware('guest');
Route::inertia('/login', 'Login')->name('login')->middleware('guest');
Route::inertia('/verification', 'Verification')->name('verification')->middleware(AuthMiddleware::class);
Route::inertia('/account-setup', 'Setup')->name('setup')->middleware(AuthMiddleware::class);
Route::inertia('/template-selection', 'Template')->name('template')->middleware('auth');
Route::inertia('/forgot-password', 'ForgotPassword')->name('forgot-password')->middleware('guest');

//scrum routes
Route::inertia('/scrum/board', 'Scrum/ScrumBoard')->name('scrum-board')->middleware('auth');

//project creation
Route::inertia('/scrum/create-project', 'ProjectCreation')->name('scrum-create-project')->middleware('auth');
Route::get('/create-project', [TemplatesController::class, 'templateSelected'])->name('template-selected')->middleware('auth');
Route::post('/create-project', [TemplatesController::class, 'createProject'])->name('create-project')->middleware('auth');
Route::post('/project-members', [TemplatesController::class, 'addProjectMembers'])->name('add-members')->middleware('auth');

//Auth posts
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [EmailController::class, 'sendEmail']);
Route::post('/create-account', [AuthController::class, 'register'])->name('create-account');
Route::post('forgot-password', [EmailController::class, 'forgotPassword'])->name('forgot-password');
Route::post('/reset-password', [AuthController::class, 'resetPass'])->middleware('guest')->name('password.update');

//Auth gets
Route::get('/setup', [AuthController::class, 'verify'])->name('verify');
Route::get('/reset-password/{token}', [EmailController::class, 'resetPassword'])->name('password.reset');

//Socialite routes
Route::controller(SocialiteController::class)->group(function () {
    Route::get('/googleLogin', 'googleLogin')->name('googleLogin');
    Route::get('/googleAuth', 'googleAuth')->name('googleauth');
    Route::get('/slackLogin', 'slacksLogin')->name('slackLogin');
    Route::get('/slackAuth', 'slacksAuth')->name('slackAuth');
});

Route::get('/search-users', [UserController::class, 'searchUsers'])->name('search-users')->middleware('auth');

//logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');