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

class SprintController extends Controller
{
    public function addSprintTask(Request $request)
    {
        try {
            Log::info('Received request data:', $request->all());

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'sprintId' => 'required|exists:sprints,id',
                'epicId' => 'required|exists:epics,id',
                'projectId' => 'required|exists:projects,id'
            ]);

            Log::info('Validation passed');

            DB::beginTransaction();

            // Get the project key and backlog count for the new key
            $projectKey = Project::where('id', $request->projectId)->value('key');
            $backlogNum = Backlogs::where('project_id', $request->projectId)->count() + 1;

            Log::info('Creating backlog with key: ' . $projectKey . '-' . $backlogNum);
            $creator = ProjectMembers::where('project_id', $request->projectId)->where('user_id', Auth::user()->id)->first();
            // Create the backlog
            $backlog = Backlogs::create([
                'title' => $request->title,
                'project_id' => $request->projectId,
                'key' => $projectKey . '-' . $backlogNum,
                'type' => 'Task',
                'description' => '',
                'priority' => 'Medium',
                'epic_id' => $request->epicId,
                'creator_id' => $creator->id,
                'status' => 'To Do'
            ]);

            Log::info('Backlog created:', $backlog->toArray());

            // Calculate duration
            $startDate = new \DateTime($request->start_date);
            $endDate = new \DateTime($request->end_date);
            $duration = $startDate->diff($endDate)->days;

            // Create the sprint task
            $sprintTask = SprintTasks::create([
                'sprint_id' => $request->sprintId,
                'backlog_id' => $backlog->id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'duration' => $duration,
                'progress' => 0
            ]);

            Log::info('Sprint task created:', $sprintTask->toArray());

            // Load the backlog relationship
            $sprintTask->load('backlog');

            $this->registerUpdate(
                $request->projectId,
                Auth::user()->id,
                "added task " . $request->title . " to sprint",
                Sprints::find($request->sprintId)->name
            );

            DB::commit();

            Log::info('Transaction committed successfully');

            return response()->json([
                'success' => true,
                'task' => $sprintTask,
                'message' => 'Task added successfully'
            ]);

        } catch (ValidationException $e) {
            DB::rollBack();
            Log::error('Validation error:', ['errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error adding sprint task:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error adding task: ' . $e->getMessage()
            ], 500);
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

    public function updateSprintTask(Request $request)
    {
        $projId = $request->projectId;
        $sprintId = Sprints::where('epic_id', $request->epicId)->first()->id;
        $sprintTask = SprintTasks::where('sprint_id', $sprintId)->where('backlog_id', $request->taskId)->first();
        $sprint = Sprints::where('id', $sprintId)->first();
        $sprintTask->update([
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'duration' => $request->duration
        ]);

        $sprint->update([
            'start_date' => ($sprintTask->start_date < $sprint->start_date) ? $sprintTask->start_date : $sprint->start_date,
            'end_date' => ($sprintTask->end_date > $sprint->end_date) ? $sprintTask->end_date : $sprint->end_date,
            'duration' => $sprintTask->duration
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Sprint task updated successfully'
        ]);
    }

    public function completeSprint(Request $request) {
        try {
            DB::beginTransaction();
            
        $sprint = Sprints::where('epic_id', $request->epicId)
            ->where('status', 'Active')
            ->first();

            if (!$sprint) {
                return response()->json(['success' => false, 'message' => 'Sprint not found'], 404);
            }

            // Update sprint status
            $sprint->status = 'Completed';
            $sprint->save();

            // Update epic status
            $epic = Epics::findOrFail($request->epicId);
            $epic->status = 'Completed';
            $epic->save();

            // Calculate final progress based on completed tasks
            $totalTasks = SprintTasks::where('sprint_id', $sprint->id)->count();
            $completedTasks = SprintTasks::where('sprint_id', $sprint->id)
                ->whereHas('backlog', function($query) {
                    $query->where('status', 'Done');
                })->count();

            $finalProgress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 100;
            $epic->progress_percent = $finalProgress;
            $epic->save();

            $this->registerUpdate(
                $request->projectId,
                Auth::user()->id,
                "completed sprint for ",
                $epic->name
            );

            DB::commit();
            return response()->json([
                'success' => true,
                'epic' => $epic,
                'sprint' => $sprint
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error completing sprint: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error completing sprint'
            ], 500);
        }
    }

    public function startSprint(Request $request) {
        $request->validate([
            'projectId' => 'required|integer',
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);
        $epic = Epics::where('id', $request->epic_id)->first();
        $backlog = Backlogs::where('epic_id', $request->epic_id)->get();
        $sprint = Sprints::create([
            'name' => $request->name,
            'epic_id' => $request->epic_id,
            'status' => 'Active',
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'description' => $request->description ?? '',
        ]);
        foreach ($backlog as $task) {
            $progress = (($task->status == 'Done') ? 100 : (($task->status == 'In Progress') ? 50 : 0));
            $startDate = new \DateTime($request->start_date);
            $endDate = new \DateTime($request->end_date);
            $duration = $startDate->diff($endDate)->days;
            SprintTasks::create([
                'sprint_id' => $sprint->id,
                'backlog_id' => $task->id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'duration' => $duration,
                'progress' => $progress,
            ]);
        }
        $this->registerUpdate($request->projectId, Auth::user()->id, "started a sprint named " . $request->name, $epic->name);
        return redirect()->back()->with('success', 'Sprint started successfully.');
    }
}
