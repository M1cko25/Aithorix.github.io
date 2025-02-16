<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function getUserDatas() {
        return Inertia::render('Home');
    }
}
