<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DatabaseBackupReady extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $backupPath,
        public string $backupFilename,
        public string $databaseName,
        public string $generatedAt,
        public int $backupSize,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Database backup: '.$this->backupFilename,
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
            view: 'emails.database-backup-ready',
            text: 'emails.database-backup-ready-text',
            with: [
                'backupFilename' => $this->backupFilename,
                'databaseName' => $this->databaseName,
                'generatedAt' => $this->generatedAt,
                'backupSize' => $this->backupSize,
            ],
        );
    }

    /** @return array<int, Attachment> */
    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->backupPath)
                ->as($this->backupFilename)
                ->withMime('application/gzip'),
        ];
    }
}
