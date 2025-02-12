<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use phpseclib3\Crypt\RC2;

class SocialiteController extends Controller
{
    public function googleLogin()
    {
        return Socialite::driver('google')->redirect();
    }

    public function googleAuth()
    {
       try{ $gooleUser = Socialite::driver('google')->user();
        $user = User::where('google_id', $gooleUser->id)->first();
        if ($user) {
            $userId = User::where('google_id', $gooleUser->id)->value('id');
            $project = Project::where('owner_id', $userId)->first();
            if (!$project) {
                Auth::login($user);
                return redirect()->route('template');
            }
            session()->put('project', $project);
            session()->put('user', $user);
            Auth::login($user);
            return redirect()->route('scrum-board');
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
                session()->put('user', $newUser);
                Auth::login($newUser);
            }
            return redirect()->route('template');
        }}
        catch (\Exception $e) {
            return redirect()->route('login');
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
            $userId = User::where('slack_id', $slackUser->id)->value('id');
            $project = Project::where('owner_id', $userId)->first();
            if (!$project) {
                Auth::login($user);
                return redirect()->route('template');
            }
            session()->put('project', $project);
            session()->put('user', $user);
            Auth::login($user);
            return redirect()->route('scrum-board');
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
                session()->put('user', $newUser);
                Auth::login($newUser);
            }
            return redirect()->route('template');
        }
    }
}
