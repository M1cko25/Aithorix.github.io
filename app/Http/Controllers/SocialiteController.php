<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use App\Models\ProjectMembers;
use Illuminate\Support\Facades\Log;

class SocialiteController extends Controller
{
    public function googleLogin()
    {
        return Socialite::driver('google')->redirect();
    }

    public function googleAuth()
    {
        try {
            $gooleUser = Socialite::driver('google')->user();
            $user = User::where('google_id', $gooleUser->id)->first();
            if ($user) {
                $userId = User::where('google_id', $gooleUser->id)->value('id');
                $projectMember = ProjectMembers::where('user_id', $userId)->get();
                
                // Initialize arrays and name count tracker
                $projects = [];
                $nameCount = [];

                foreach ($projectMember as $member) {
                    $project = Project::where('id', $member->project_id)->first();
                    if ($project) {
                        $originalName = $project->name;
                        
                        // Check if this name already exists in our projects
                        if (isset($nameCount[$originalName])) {
                            $nameCount[$originalName]++;
                            $project->name = $originalName . ' (' . $nameCount[$originalName] . ')';
                        } else {
                            // First occurrence of this name
                            $nameCount[$originalName] = 0;
                        }
                        
                        array_push($projects, $project);
                    }
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

                session()->put('projects', $projects);
                session()->put('members', $members);
                session()->put('user', $user);
                Auth::login($user);
                Log::info('User logged in: ' . $user->name);
                return redirect()->route('home');
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
                Log::info('New user created: ' . $newUser->name);
                return redirect()->route('template');
            }
        } catch (\Exception $e) {
            Log::error('Error in googleAuth: ' . $e->getMessage());
            return redirect()->route('login');
        }
    }

    public function slacksLogin()
    {
        return Socialite::driver('slack')->redirect();
    }
    public function slacksAuth() {
        try {
            $slackUser = Socialite::driver('slack')->user();
        $user = User::where('slack_id', $slackUser->id)->first();
        if ($user) {
            $userId = User::where('slack_id', $slackUser->id)->value('id');
            $projectMember = ProjectMembers::where('user_id', $userId)->get();
            
            // Initialize arrays and name count tracker
            $projects = [];
            $nameCount = [];

            foreach ($projectMember as $member) {
                $project = Project::where('id', $member->project_id)->first();
                if ($project) {
                    $originalName = $project->name;
                    
                    // Check if this name already exists in our projects
                    if (isset($nameCount[$originalName])) {
                        $nameCount[$originalName]++;
                        $project->name = $originalName . ' (' . $nameCount[$originalName] . ')';
                    } else {
                        // First occurrence of this name
                        $nameCount[$originalName] = 0;
                    }
                    
                    array_push($projects, $project);
                }
            }

            $members = [];
            foreach ($projects as $project) {
                $member = ProjectMembers::where('project_id', $project->id)->get();
                array_push($members, $member);
            }

            if (!$project) {
                Auth::login($user);
                return redirect()->route('template');
            }
            session()->put('projects', $projects);
            session()->put('members', $members);
            session()->put('user', $user);
            Auth::login($user);
            return redirect()->route('home');
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
            Log::info('New user created: ' . $newUser->name);
            return redirect()->route('template');
        }
        }catch (\Exception $e) {
            Log::error('Error in slackAuth: ' . $e->getMessage());
            return redirect()->route('login');
        }
    }
}
