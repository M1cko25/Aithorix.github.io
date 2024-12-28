<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function signin(Request $request) {
        //validation
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6|max:255'
        ]);
    }
}
