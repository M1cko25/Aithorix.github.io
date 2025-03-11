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

class EpicController extends Controller
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
}
