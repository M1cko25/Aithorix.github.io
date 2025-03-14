<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Project;
use App\Models\Backlogs;
use App\Models\Meetings;
use App\Models\TaskAssignees;
use App\Models\ProjectMembers;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function getUserDatas() {
        $user = Auth::user();
        
        // Get projects where user is a member with their members
        $userProjects = Project::whereIn('id', function($query) use ($user) {
            $query->select('project_id')
                ->from('project_members')
                ->where('user_id', $user->id);
        })->get();

        // Get project members for all user's projects
        $projectMembers = ProjectMembers::whereIn('project_id', $userProjects->pluck('id'))
            ->with('user:id,name,avatar')
            ->get()
            ->groupBy('project_id');

        // Get total tasks assigned to user
        $assignedTasks = TaskAssignees::where('user_id', $user->id)->count();

        // Get completed tasks for user
        $completedTasks = TaskAssignees::whereIn('task_id', function($query) {
            $query->select('id')
                ->from('backlogs')
                ->where('status', 'Done');
        })->where('user_id', $user->id)->count();

        // Get upcoming tasks for user
        $upcomingTasks = Backlogs::whereIn('id', function($query) use ($user) {
            $query->select('task_id')
                ->from('task_assignees')
                ->where('user_id', $user->id);
        })->where('status', '!=', 'Done')
        ->with(['project:id,name'])
        ->get(['id', 'title', 'project_id', 'status']);

        // Get upcoming meetings
        $upcomingMeetings = Meetings::whereIn('project_id', $userProjects->pluck('id'))
            ->where('date', '>=', Carbon::today())
            ->orderBy('date')
            ->orderBy('start_time')
            ->limit(5)
            ->get(['id', 'name', 'date', 'start_time']);

        // Format projects data for the home page cards
        $projectsData = $userProjects->map(function($project) use ($projectMembers) {
            $members = isset($projectMembers[$project->id]) 
                ? $projectMembers[$project->id]->map(function($member) {
                    return [
                        'id' => $member->user->id,
                        'name' => $member->user->name,
                        'avatar' => $member->user->avatar
                    ];
                })->values()->all()
                : [];

            return [
                'name' => $project->name,
                'tag' => $project->template,
                'tagColor' => 'bg-blue-100 text-blue-800',
                'members' => $members
            ];
        });

        return Inertia::render('Home', [
            'stats' => [
                'totalProjects' => $userProjects->count(),
                'totalTasks' => Backlogs::whereIn('project_id', $userProjects->pluck('id'))->count(),
                'assignedTasks' => $assignedTasks,
                'completedTasks' => $completedTasks
            ],
            'tasks' => $upcomingTasks->map(function($task) {
                return [
                    'title' => $task->title,
                    'project' => $task->project->name,
                    'dateRange' => $task->status
                ];
            }),
            'projectCards' => $projectsData,
            'meetings' => $upcomingMeetings->map(function($meeting) {
                return [
                    'title' => $meeting->name,
                    'time' => Carbon::parse($meeting->date)->format('M d') . ' at ' . Carbon::parse($meeting->start_time)->format('H:i'),
                    'icon' => '/path/to/meeting/icon.png'
                ];
            }),
            // Add projects data for the sidebar in the expected format
            'projects' => [
                'project' => $userProjects->map(function($project) use ($projectMembers) {
                    return [
                        'id' => $project->id,
                        'name' => $project->name,
                        'key' => $project->key,
                        'members' => isset($projectMembers[$project->id]) 
                            ? $projectMembers[$project->id]->map(function($member) {
                                return [
                                    'id' => $member->user->id,
                                    'name' => $member->user->name,
                                    'avatar' => $member->user->avatar
                                ];
                            })->values()->all()
                            : []
                    ];
                })
            ]
        ]);
    }
}
