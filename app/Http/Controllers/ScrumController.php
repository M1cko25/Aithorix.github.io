<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Backlogs;
use App\Models\Meetings;
use App\Models\Project;
use Inertia\Inertia;
use App\Models\Activity;
use App\Models\MeetingParticipants;
use App\Models\ProjectMembers;
use Carbon\Carbon;
use App\Models\Epics;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Sprints;
use App\Models\TaskAttachments;
use Illuminate\Support\Facades\Storage;
use App\Models\TaskComments;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\TaskStatusCol;
use Illuminate\Validation\ValidationException;
use App\Models\SprintTasks;


class ScrumController extends Controller
{
    public function getDashboardDatas(Request $request) {
        $projectDetails = Project::where('id', $request->query('id'))->first();
    
        $toDoBacklogs = Backlogs::where('project_id', $projectDetails->id)
            ->where('status', 'To Do')
            ->count();
            
        $progressBacklogs = Backlogs::where('project_id', $projectDetails->id)
            ->where('status', 'In Progress')
            ->count();
            
        $completedBacklogs = Backlogs::where('project_id', $projectDetails->id)
            ->where('status', 'Done')
            ->where('updated_at', '>=', Carbon::now()->subDays(7))
            ->count();
            
        $meetingCreated = Meetings::where('project_id', $projectDetails->id)
        ->where('created_at', '>=', Carbon::now()->subDays(7))
        ->count();
            
        $meetings = Meetings::where('project_id', $projectDetails->id)
            ->get(['id','date', 'start_time', 'end_time']);
            
        $backlogCreated = Backlogs::where('project_id', $projectDetails->id)
        ->where('created_at', '>=', Carbon::now()->subDays(7))->count();
            
        $activities = Activity::with('user:id,name,avatar')
            ->where('project_id', $projectDetails->id)
            ->whereBetween('created_at', [now()->subMonth(), now()])
            ->orderBy('created_at', 'desc')
            ->get(['description', 'date', 'update', 'user_id', 'created_at'])
            ->map(function ($activity) {
                $activity->created = Carbon::parse($activity->created_at)->diffForHumans();
                return $activity;
            });
        if ($meetings->isNotEmpty()) {
            $onTime = MeetingParticipants::where('meeting_id', $meetings->pluck('id'))
            ->where('status', 'on time')->count();
            $late = MeetingParticipants::where('meeting_id', $meetings->pluck('id'))
            ->where('status', 'late')->count();
            $absent = MeetingParticipants::where('meeting_id', $meetings->pluck('id'))
            ->where('status', 'absent')->count();
        } else {
            $onTime = 0;
            $late = 0;
            $absent = 0;
        }
        $totalMembers = Project::where('id', $projectDetails->id)->value('members');
    
        return Inertia::render('Scrum/ScrumDashboard', [
            'projectDetails' => $projectDetails,
            'toDoBacklogs' => $toDoBacklogs,
            'progressBacklogs' => $progressBacklogs,
            'completedBacklogs' => $completedBacklogs,
            'meetingCreated' => $meetingCreated,
            'meetings' => $meetings,
            'backlogCreated' => $backlogCreated,
            'activities' => $activities,
            'onTime' => $onTime,
            'late' => $late,
            'absent' => $absent,
            'totalMembers' => $totalMembers,
        ]);
    }
    
    public function getBoardDatas(Request $request) {
        $projectDetails = Project::where('id', $request->query('id'))->first();
        
        // Get all epics for the project
        $epics = Epics::where('project_id', $projectDetails->id)->get();
        
        // Get all backlogs with their assignees
        $backlogs = Backlogs::with(['assignees', 'attachments'])
            ->where('project_id', $projectDetails->id)
            ->get();

        $comments = [];

        foreach ($backlogs as $backlog) {
            // Get comments for this specific task and store them with the task ID as key
            $taskComments = TaskComments::with('user')
                ->where('task_id', $backlog->id)
                ->get();
            
            if ($taskComments->isNotEmpty()) {
                $comments[$backlog->id] = $taskComments;
            }
        }
        $taskStatus = TaskStatusCol::where('project_id', $projectDetails->id)
            ->orderBy('id')
            ->get();
        return Inertia::render('Scrum/ScrumBoard', [
            'projectDetails' => $projectDetails,
            'epics' => $epics,
            'backlogs' => $backlogs,
            'comments' => $comments,
            'columns' => $taskStatus,
        ]);
    }

