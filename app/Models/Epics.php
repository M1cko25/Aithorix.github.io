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
        'start_date',
        'end_date'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function backlogs()
    {
        return $this->hasMany(Backlogs::class, 'epic_id');
    }

    public function sprints()
    {
        return $this->hasMany(Sprints::class, 'epic_id');
    }
}
