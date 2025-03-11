<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Project;
use App\Models\ProjectMembers;
use App\Models\User;
use App\Models\TaskStatusCol;
use Illuminate\Support\Facades\Auth;
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
        $request->validate([
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
                'members' => count($request->members),
            ]);
            foreach($request->members as $member) {
                ProjectMembers::create([
                    'project_id' => $projectCreated->id,
                    'user_id' => $member['id'],
                    'role' => $member['role'],
                ]);
            }

            // Initialize projects array and name count tracker
            $projects = [];
            $nameCount = [];

            // Get all projects for the user
            $getAllProjects = ProjectMembers::where('user_id', Auth::user()->id)->get();
            
            foreach ($getAllProjects as $projectMember) {
                $project = Project::where('id', $projectMember->project_id)->first();
                
                if ($project) {
                    $originalName = $project->name;
                    
                    // Check if this name already exists
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

            $taskTypes = ['To Do', 'In Progress', 'Done'];
            foreach ($taskTypes as $taskType) {
                TaskStatusCol::create([
                    'title' => $taskType,
                    'project_id' => $projectCreated->id
                ]);
            }

            session()->put('projects', $projects);
            session()->put('members', $request->members);
            return redirect()->route('scrum-board', ['id' => $projectCreated->id]);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([
                'error' => 'An error occurred while creating the project.',
            ]);
        }
    }
}
