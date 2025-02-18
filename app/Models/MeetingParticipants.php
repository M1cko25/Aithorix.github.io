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
        'user_id',
        'status'
    ];

    public function meeting() {
        return $this->belongsTo(Meetings::class, 'meeting_id');
    }
    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
