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
            ->whereBetween('date', [Carbon::now()->subMonth(), Carbon::now()])
            ->orderBy('date', 'desc')
            ->get(['description', 'date', 'update', 'user_id'])
            ->map(function ($activity) {
                $activity->date = Carbon::parse($activity->date)->diffForHumans();
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

        return Inertia::render('Scrum/ScrumBoard', [
            'projectDetails' => $projectDetails,
        ]);
    }

    public function getTimelineDatas(Request $request) {
        $projectDetails = Project::where('id', $request->query('id'))->first();
        return  Inertia::render('Scrum/ScrumTimeline', [
            'projectDetails' => $projectDetails,
        ]);
    }
    public function getBacklogDatas(Request $request) {
        if ($request->query('id')) {
            $projectDetails = Project::where('id', $request->query('id'))->first();
        } else {
            $projectDetails = Project::where('id', $request->projectId)->first();
        }
        $sprint = [];
        $epics = Epics::where('project_id', $projectDetails->id)->get();
        $backlogs = Backlogs::where('project_id', $projectDetails->id)->get();
        foreach ($epics as $epic) {
            if ($epic->status == "On Sprint") {
                $sprint = Sprints::where('epic_id', $epic->id)
                ->where('status', 'Active')
                ->first();
            }
        }
        return Inertia::render('Scrum/ScrumBacklog', [
            'projectDetails' => $projectDetails,
            'backlogs' => $backlogs,
            'epics' => $epics,
            'sprint' => $sprint,
        ]);
    }

    public function updateEpicOrder(Request $request) {
        Log::info('Received epics data:', ['epics' => $request->epics]);
        $epics = $request->epics;
        
        foreach($epics as $index => $epic) {
            Epics::where('id', $epic['epic_id'])
                ->update(['order' => $index + 1]);
        }
        return response()->json(['success' => true]);
    }

    public function createEpic(Request $request) {
        $projId = $request->projectId;
        $epicNum = Epics::where('project_id', $projId)->count();
        $epic = Epics::create([
            'project_id' => $projId,
            'name' => $request->name,
            'description' => '',
            'progress_percent' => 0,
            'status' => 'Pending',
            'key' => $request->key,
            'order' => $epicNum + 1,
        ]);
        $this->registerUpdate($projId, Auth::user()->id, ' created an epic named ', $request->name);
        return response()->json(['success' => true, 'id' => $epic->id]);
    }

    public function createBacklog(Request $request) {
        $projId = $request->projectId;
        $backlogNum = Backlogs::where('project_id', $projId)
        ->where('epic_id', $request->epicId)
        ->count() + 1;
        $epic = Epics::where('id', $request->epicId)->first();
        $creator = ProjectMembers::where('user_id', Auth::user()->id)->value('id');
        $backlog = Backlogs::create([
            'title' => $request->title,
            'project_id' => $projId,
            'type' => $request->type,
            'description' => '',
            'priority' => $request->priority,
            'epic_id' => $request->epicId,
            'creator_id' => $creator,
            'status' => 'To Do',
            'order' => $request->order,
        ]);
        $this->registerUpdate($projId, Auth::user()->id, "created " . $request->title . " in ", $epic->name);
        return response()->json(['success' => true, 'id' => $backlog->id]);
    }

    public function deleteBacklog(Request $request) {
        $backlog = Backlogs::where('id', $request->id)->first();
        if ($backlog) {
            $epic = Epics::where('id', $request->epicId)->first();
            $desc = "deleted " . $request->title . " in ";
            $backlog->delete();
            $updateResult = $this->registerUpdate($request->projectId, Auth::user()->id, $desc, $epic->name);
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 404);
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

    public function updateBacklogStatus(Request $request) {
        $backlog = Backlogs::where('id', $request->id)->first();
        $backlog->status = $request->status;
        $backlog->save();
        
        $epic = Epics::where('id', $backlog->epic_id)->first();
        $this->registerUpdate($request->projectId, Auth::user()->id, "updated status of " . $backlog->title . " to " . $request->status . " in ", $epic->name);
        
        return response()->json(['success' => true]);
    }

    public function updateEpicStatus(Request $request) {
        $epic = Epics::where('id', $request->epicId)->first();
        $epic->status = $request->status;
        $epic->save();
        
        $this->registerUpdate(
            $request->projectId, 
            Auth::user()->id, 
            "updated epic status to " . $request->status . " for ", 
            $epic->name
        );
        
        return response()->json(['success' => true]);
    }
    
    public function startSprint(Request $request) {
        $request->validate([
            'projectId' => 'required|integer',
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);
        $epic = Epics::where('id', $request->epic_id)->first();
        $sprint = Sprints::create([
            'name' => $request->name,
            'epic_id' => $request->epic_id,
            'status' => 'Active',
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'description' => $request->description,
        ]);
        $this->registerUpdate($request->projectId, Auth::user()->id, "started a sprint named " . $request->name, $epic->name);
        return redirect()->back()->with('success', 'Sprint started successfully.');
    }
}

