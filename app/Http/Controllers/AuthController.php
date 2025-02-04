<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request) {
        //validation
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Check if user exists first
        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            return redirect()->back()->withErrors([
                'email' => 'Email not found'
            ])->onlyInput('email');
        }

        //log in
        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();
            return redirect()->route('scrum-board');
        }
        return redirect()->back()->withErrors(['password' => 'Incorrect password'])->onlyInput('password');
    }

    public function verify(Request $request) {

        if ($request->email == null) {
            return redirect()->back()->withErrors(['email' => 'Email is required']);
        }
        return Inertia::render('Setup', [
            'email' => $request->email,
        ]);
    }

    public function register(Request $request) {
        $request->validate([
            'name' => 'required|unique:users,name|max:255|regex:/^[a-zA-Z\s]+$/',
            'password' => 'required|min:8|max:255',
            'confirmPassword' => 'required|same:password',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'avatar' => $request->avatar,
            'google_id' => $request->google_id,
            'password' => bcrypt($request->password),
            'email_verified_at' => now()
        ]);

        Auth::login($user);
        return redirect()->route('template');
    }

    public function logout(Request $request) {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return Inertia::render('Login');
    }

    public function resetPass (Request $request) {
        $request->validate([
            'token' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);
     
        $status = Password::reset(
            $request->only('email', 'password', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));
     
                $user->save();
     
                event(new PasswordReset($user));
            }
        );
     
        return $status === Password::PASSWORD_RESET
                    ? redirect()->back()->with('status', __($status))
                    : back()->withErrors(['email' => [__($status)]]);
    }
}