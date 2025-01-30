<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Project;
use App\Models\ProjectMembers;
use App\Models\User;

class TemplatesController extends Controller
{
    public function templateSelected(Request $request)
    {
        if (!$request->has('selectedTemplate')) {
            return redirect()->back();
        }
        return Inertia::render('ProjectCreation', [
            'selectedTemplate' => $request,
        ]);
    }

    // public function addProjectMembers(Request $request) {
    //     $userId = User::where('email', $request->email)->first()->id;
    //     ProjectMembers::create([
    //         'project_key' => $request->project_key,
    //         'user_id' => $userId,
    //         'role' => $request->role,
    //     ]);

    //     return redirect()->back()->with('success', 'Member added successfully');
    // }

    public function createProject(Request $request){
        $request->validate([
            'name' => 'required',
            'key' => 'required',
            'email' => 'required',
            'template' => 'required',
        ]);
        $projectKey = Project::where('key', $request->key)->first();
        if ($projectKey) {
            // return redirect()->back()->with('error', 'Project key already exists');
            return response()->json(['error' => 'Project key already exists'], 400);
        }

        $project = Project::create([
            'name' => $request->name,
            'key' => $request->key,
            'owner_email' => $request->email,
            'template' => $request->template,
        ]);

        foreach($request->members as $member) {
            $userId = User::where('email', $member['email'])
            ->orWhere('google_email', $member['email'])
            ->orWhere('slack_email', $member['email'])
            ->first()->id;
            ProjectMembers::create([
                'project_key' => $request->key,
                'user_id' => $userId,
                'role' => $member['role'],
            ]);
        }

        return redirect()->route('scrum-board', ['key' => $request->key])->with('success', 'Project created successfully');
    }
}
