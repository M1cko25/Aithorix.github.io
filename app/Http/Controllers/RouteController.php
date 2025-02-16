<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class RouteController extends Controller
{
    public function login() {
        return Inertia::render('Auth/Login');
    }
    public function register() {
        return Inertia::render('Auth/Register');
    }
    public function verification() {
        return Inertia::render('Auth/Verification');
    }
    public function setup() {
        return Inertia::render('Auth/Setup');
    }
}
