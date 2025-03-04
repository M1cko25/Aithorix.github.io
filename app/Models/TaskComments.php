<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskComments extends Model
{
    /** @use HasFactory<\Database\Factories\TaskCommentsFactory> */
    use HasFactory;

    protected $fillable = [
        'task_id',
        'user_id',
        'comment'
    ];

    protected $with = ['user'];

    public function task()
    {
        return $this->belongsTo(Backlogs::class, 'task_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
