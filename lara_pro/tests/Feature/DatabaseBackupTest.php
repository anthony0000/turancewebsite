<?php

use App\Mail\DatabaseBackupReady;
use Illuminate\Support\Facades\Mail;

it('keeps automatic database backups disabled until explicitly enabled', function () {
    Mail::fake();
    config()->set('database-backup.enabled', false);

    $this->artisan('db:backup')->assertExitCode(0);

    Mail::assertNothingSent();
});

it('builds a compressed backup email with the archive attached', function () {
    $backupPath = tempnam(storage_path('framework'), 'database-backup-');
    file_put_contents($backupPath, 'compressed backup placeholder');

    $mail = new DatabaseBackupReady(
        backupPath: $backupPath,
        backupFilename: 'turancetech-20260823-120000.sql.gz',
        databaseName: 'turancetech',
        generatedAt: '2026-08-23 12:00:00',
        backupSize: filesize($backupPath),
    );

    expect($mail->attachments())->toHaveCount(1);
    expect($mail->envelope()->subject)->toBe('Database backup: turancetech-20260823-120000.sql.gz');

    @unlink($backupPath);
});
