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
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6|max:255'
        ]);

        //sign in
        if (Auth::attempt($request->only('email', 'password'))) {
            return redirect()->route('template');
        }

        return redirect()->back()->withErrors(['email' => 'Incorrect email', 'password' => 'Incorrect pasword']);
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
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
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
     
        return $status === Password::PasswordReset
                    ? redirect()->route('login')->with('status', __($status))
                    : back()->withErrors(['email' => [__($status)]]);
    }
}
