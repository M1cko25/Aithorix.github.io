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
class ScrumController extends Controller
{
    public function getDashboardDatas() {
        $toDoBacklogs = Backlogs::where('creator_id', session('user.id'))
        ->where('status', 'pending')
        ->count();
        $progressBacklogs = Backlogs::where('creator_id', session('user.id'))
        ->where('status', 'in_progress')
        ->count();
        $completedBacklogs = Backlogs::where('creator_id', session('user.id'))
        ->where('status', 'completed')
        ->count();
        $meetingCreated = Meetings::where('creator_id', session('user.id'))
        ->count();
        $meetings = Meetings::where('creator_id', session('user.id'))->get(['id','date', 'start_time', 'end_time']);
        $sprints = Sprint::where('project_id', session('project.id'))
        ->count();
        $activities = Activity::with('user:id,name,avatar')
        ->where('project_id', session('project.id'))
        ->whereBetween('date', [Carbon::now()->subMonth(), Carbon::now()])
        ->orderBy('date', 'desc')
        ->get(['description', 'date', 'update', 'user_id'])
        ->map(function ($activity) {
            $activity->date = Carbon::parse($activity->date)->diffForHumans();
            return $activity;
        });
        $onTimeParticipant = MeetingParticipants::with('meeting:id,date,start_time,end_time')
        ->where('meeting_id', 1)
        ->where('status', 'on time')
        ->count();
        $lateParticipant = MeetingParticipants::with('meeting:id,date,start_time,end_time')
        ->where('meeting_id', 1)
        ->where('status', 'late')
        ->count();
        $absentParticipant = MeetingParticipants::with('meeting:id,date,start_time,end_time')
        ->where('meeting_id', 1)
        ->where('status', 'absent')
        ->count();
        $totalMembers = ProjectMembers::where('project_id', session('project.id'))->count();

        return Inertia::render('Scrum/ScrumDashboard', [
            'toDoBacklogs' => $toDoBacklogs,
            'progressBacklogs' => $progressBacklogs,
            'completedBacklogs' => $completedBacklogs,
            'meetingCreated' => $meetingCreated,
            'meetings' => $meetings,
            'onTimeParticipant' => $onTimeParticipant,
            'lateParticipant' => $lateParticipant,
            'absentParticipant' => $absentParticipant,
            'sprints' => $sprints,
            'activities' => $activities,
            'totalMembers' => $totalMembers,
        ]);
    }
}
