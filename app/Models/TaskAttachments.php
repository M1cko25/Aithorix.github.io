<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskAttachments extends Model
{
    /** @use HasFactory<\Database\Factories\TaskAttachmentsFactory> */
    use HasFactory;

    protected $table = 'task_attachments';

    protected $fillable = [
        'task_id',
        'file_path',
        'file_name',
        'file_size',
        'file_type'
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    public function task()
    {
        return $this->belongsTo(Backlogs::class, 'task_id');
    }
}
