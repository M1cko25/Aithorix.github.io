<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function signin(Request $request) {
        //validation
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6|max:255'
        ]);

        //sign in
        if (Auth::attempt($request->only('email', 'password'))) {
            return redirect()->route('/');
        }

        return redirect()->back()->withErrors(['email' => 'Invalid credentials']);
    }
}
