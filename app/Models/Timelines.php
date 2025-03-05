<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timelines extends Model
{
    /** @use HasFactory<\Database\Factories\TimelinesFactory> */
    use HasFactory;

    protected $fillable = [
        'project_id',
        'user_id',
        'description',
        'subject',
        'status',
        'priority',
        'type',
    ];
};