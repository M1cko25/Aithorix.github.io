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
        'description',
        'type',
        'status',
        'priority',
        'project_id',
        'epic_id',
        'creator_id',
        'order'
    ];

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
