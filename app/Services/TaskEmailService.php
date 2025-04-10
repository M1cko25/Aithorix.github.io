<?php

namespace App\Services;

use App\Mail\TaskAssignedMail;
use App\Mail\NewTaskCreatedMail;
use App\Models\Backlogs;
use App\Models\Epics;
use App\Models\Project;
use App\Models\ProjectMembers;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class TaskEmailService
{
    /**
     * Send email notification to user when they are assigned a task
     */
    public static function sendTaskAssignedEmail(Backlogs $task, User $assignee)
    {
        try {
            $project = Project::find($task->project_id);
            $assignedBy = Auth::user();

            if ($assignee->email) {
                Mail::to($assignee->email)->send(
                    new TaskAssignedMail($task, $assignedBy, $project->name)
                );
            } else if ($assignee->google_email) {
                Mail::to($assignee->google_email)->send(
                    new TaskAssignedMail($task, $assignedBy, $project->name)
                );
            } else if($assignee->slack_email) {
                Mail::to($assignee->slack_email)->send(
                    new TaskAssignedMail($task, $assignedBy, $project->name)
                );
            } else {
                Log::info('No user');
            }


            Log::info("Task assignment email sent to {$assignee->email} for task {$task->id}");

            // Also create a notification in the database
            return true;
        } catch (\Exception $e) {
            Log::error("Error sending task assignment email: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Send email notification to all project members when a new task is created
     */
    public static function sendNewTaskCreatedEmail(Backlogs $task)
    {
        try {
            $project = Project::find($task->project_id);
            $createdBy = Auth::user();
            $epic = Epics::find($task->epic_id);

            // Get all project members except the creator
            $projectMembers = ProjectMembers::where('project_id', $task->project_id)
                ->where('user_id', '!=', $createdBy->id) // Skip the creator
                ->with('user')
                ->get();

            foreach ($projectMembers as $member) {
                if ($member->user && $member->user->email) {
                    Mail::to($member->user->email)->send(
                        new NewTaskCreatedMail($task, $createdBy, $project->name, $epic->name)
                    );
                } else if ($member->user->google_email) {
                    Mail::to($member->user->google_email)->send(
                        new NewTaskCreatedMail($task, $createdBy, $project->name, $epic->name)
                    );
                } else if($member->user->slack_email) {
                    Mail::to($member->user->slack_email)->send(
                        new NewTaskCreatedMail($task, $createdBy, $project->name, $epic->name)
                    );
                } else {
                    Log::info('No user');
                }
            }

            Log::info("Task creation emails sent to project members for task {$task->id}");
            return true;
        } catch (\Exception $e) {
            Log::error("Error sending task creation emails: {$e->getMessage()}");
            return false;
        }
    }
}