    public function getTimelineDatas(Request $request) {
        try {
        $projectDetails = Project::where('id', $request->query('id'))->first();
            $epics = Epics::where('project_id', $request->query('id'))->get();
            
            // Initialize empty collections
            $backlogs = collect();
            $sprints = collect();
            $sprintTasks = collect();

            // Collect all backlogs
            foreach($epics as $epic) {
                $epicBacklogs = Backlogs::with(['attachments', 'assignees'])
                ->where('epic_id', $epic->id)->
                where('epic_id', $epic->id)->get();
                $backlogs = $backlogs->concat($epicBacklogs);
            }

            // Collect all sprints
            foreach ($epics as $epic) {
                $epicSprints = Sprints::where('epic_id', $epic->id)->get()
                ->map(function ($sprint) {
                    $sprint->dated = Carbon::parse($sprint->updated_at)->diffForHumans();
                    return $sprint;
                });
                $sprints = $sprints->concat($epicSprints);
            }

            // Collect all sprint tasks
            foreach ($sprints as $sprint) {
                $tasks = SprintTasks::where('sprint_id', $sprint->id)->get();
                $sprintTasks = $sprintTasks->concat($tasks);
            }
            
            return Inertia::render('Scrum/ScrumTimeline', [
            'projectDetails' => $projectDetails,
                'sprints' => $sprints->values()->all(), // Convert to array and reindex
                'sprintTasks' => $sprintTasks->values()->all(),
                'backlogs' => $backlogs->values()->all(),
                'epics' => $epics
            ]);

        } catch (\Exception $e) {
            Log::error('Error in getTimelineDatas: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving timeline data'
            ], 500);
        }
    }
    public function getBacklogDatas(Request $request) {
        if ($request->query('id')) {
            $projectDetails = Project::where('id', $request->query('id'))->first();
        } else {
            $projectDetails = Project::where('id', $request->projectId)->first();
        }
        $sprint = [];
        $epics = Epics::where('project_id', $projectDetails->id)->get();
        $backlogs = Backlogs::with(['attachments', 'assignees'])
            ->where('project_id', $projectDetails->id)
            ->get();

        // Initialize comments as an associative array
        $comments = [];

        foreach ($backlogs as $backlog) {
            // Get comments for this specific task and store them with the task ID as key
            $taskComments = TaskComments::with('user')
                ->where('task_id', $backlog->id)
                ->get();
            
            if ($taskComments->isNotEmpty()) {
                $comments[$backlog->id] = $taskComments;
            }
        }
        
        // Fix project members loading
        $projectMembers = User::whereIn('id', function($query) use ($projectDetails) {
            $query->select('user_id')
                ->from('project_members')
                ->where('project_id', $projectDetails->id);
        })->select('id', 'name', 'avatar')->get();

        foreach ($epics as $epic) {
            if ($epic->status == "On Sprint") {
                $sprint = Sprints::where('epic_id', $epic->id)
                ->where('status', 'Active')
                ->first();
            }
        }
        $taskStatus = TaskStatusCol::where('project_id', $projectDetails->id)
            ->orderBy('id')
            ->get();
        return Inertia::render('Scrum/ScrumBacklog', [
            'projectDetails' => $projectDetails,
            'backlogs' => $backlogs,
            'epics' => $epics,
            'sprint' => $sprint,
            'comments' => $comments,
            'projectMembers' => $projectMembers,
            'columns' => $taskStatus,
        ]);
    }

    private function registerUpdate($projectId ,$userId, $description,$subject) {
        try {
            $activity = Activity::create([ 
                'user_id' => $userId,
                'description' => $description,
                'date' => now(),
                'project_id' => $projectId,
                'update' => $subject
            ]);
            Log::info('Activity created:', ['activity' => $activity]);
            return true;
        } catch(\Exception $e) {
            Log::error('Activity creation failed:', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
            throw $e;
        }
    }

    public function getProjectMembers($projectId)
    {
        try {
            $members = ProjectMembers::where('project_id', $projectId)
                ->with('user:id,name,avatar')
                ->get()
                ->map(function ($member) {
                    return [
                        'id' => $member->id,
                        'user_id' => $member->user_id,
                        'name' => $member->user->name,
                        'avatar' => $member->user->avatar,
                        'role' => $member->role
                    ];
                });

            return response()->json([
                'success' => true,
                'members' => $members
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting project members: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving project members'
            ], 500);
        }
    }

    public function addColumn(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'projectId' => 'required|exists:projects,id'
        ]);

        try {
            $column = TaskStatusCol::create([
                'title' => $request->title,
                'project_id' => $request->projectId
            ]);

            $this->registerUpdate(
                $request->projectId,
                Auth::user()->id,
                "added new column ",
                $request->title
            );

            return response()->json([
                'success' => true,
                'id' => $column->id,
                'message' => 'Column added successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error adding column: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error adding column'
            ], 500);
        }
    }

    public function deleteColumn(Request $request)
    {
        $request->validate([
            'columnId' => 'required',
            'projectId' => 'required|exists:projects,id'
        ]);

        try {
            $column = TaskStatusCol::findOrFail($request->columnId);
            
            // Update all tasks in this column to 'To Do' status
            Backlogs::where('project_id', $request->projectId)
                ->where('status', $column->title)
                ->update(['status' => 'To Do']);
            $column->delete();

            $this->registerUpdate(
                $request->projectId,
                Auth::user()->id,
                "deleted column ",
                $column->title
            );

            return response()->json([
                'success' => true,
                'message' => 'Column deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting column: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting column'
            ], 500);
        }
    }
}