<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Epics extends Model
{
    /** @use HasFactory<\Database\Factories\EpicsFactory> */
    use HasFactory;
    protected $fillable = [
        'project_id',
        'name',
        'description',
        'status',
        'progress_percent',
        'key',
        'order',
    ];
}
