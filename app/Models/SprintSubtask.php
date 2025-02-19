<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SprintSubtask extends Model
{
    /** @use HasFactory<\Database\Factories\SprintSubtaskFactory> */
    use HasFactory;
    protected $fillable = [
        'task_id',
        'title',
        'description',
        'status',
    ];
}
