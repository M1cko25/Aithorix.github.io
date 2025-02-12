<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Activity extends Model
{
    /** @use HasFactory<\Database\Factories\ActivityFactory> */
    use HasFactory;
    protected $fillable = [
        'description',
        'user_id',
        'project_id',
        'date',
        'update',
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
