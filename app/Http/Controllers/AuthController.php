<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use App\Mail\WelcomeMail;
use App\Models\Project;
use App\Models\ProjectMembers;
use Illuminate\Support\Facades\Validator;

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
        $userId = User::where('email', $request->email)->value('id');
        $projectMember = ProjectMembers::where('user_id', $userId)->get();
        $projects = [];
        foreach ($projectMember as $member) {
            $project = Project::where('id', $member->project_id)->first();
            array_push($projects, $project);
        }
        $members = [];
        foreach ($projects as $project) {
            $member = ProjectMembers::where('project_id', $project->id)->get();
            array_push($members, $member);
        }

        if (!$projects) {
            Auth::login($user);
            return redirect()->route('template');
        }

        //log in
        if (Auth::attempt($credentials, $request->remember)) {
            session_start();
            $request->session()->regenerate();
            session()->put('user', $user);
            session()->put('projects', $projects);
            session()->put('members', $members);
            return redirect()->route('home');
        }
        return redirect()->back()->withErrors(['password' => 'Incorrect password'])->onlyInput('password');
    }

    public function verify(Request $request) {

        try {
            if ($request->email == null) {
                return redirect()->back()->withErrors(['email' => 'Email is required']);
            }
            return Inertia::render('Auth/Setup', [
                'email' => $request->email,
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['email' => 'An error occurred while logging in.']);
        }
    }

    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255|regex:/^[a-zA-Z\s]+$/',
            'password' => 'required|min:8|max:255|confirmed',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->with('email', $request->email)->withErrors($validator->errors())->withInput();
        }

        $colors = ['#3A5573', '#032F61', '#0F7E78', '#AE4432', '#B9850D'];
        $randomColor = $colors[array_rand($colors)];
        $avatar = $request->avatar;
        
        if ($avatar == null) {
            $initials = strtoupper(substr($request->name, 0, 2));
            $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100">'
                 . '<circle cx="50" cy="50" r="50" fill="' . $randomColor . '"/>'
                 . '<text x="50%" y="50%" fill="white" text-anchor="middle" alignment-baseline="central" '
                 . 'font-family="Arial, sans-serif" font-size="40" font-weight="bold" style="dominant-baseline: central;">'
                 . $initials
                 . '</text>'
                 . '</svg>';
            $avatar = 'data:image/svg+xml;base64,' . base64_encode($svg);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'avatar' => $avatar,
            'google_id' => $request->google_id,
            'password' => bcrypt($request->password),
            'email_verified_at' => now()
        ]);
        
        Auth::login($user);
        session()->put('user', $user);
        Mail::to($request->email)->send(new WelcomeMail($request->name));
        return redirect()->route('template')->with('success', 'Registration successful.');
    }

    public function logout(Request $request) {
        Auth::guard('web')->logout();
        session_start();
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
     
        return $status === Password::PASSWORD_RESET
                    ? redirect()->back()->with('status', __($status))
                    : back()->withErrors(['email' => [__($status)]]);
    }
}