<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeetingSummaries extends Model
{
    /** @use HasFactory<\Database\Factories\MeetingSummariesFactory> */
    use HasFactory;
    protected $fillable = [
        'meeting_id',
        'summary',
        'date',
    ];
}
