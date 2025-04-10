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
use App\Models\TaskAssignees;
use App\Services\TaskEmailService;
use App\Models\Notifications;


class TaskController extends Controller
{
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

    public function updateTaskAssignees(Request $request)
    {
        $request->validate([
            'taskId' => 'required|exists:backlogs,id',
            'assignees' => 'required|array',
            'projectId' => 'required|exists:projects,id'
        ]);

        try {
            $backlog = Backlogs::with('epic')->find($request->taskId);

            // Begin transaction
            DB::beginTransaction();

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

                // Send email notification to the assignee
                $user = User::find($assigneeId);

                // Send email notification
                TaskEmailService::sendTaskAssignedEmail($backlog, $user);

                // Create in-app notification for task assignment
                $this->createTaskAssignedNotification($assigneeId, $request->taskId, $backlog->title, Auth::user()->name);
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

            DB::commit();

            return response()->json([
                'success' => true,
                'assignees' => $updatedAssignees,
                'message' => 'Assignees updated successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating assignees: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating assignees: ' . $e->getMessage()
            ], 500);
        }
    }

    public function createBacklog(Request $request) {
        try {
            $projId = $request->projectId;
            $projKey = Backlogs::generateKey($projId);
            $epic = Epics::where('id', $request->epicId)->first();
            $creator = ProjectMembers::where('user_id', Auth::user()->id)->value('id');

            $createdBacklog = Backlogs::create([
                'title' => $request->title,
                'project_id' => $projId,
                'key' => $projKey,
                'type' => $request->type,
                'description' => '',
                'priority' => $request->priority ?? 'Low',
                'epic_id' => $request->epicId,
                'creator_id' => $creator,
                'status' => 'To Do',
                'order' => $request->order ?? 1,
            ]);

            $backlog = Backlogs::with(['attachments', 'assignees'])
                ->where('id', $createdBacklog->id)
                ->first();

            if ($epic->status == "On Sprint") {
                $startDate = new \DateTime($epic->start_date);
                $endDate = new \DateTime($epic->end_date);
                $duration = $startDate->diff($endDate)->days;

                $sprint = SprintTasks::create([
                    'sprint_id' => $epic->id,
                    'backlog_id' => $backlog->id,
                    'start_date' => $epic->start_date,
                    'end_date' => $epic->end_date,
                    'duration' => $duration,
                    'progress' => 0,
                ]);
            }

            $this->registerUpdate($projId, Auth::user()->id, "created " . $request->title . " in ", $epic->name);

            // Send emails to project members about new task
            TaskEmailService::sendNewTaskCreatedEmail($backlog);

            return response()->json(['success' => true, 'backlog' => $backlog]);
        } catch (\Exception $e) {
            Log::error('Error creating backlog: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error creating backlog: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteBacklog(Request $request) {
        try {
            DB::beginTransaction();
        $backlog = Backlogs::where('id', $request->id)->first();
            if (!$backlog) {
                throw new \Exception('Backlog not found');
            }
            SprintTasks::where('backlog_id', $backlog->id)->delete();
            TaskComments::where('task_id', $backlog->id)->delete();
            TaskAttachments::where('task_id', $backlog->id)->delete();
            DB::table('task_assignees')->where('task_id', $backlog->id)->delete();
            $backlog->delete();
            $this->registerUpdate(
                $request->projectId,
                Auth::user()->id,
                "deleted task " . $request->title . " in ",
                Epics::find($request->epicId)->name
            );

            DB::commit();
            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting backlog: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting task: ' . $e->getMessage()
            ], 500);
        }
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

    public function deleteAttachment(Request $request)
    {
        $request->validate([
            'attachmentId' => 'required|exists:task_attachments,id',
            'taskId' => 'required|exists:backlogs,id',
            'projectId' => 'required|exists:projects,id'
        ]);

        try {
            $attachment = TaskAttachments::findOrFail($request->attachmentId);

            // Check if attachment belongs to the task
            if ($attachment->task_id != $request->taskId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Attachment does not belong to this task'
                ], 403);
            }

            // Delete the file from storage
            if (Storage::disk('public')->exists($attachment->file_path)) {
                Storage::disk('public')->delete($attachment->file_path);
            }

            // Delete the attachment record
            $attachment->delete();

            // Log activity
            $backlog = Backlogs::with('epic')->find($request->taskId);
            $this->registerUpdate(
                $request->projectId,
                Auth::user()->id,
                "deleted attachment " . $attachment->file_name . " from task in ",
                $backlog->epic->name
            );

            return response()->json([
                'success' => true,
                'message' => 'Attachment deleted successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting attachment: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting attachment'
            ], 500);
        }
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

    public function updateBacklogStatus(Request $request) {
        try {
        $backlog = Backlogs::where('id', $request->id)->first();
        $backlog->status = $request->status;
        $backlog->save();

        $epic = Epics::where('id', $backlog->epic_id)->first();

            $totalBacklogs = Backlogs::where('epic_id', $backlog->epic_id)->count();
            $progressBacklogs = Backlogs::where('epic_id', $backlog->epic_id)
                ->where('status', 'In Progress')
                ->count();
            $doneBacklogs = Backlogs::where('epic_id', $backlog->epic_id)
                ->where('status', 'Done')
                ->count();

            $newProgressPercent = $totalBacklogs > 0 ? floor(((($progressBacklogs / 2) + $doneBacklogs) / $totalBacklogs) * 100) : 0;

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

    /**
     * Create a task assignment notification
     */
    private function createTaskAssignedNotification($userId, $taskId, $taskTitle, $assignedByName)
    {
        $content = "{$assignedByName} assigned a task for you. <a href='/scrum/board?id=" . Backlogs::find($taskId)->project_id . "' class='text-blue-600 hover:underline'>Click this to see</a>";

        return Notifications::create([
            'title' => 'New task assigned to you',
            'content' => $content,
            'sender_id' => Auth::id(),
            'receiver_id' => $userId,
            'is_read' => false,
            'type' => 'task_assigned',
        ]);
    }
}
