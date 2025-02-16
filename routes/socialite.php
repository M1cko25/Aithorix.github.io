<?php 

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SocialiteController;

Route::controller(SocialiteController::class)->group(function () {
    Route::get('/googleLogin', 'googleLogin')->name('googleLogin');
    Route::get('/googleAuth', 'googleAuth')->name('googleauth');
    Route::get('/slackLogin', 'slacksLogin')->name('slackLogin');
    Route::get('/slackAuth', 'slacksAuth')->name('slackAuth');
});