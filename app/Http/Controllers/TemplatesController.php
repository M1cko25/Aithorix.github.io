<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Project;
use App\Models\ProjectMembers;
use App\Models\User;
use App\Models\TaskStatusCol;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
            
            \DB::beginTransaction();
            
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

            $taskTypes = ['To Do', 'In Progress', 'Done'];
            foreach ($taskTypes as $taskType) {
                TaskStatusCol::create([
                    'title' => $taskType,
                    'project_id' => $projectCreated->id
                ]);
            }

            // Get fresh list of all projects for the user
            $userProjects = ProjectMembers::where('user_id', Auth::user()->id)
                ->with('project')
                ->get()
                ->map(function ($projectMember) {
                    return $projectMember->project;
                });

            \DB::commit();

            // Update session with fresh project list
            session()->put('projects', $userProjects);
            session()->put('members', $request->members);

            return redirect()->route('scrum-board', ['id' => $projectCreated->id]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->back()->withErrors([
                'error' => 'An error occurred while creating the project.',
            ]);
        }
    }

    public function createNewProject(Request $request){
        $request->validate([
            'name' => 'required|string|min:3|max:255',
            'key' => 'required|string|min:2|max:4|unique:projects,key',
            'owner_id' => 'required|integer|exists:users,id',
            'template' => [
                'required',
                'string',
                'in:Scrum,Education Purpose,Event Planning,Research Development,Content Calendar'
            ]
        ]);

        try {
            $project = Project::create([
                'name' => $request->name,
                'key' => strtoupper($request->key),
                'owner_id' => $request->owner_id,
                'template' => $request->template,
                'members' => count($request->members),
            ]);

            // Add members to the project
            foreach($request->members as $member) {
                ProjectMembers::create([
                    'project_id' => $project->id,
                    'user_id' => $member['id'],
                    'role' => $member['role'],
                ]);
            }

            $taskColumns = match($request->template) {
                'Scrum' => ['To Do', 'In Progress', 'Code Review', 'Testing', 'Done'],
                'Education Purpose' => ['Not Started', 'Working On', 'Need Help', 'Completed'],
                'Event Planning' => ['Planning', 'In Progress', 'Pending Review', 'Confirmed', 'Completed'],
                'Research Development' => ['Proposed', 'In Research', 'Analysis', 'Review', 'Published'],
                'Content Calendar' => ['Draft', 'Review', 'Scheduled', 'Published'],
                default => ['To Do', 'In Progress', 'Done']
            };

            foreach ($taskColumns as $columnTitle) {
                TaskStatusCol::create([
                    'title' => $columnTitle,
                    'project_id' => $project->id
                ]);
            }

            // Get all projects for the user to update session
            $userProjects = ProjectMembers::where('user_id', $request->owner_id)
                ->with('project')
                ->get()
                ->map(function ($projectMember) {
                    return $projectMember->project;
                });

            \DB::commit();

            // Update session data
            session()->put('projects', $userProjects);
            session()->put('members', $request->members);

            return response()->json([
                'success' => true,
                'message' => 'Project created successfully',
                'project' => $project,
                'redirect' => route('scrum-board', ['id' => $project->id])
            ]);

        } catch (\Exception $e) {
            \DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create project',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
