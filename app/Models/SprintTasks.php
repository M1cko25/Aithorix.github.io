<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SprintTasks extends Model
{
    /** @use HasFactory<\Database\Factories\SprintTasksFactory> */
    use HasFactory;
    protected $fillable = [
        'sprint_id',
        'backlog_id',
    ];
}
