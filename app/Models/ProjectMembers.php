<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProjectMembers extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectMembersFactory> */
    use HasFactory;
    protected $fillable = [
        'project_id',
        'user_id',
        'role'
    ];
    public function projectId() {
        return $this->belongsTo(Project::class, 'project_id');
    }
}
