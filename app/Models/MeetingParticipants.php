<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeetingParticipants extends Model
{
    /** @use HasFactory<\Database\Factories\MeetingParticipantsFactory> */
    use HasFactory;
    protected $fillable = [
        'meeting_id',
        'user_id'
    ];
}
