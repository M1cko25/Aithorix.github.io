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
                'avatar' => $gooleUser->avatar,
                'google_email' => $gooleUser->email,
                'google_id' => $gooleUser->id,
                'google_token' => $gooleUser->token,
                'email_verified_at' => now()
            ]);
            if ($newUser) {
                Auth::login($newUser);
            }
            return redirect()->route('template');
        }
    }

    public function slacksLogin()
    {
        return Socialite::driver('slack')->redirect();
    }
    public function slacksAuth() {
        $slackUser = Socialite::driver('slack')->user();
        $user = User::where('slack_id', $slackUser->id)->first();
        if ($user) {
            Auth::login($user);
            return redirect()->route('template');
        } else {
            $newUser = User::create([
                'name' => $slackUser->name,
                'avatar' => $slackUser->avatar,
                'slack_email' => $slackUser->email,
                'slack_id' => $slackUser->id,
                'slack_token' => $slackUser->token,
                'email_verified_at' => now()
            ]);
            if ($newUser) {
                Auth::login($newUser);
            }
            return redirect()->route('template');
        }
    }
}
