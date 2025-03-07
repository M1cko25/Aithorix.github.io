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
        $projectDetails = Project::where('id', $request->query('id'))->first();
        $epics = Epics::where('project_id', $projectDetails->id)->get();
        $backlogs = Backlogs::with(['attachments', 'assignees'])
            ->where('project_id', $projectDetails->id)
            ->get();
        return  Inertia::render('Scrum/ScrumTimeline', [
            'projectDetails' => $projectDetails,
            'epics' => $epics,
            'backlogs' => $backlogs,
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
        try {
            // Get current date for start_date and add 7 days for end_date
            $startDate = now();
            $endDate = now()->addDays(7);

            $epicData = [
                'project_id' => $projId,
                'name' => $request->name,
                'description' => '',
                'progress_percent' => 0,
                'status' => 'Pending',
                'key' => $request->key,
                'order' => $epicNum + 1,
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d')
            ];

            $epic = Epics::create($epicData);
            
            $this->registerUpdate($projId, Auth::user()->id, ' created an epic named ', $request->name);
            
            return response()->json([
                'success' => true,
                'id' => $epic->id,
                'name' => $epic->name,
                'description' => $epic->description,
                'status' => $epic->status,
                'key' => $epic->key,
                'order' => $epic->order,
                'start_date' => $epic->start_date,
                'end_date' => $epic->end_date,
                'progress_percent' => $epic->progress_percent
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating epic: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false, 
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function createBacklog(Request $request) {
        $projId = $request->projectId;
        $backlogNum = Backlogs::where('project_id', $projId)
        ->where('epic_id', $request->epicId)
        ->count() + 1;
        $projectKey = Project::where('id', $projId)->value('key');
        $epic = Epics::where('id', $request->epicId)->first();
        $creator = ProjectMembers::where('user_id', Auth::user()->id)->value('id');
        $backlog = Backlogs::create([
            'title' => $request->title,
            'project_id' => $projId,
            'key' => $projectKey . '-' . $backlogNum,
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
        try {
            $backlog = Backlogs::where('id', $request->id)->first();
            $backlog->status = $request->status;
            $backlog->save();
            
            $epic = Epics::where('id', $backlog->epic_id)->first();

            $totalBacklogs = Backlogs::where('epic_id', $backlog->epic_id)->count();
            $doneBacklogs = Backlogs::where('epic_id', $backlog->epic_id)
                ->where('status', 'Done')
                ->count();

            $newProgressPercent = $totalBacklogs > 0 ? round(($doneBacklogs / $totalBacklogs) * 100) : 0;

            $epic->progress_percent = $newProgressPercent;
            $epic->save();

            $this->registerUpdate(
                $request->projectId, 
                Auth::user()->id, 
                "updated status of " . $backlog->title . " to " . $request->status . " in ", 
                $epic->name
            );
            
            return response()->json([
                'success' => true,
                'progress_percent' => $newProgressPercent
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating backlog status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating backlog status'
            ], 500);
        }
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

    public function completeSprint(Request $request) {
        $sprint = Sprints::where('epic_id', $request->epicId)
            ->where('status', 'Active')
            ->first();

        if ($sprint) {
            $sprint->status = 'Completed';
            $sprint->save();

            $epic = Epics::where('id', $request->epicId)->first();
            $this->registerUpdate(
                $request->projectId,
                Auth::user()->id,
                "completed sprint for ",
                $epic->name
            );

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Sprint not found'], 404);
    }

    public function updateBacklog(Request $request) {
        $backlog = Backlogs::where('id', $request->id)->first();
        
        if ($backlog) {
            $backlog->update([
                'title' => $request->title,
                'description' => $request->description,
                'type' => $request->type,
                'priority' => $request->priority,
                'status' => $request->status
            ]);

            $epic = Epics::where('id', $request->epicId)->first();
            $this->registerUpdate(
                $request->projectId,
                Auth::user()->id,
                "updated task " . $request->title . " in ",
                $epic->name
            );

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Task not found'], 404);
    }

    public function uploadAttachment(Request $request) {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max file size
            'id' => 'required|exists:backlogs,id',
            'projectId' => 'required|exists:projects,id'
        ]);

        try {
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = $file->getClientOriginalName();
                
                // Store file in public storage
                $filePath = Storage::disk('public')->put('attachments', $file);

                // Create attachment record
                $taskAttachment = new TaskAttachments();
                $taskAttachment->task_id = $request->id;
                $taskAttachment->file_path = $filePath;
                $taskAttachment->file_name = $fileName;
                $taskAttachment->file_size = $file->getSize();
                $taskAttachment->file_type = $file->getMimeType();
                $taskAttachment->save();

                // Log activity
                $backlog = Backlogs::with('epic')->find($request->id);
                $this->registerUpdate(
                    $request->projectId,
                    Auth::user()->id,
                    "attached file " . $fileName . " to task in ",
                    $backlog->epic->name
                );

                // Return success response with file details
                return response()->json([
                    'success' => true,
                    'id' => $taskAttachment->id,
                    'file_path' => $filePath,
                    'name' => $fileName,
                    'size' => $file->getSize(),
                    'type' => $file->getMimeType(),
                    'message' => 'File uploaded successfully'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No file uploaded'
            ], 400);

        } catch (\Exception $e) {
            Log::error('File upload error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Error uploading file: ' . $e->getMessage()
            ], 500);
        }
    }

    public function addComment(Request $request)
    {
        $request->validate([
            'taskId' => 'required|exists:backlogs,id',
            'comment' => 'required|string',
            'projectId' => 'required|exists:projects,id'
        ]);

        try {
            $comment = TaskComments::create([
                'task_id' => $request->taskId,
                'user_id' => Auth::id(),
                'comment' => $request->comment
            ]);

            // Load the user relationship
            $comment->load('user');

            $backlog = Backlogs::with('epic')->find($request->taskId);
            $this->registerUpdate(
                $request->projectId,
                Auth::user()->id,
                "commented on task " . $backlog->title . " in ",
                $backlog->epic->name
            );

            return response()->json([
                'success' => true,
                'comment' => $comment
            ]);
        } catch (\Exception $e) {
            Log::error('Error adding comment: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error adding comment'
            ], 500);
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

    public function updateTaskAssignees(Request $request)
    {
        $request->validate([
            'taskId' => 'required|exists:backlogs,id',
            'assignees' => 'required|array',
            'projectId' => 'required|exists:projects,id'
        ]);

        try {
            $backlog = Backlogs::with('epic')->find($request->taskId);
            
            // Clear existing assignees
            DB::table('task_assignees')->where('task_id', $request->taskId)->delete();
            
            // Add new assignees
            foreach ($request->assignees as $assigneeId) {
                DB::table('task_assignees')->insert([
                    'task_id' => $request->taskId,
                    'user_id' => $assigneeId,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            // Get updated assignees with correct data structure
            $updatedAssignees = User::whereIn('id', $request->assignees)
                ->select('id', 'name', 'avatar')
                ->get();

            // Log activity
            $this->registerUpdate(
                $request->projectId,
                Auth::user()->id,
                "updated assignees for task " . $backlog->title . " in ",
                $backlog->epic->name
            );

            return response()->json([
                'success' => true,
                'assignees' => $updatedAssignees,
                'message' => 'Assignees updated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating assignees: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating assignees'
            ], 500);
        }
    }

    public function updateEpic(Request $request)
    {
        try {
            $request->validate([
                'epicId' => 'required|exists:epics,id',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'projectId' => 'required|exists:projects,id'
            ]);

            $epic = Epics::findOrFail($request->epicId);
            
            // Check if the epic belongs to the project
            if ($epic->project_id != $request->projectId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Epic does not belong to this project'
                ], 403);
            }

            // Update the epic with all fields
            $epic->update([
                'name' => $request->name,
                'description' => $request->description ?? '',
                'start_date' => $request->start_date,
                'end_date' => $request->end_date
            ]);

            $this->registerUpdate(
                $request->projectId,
                Auth::user()->id,
                "updated epic ",
                $epic->name
            );

            // Return the updated epic
            return response()->json([
                'success' => true,
                'message' => 'Epic updated successfully',
                'epic' => $epic->fresh(),
                'start_date' => $epic->start_date,
                'end_date' => $epic->end_date
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error updating epic: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the epic'
            ], 500);
        }
    }

    public function deleteEpic(Request $request)
    {
        $request->validate([
            'epicId' => 'required|exists:epics,id',
            'projectId' => 'required|exists:projects,id'
        ]);

        try {
            DB::beginTransaction();
            
            $epic = Epics::findOrFail($request->epicId);
            
            // Get all backlog IDs associated with this epic
            $backlogIds = Backlogs::where('epic_id', $epic->id)->pluck('id')->toArray();
            
            if (!empty($backlogIds)) {
                // Delete task comments
                TaskComments::whereIn('task_id', $backlogIds)->delete();
                
                // Delete task attachments
                TaskAttachments::whereIn('task_id', $backlogIds)->delete();
                
                // Delete task assignees
                DB::table('task_assignees')->whereIn('task_id', $backlogIds)->delete();
                
                // Delete backlogs
                Backlogs::whereIn('id', $backlogIds)->delete();
            }
            
            // Delete any active sprints associated with this epic
            Sprints::where('epic_id', $epic->id)->delete();
            
            // Finally, delete the epic
            $epic->delete();
            
            DB::commit();

            $this->registerUpdate(
                $request->projectId,
                Auth::user()->id,
                "deleted epic ",
                $epic->name
            );

            return response()->json([
                'success' => true,
                'message' => 'Epic deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting epic: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting epic: ' . $e->getMessage()
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

            // Delete the column
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

    public function moveTasks(Request $request)
    {
        $request->validate([
            'taskIds' => 'required|array',
            'taskIds.*' => 'exists:backlogs,id',
            'targetEpicId' => 'required|exists:epics,id',
            'sourceEpicId' => 'required|exists:epics,id',
            'projectId' => 'required|exists:projects,id'
        ]);

        try {
            DB::beginTransaction();

            // Update all tasks with new epic_id
            Backlogs::whereIn('id', $request->taskIds)
                ->update(['epic_id' => $request->targetEpicId]);

            // Get epic names for logging
            $sourceEpic = Epics::find($request->sourceEpicId);
            $targetEpic = Epics::find($request->targetEpicId);
            
            // Log the move action
            $taskCount = count($request->taskIds);
            $actionDesc = $taskCount > 1 
                ? "moved {$taskCount} tasks from {$sourceEpic->name} to "
                : "moved a task from {$sourceEpic->name} to ";
            
            $this->registerUpdate(
                $request->projectId,
                Auth::user()->id,
                $actionDesc,
                $targetEpic->name
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tasks moved successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error moving tasks: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error moving tasks: ' . $e->getMessage()
            ], 500);
        }
    }
}

