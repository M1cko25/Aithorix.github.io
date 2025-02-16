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

        // try {
            // Check if user exists first
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return redirect()->back()->withErrors([
                    'email' => 'Email not found'
                ])->onlyInput('email');
            }
            $userId = User::where('email', $request->email)->value('id');
            $projects = Project::where('owner_id', $userId)->get();
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
                session()->put('isLoggedin', true);
                session()->put('user', $user);
                session()->put('projects', $projects);
                session()->put('members', $members);
                return redirect()->route('home');
            }
            return redirect()->back()->withErrors(['password' => 'Incorrect password'])->onlyInput('password');
        // } catch (\Exception $e) {
        //     return redirect()->back()->withErrors(['email' => 'error: ' . $e->getMessage()]);
        // }
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
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'avatar' => $request->avatar,
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