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
        'project_id',
        'creator_id',
        'assigned_to',
        'sprint_id',
        'sprint_order'
    ];
}
