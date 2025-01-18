<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SocialiteController extends Controller
{
    public function googleLogin()
    {
        return Socialite::driver('google')->redirect();
    }

    public function googleAuth()
    {
        $gooleUser = Socialite::driver('google')->user();
        $user = User::where('google_id', $gooleUser->id)->first();
        if ($user) {
            Auth::login($user);
            return redirect()->route('template');
        } else {
            $newUser = User::create([
                'name' => $gooleUser->name,
                'email' => $gooleUser->email,
                'avatar' => $gooleUser->avatar,
                'google_id' => $gooleUser->id,
                'password' => Hash::make('password'),
                'email_verified_at' => now()
            ]);
            if ($newUser) {
                Auth::login($newUser);
            }
            return redirect()->route('template');
        }
        dd($gooleUser);
    }
}
