<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProjectMembers extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectMembersFactory> */
    use HasFactory;
    protected $fillable = [
        'project_key',
        'user_id',
        'role'
    ];
}
