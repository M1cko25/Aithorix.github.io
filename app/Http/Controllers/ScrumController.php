<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Backlogs;
use App\Models\Meetings;
use App\Models\Sprint;
use App\Models\Project;
use Inertia\Inertia;

class ScrumController extends Controller
{
    public function getDashboardDatas() {
        $pendingBacklogs = Backlogs::where('creator_id', session('user.id'))
        ->where('status', 'pending')
        ->orWhere('status', 'in_progress')
        ->count();
        $completedBacklogs = Backlogs::where('creator_id', session('user.id'))
        ->where('status', 'completed')
        ->count();
        $meetingCreated = Meetings::where('creator_id', session('user.id'))
        ->count();
        $sprints = Sprint::where('project_id', 1)
        ->count();
        return Inertia::render('Scrum/ScrumDashboard', [
            'pendingBacklogs' => $pendingBacklogs,
            'completedBacklogs' => $completedBacklogs,
            'meetingCreated' => $meetingCreated,
            'sprints' => $sprints,
            'projectName' => session('project.name'),
            'projectKey' => session('project.key'),
        ]);
    }

    public  function getProjectDetails() {
        return Inertia::render('Scrum/ScrumBoard', [
            'projectName' => session('project.name'),
            'projectKey' => session('project.key'),
        ]);
    }
}
