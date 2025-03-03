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
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
