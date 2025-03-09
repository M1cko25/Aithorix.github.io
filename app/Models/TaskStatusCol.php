<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskStatusCol extends Model
{
    /** @use HasFactory<\Database\Factories\TaskStatusColFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'project_id'
    ];
}
