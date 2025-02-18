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

    public function createProject(Request $request){
        $credentials = $request->validate([
            'name' => 'required',
            'key' => 'required|min:2|max:4',
            'owner_id' => 'required|integer',
            'template' => 'required',
        ]); 

        try {
            $projectKey = Project::where('owner_id', $request->owner_id)
            ->where('key', $request->key)
            ->exists();
            if ($projectKey == $request->key) {
                return redirect()->back()->withErrors([
                    'key' => 'The key is already in use.',
                ]);
            }

            foreach($request->members as $member) {
                $projMember = ProjectMembers::where('user_id', $member['id'])->first();
                if ($projMember && $projMember->project_key == $request->key) {
                    return redirect()->back()->withErrors([
                        'key' => 'The key is already been used by another member.',
                    ]);
                }
            }
            
            $members = [];
            $projectCreated = Project::create([
                'name' => $request->name,
                'key' => $request->key,
                'owner_id' => $request->owner_id,
                'template' => $request->template,
            ]);
            foreach($request->members as $member) {
                ProjectMembers::create([
                    'project_id' => $projectCreated->id,
                    'user_id' => $member['id'],
                    'role' => $member['role'],
                ]);
            }
            $getAllProjects = Project::where('owner_id', session('user.id'))->get();
            foreach ($getAllProjects as $project) {
                $member = ProjectMembers::where('project_id', $project->id)->get();
                array_push($members, $member);
            }
            session()->put('projects', $getAllProjects);
            session()->put('members', $request->members);
            return redirect()->route('scrum-board', ['id' => $projectCreated->id]);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([
                'error' => 'An error occurred while creating the project.',
            ]);
        }
    }
}
