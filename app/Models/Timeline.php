<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Timeline extends Model
{
    protected $fillable = [
        'type',
        'text',
        'description',
        'project_id',
        'user_id',
        'details'
    ];

    protected $casts = [
        'details' => 'array'
    ];

    // Relationship with User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship with Project model 
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
