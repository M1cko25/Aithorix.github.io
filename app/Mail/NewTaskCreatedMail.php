<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Backlogs;
use App\Models\User;

class NewTaskCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $task;
    public $createdBy;
    public $projectName;
    public $epicName;

    /**
     * Create a new message instance.
     */
    public function __construct(Backlogs $task, User $createdBy, string $projectName, string $epicName)
    {
        $this->task = $task;
        $this->createdBy = $createdBy;
        $this->projectName = $projectName;
        $this->epicName = $epicName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "New Task Created in {$this->projectName}: {$this->task->title}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.new-task-created',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
