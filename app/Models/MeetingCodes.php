<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeetingCodes extends Model
{
    /** @use HasFactory<\Database\Factories\MeetingCodesFactory> */
    use HasFactory;
    protected $fillable = [
        'meeting_name',
        'code',
        'created_by',
        'expires_at',
        'project_id',
    ];

    /**
     * Generate a unique meeting code in the format CCCC-CCCC
     * where C is alphanumeric (A-Z, 0-9)
     */
    public static function generateUniqueCode()
    {
        do {
            // Generate first part (CCCC)
            $part1 = strtoupper(substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 4));
            // Generate second part (CCCC)
            $part2 = strtoupper(substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 4));
            // Combine with hyphen
            $code = $part1 . '-' . $part2;
        } while (self::where('code', $code)->exists()); // Ensure code is unique

        return $code;
    }

    /**
     * Get the full room name for Daily.co (meeting name + code)
     */
    public function getFullRoomName()
    {
        return $this->meeting_name . '-' . str_replace('-', '', $this->code);
    }
}
