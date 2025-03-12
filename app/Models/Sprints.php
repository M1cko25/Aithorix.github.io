<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sprints extends Model
{
    /** @use HasFactory<\Database\Factories\SprintsFactory> */
    use HasFactory;
    protected $fillable = [
        'name',
        'epic_id',
        'start_date',
        'end_date',
        'status',
        'description',
        'updated_at',
    ];
}
