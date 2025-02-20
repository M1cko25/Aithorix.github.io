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
use App\Models\Epic;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
        $projectDetails = Project::where('id', $request->query('id'))->first();

        $epics = Epic::where('project_id', $projectDetails->id)->get();
        $backlogs = Backlogs::where('project_id', $projectDetails->id)->get();
        return Inertia::render('Scrum/ScrumBacklog', [
            'projectDetails' => $projectDetails,
            'backlogs' => $backlogs,
            'epics' => $epics,
        ]);
    }

    public function updateEpicOrder(Request $request) {
        $epics = $request->epics;
        
        foreach($epics as $index => $epic) {
            Epic::where('id', $epic['epic_id'])
                ->update(['order' => $index + 1]);
        }
        return response()->json(['success' => true]);
    }

    public function createEpic(Request $request) {
        $projId = $request->projectId;
        $epicNum = Epic::where('project_id', $projId)->count() + 1;
        Epic::create([
            'project_id' => $projId,
            'name' => $request->name,
            'description' => '',
            'progress_precent' => 0,
            'key' => $request->key,
            'order' => $epicNum + 1,
        ]);
        return response()->json(['success' => true]);
    }

    public function createBacklog(Request $request) {
        $projId = $request->projectId;
        $backlogNum = Backlogs::where('project_id', $projId)
        ->where('epic_id', $request->epicId)
        ->count() + 1;
        $epic = Epic::where('id', $request->epicId)->first();
        $backlogCreated = Backlogs::create([
            'title' => $request->title,
            'project_id' => $projId,
            'type' => $request->type,
            'description' => '',
            'priority' => $request->priority,
            'epic_id' => $request->epicId,
            'creator_id' => Auth::user()->id,
            'status' => 'To Do',
            'order' => $request->order,
        ]);
        $this->registerUpdate($projId, Auth::user()->id, "created " . $request->title . " in ", $epic->name);
        return response()->json(['success' => true, 'id' => $backlogCreated->id]);
    }

    public function deleteBacklog(Request $request) {
        $backlog = Backlogs::where('id', $request->id)->first();
        $backlog->delete();
        $epic = Epic::where('id', $request->epicId)->first();
        $desc = "deleted " . $request->title . " in ";
        $updateResult = $this->registerUpdate($request->projectId, Auth::user()->id, $desc, $epic->name);
        if ($updateResult) {
            return response()->json(['success' => true]);;
        }
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
}

