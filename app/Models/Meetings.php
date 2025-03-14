<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meetings extends Model
{
    /** @use HasFactory<\Database\Factories\MeetingsFactory> */
    use HasFactory;
    protected $fillable = [
        'name',
        'code_id',
        'status',
        'date',
        'start_time',
        'end_time',
        'project_id',
        'creator_id'
    ];
}
