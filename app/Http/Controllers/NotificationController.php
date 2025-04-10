<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notifications;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Get all notifications for the authenticated user
     */
    public function index()
    {
        $notifications = Notifications::where('receiver_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($notifications);
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead($id)
    {
        $notification = Notifications::where('id', $id)
            ->where('receiver_id', Auth::id())
            ->first();

        if ($notification) {
            $notification->update(['is_read' => true]);
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }

    /**
     * Mark all notifications as read for the authenticated user
     */
    public function markAllAsRead()
    {
        Notifications::where('receiver_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * Delete a notification
     */
    public function destroy($id)
    {
        $notification = Notifications::where('id', $id)
            ->where('receiver_id', Auth::id())
            ->first();

        if ($notification) {
            $notification->delete();
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }

    /**
     * Create a new task assignment notification
     */
    public function createTaskAssignedNotification($userId, $taskId, $taskTitle, $assignedByName)
    {
        $content = "{$assignedByName} assigned a task for you. <a href='#' class='text-blue-600 hover:underline'>Click this to see</a>";

        return Notifications::create([
            'title' => 'New task assigned to you',
            'content' => $content,
            'sender_id' => Auth::id(),
            'receiver_id' => $userId,
            'is_read' => false,
            'type' => 'task_assigned',
        ]);
    }

    /**
     * Create a task due notification
     */
    public function createTaskDueNotification($userId, $taskId, $taskTitle)
    {
        $content = "{$taskTitle} is due tomorrow. <a href='#' class='text-blue-600 hover:underline'>Click this to see</a>";

        return Notifications::create([
            'title' => 'Task due tomorrow',
            'content' => $content,
            'sender_id' => Auth::id(),
            'receiver_id' => $userId,
            'is_read' => false,
            'type' => 'task_due',
        ]);
    }

    /**
     * Create a task comment notification
     */
    public function createTaskCommentNotification($userId, $taskId, $taskTitle, $commenterName)
    {
        $content = "{$commenterName} commented on your work. <a href='#' class='text-blue-600 hover:underline'>Click this to see</a>";

        return Notifications::create([
            'title' => 'New comment about your work',
            'content' => $content,
            'sender_id' => Auth::id(),
            'receiver_id' => $userId,
            'is_read' => false,
            'type' => 'task_comment',
        ]);
    }
}
