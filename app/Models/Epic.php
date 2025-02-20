<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Epic extends Model
{
    /** @use HasFactory<\Database\Factories\EpicFactory> */
    use HasFactory;
    protected $fillable = [
        'project_id',
        'name',
        'description',
        'status',
        'progress_precent',
        'key',
        'order',
    ];
}
