<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Backlogs extends Model
{
    /** @use HasFactory<\Database\Factories\BacklogsFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'key',
        'description',
        'type',
        'status',
        'priority',
        'project_id',
        'epic_id',
        'creator_id',
        'order'
    ];

    public static function generateKey($projectId)
    {
        $project = Project::findOrFail($projectId);

        // Get all existing numbers for this project
        $existingNumbers = self::where('project_id', $projectId)
            ->pluck('key')
            ->map(function($key) {
                $parts = explode('-', $key);
                return isset($parts[1]) ? intval($parts[1]) : 0;
            })
            ->toArray();

        // If no existing numbers, start with 1
        if (empty($existingNumbers)) {
            return $project->key . '-1';
        }

        // Sort numbers to find gaps
        sort($existingNumbers);
        $maxNumber = max($existingNumbers);

        // Find first gap or use max+1
        $nextNumber = 1;
        for ($i = 1; $i <= $maxNumber + 1; $i++) {
            if (!in_array($i, $existingNumbers)) {
                $nextNumber = $i;
                break;
            }
        }

        return $project->key . '-' . $nextNumber;
    }

    public function attachments()
    {
        return $this->hasMany(TaskAttachments::class, 'task_id');
    }

    public function comments()
    {
        return $this->hasMany(TaskComments::class, 'task_id');
    }

    public function epic()
    {
        return $this->belongsTo(Epics::class, 'epic_id');
    }

    public function assignees()
    {
        return $this->belongsToMany(User::class, 'task_assignees', 'task_id', 'user_id')
            ->select(['users.id', 'name', 'avatar']);
    }
}
