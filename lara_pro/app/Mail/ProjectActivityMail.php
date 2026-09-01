<?php

namespace App\Mail;

use App\Models\Project;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class ProjectActivityMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $recipient,
        public Project $project,
        public string $type,
        public string $message,
        public string $url,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: Str::headline(str_replace(['.', '_'], ' ', $this->type)).' · '.$this->project->name,
        );
    }

    public function headers(): Headers
    {
        return new Headers(text: [
            'Auto-Submitted' => 'auto-generated',
            'X-Auto-Response-Suppress' => 'All',
        ]);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.project-activity',
            text: 'emails.project-activity-text',
            with: [
                'recipientName' => $this->recipient->name,
                'project' => $this->project,
                'activityLabel' => Str::headline(str_replace(['.', '_'], ' ', $this->type)),
                'activityMessage' => $this->message,
                'url' => $this->url,
                'sentAt' => now(),
            ],
        );
    }
}
