<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Backlogs;
use App\Models\Meetings;
use App\Models\Sprint;
use App\Models\Project;
use Inertia\Inertia;
use App\Models\Activity;
use App\Models\MeetingParticipants;
use App\Models\ProjectMembers;
use Carbon\Carbon;
use App\Models\Epic;

class ScrumController extends Controller
{
    public function getDashboardDatas(Request $request) {
        $projectDetails = Project::where('id', $request->query('id'))->first();
    
        $toDoBacklogs = Backlogs::where('project_id', $projectDetails->id)
            ->where('status', 'to do')
            ->count();
            
        $progressBacklogs = Backlogs::where('project_id', $projectDetails->id)
            ->where('status', 'in_progress')
            ->count();
            
        $completedBacklogs = Backlogs::where('project_id', $projectDetails->id)
            ->where('status', 'completed')
            ->count();
            
        $meetingCreated = Meetings::where('project_id', $projectDetails->id)
            ->count();
            
        $meetings = Meetings::where('project_id', $projectDetails->id)
            ->get(['id','date', 'start_time', 'end_time']);
            
        $sprints = Sprint::where('project_id', $projectDetails->id)
            ->count();
            
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
            'sprints' => $sprints,
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
            'epics' => $epics,
        ]);
    }
}
