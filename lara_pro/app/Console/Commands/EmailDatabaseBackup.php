<?php

namespace App\Console\Commands;

use App\Mail\DatabaseBackupReady;
use Illuminate\Console\Command;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use RuntimeException;
use Symfony\Component\Process\Process;
use Throwable;

class EmailDatabaseBackup extends Command
{
    protected $signature = 'db:backup
        {--force : Run the backup even when automatic backups are disabled}';

    protected $description = 'Create a compressed database backup and email it to the company mailbox';

    public function handle(): int
    {
        if (! config('database-backup.enabled') && ! $this->option('force')) {
            $this->components->warn('Database backups are disabled. Set DATABASE_BACKUP_ENABLED=true to enable them.');

            return self::SUCCESS;
        }

        if (! $this->option('force') && $this->backupWasSentRecently()) {
            $this->components->line('The 48-hour backup interval has not elapsed.');

            return self::SUCCESS;
        }

        $recipientAddress = trim((string) config('database-backup.recipient.address'));

        if ($recipientAddress === '') {
            $this->components->error('No database backup recipient is configured.');

            return self::FAILURE;
        }

        $backupPath = null;

        try {
            $backup = $this->createBackup();
            $backupPath = $backup['path'];

            Mail::to(new Address(
                $recipientAddress,
                (string) config('database-backup.recipient.name'),
            ))->send(new DatabaseBackupReady(
                backupPath: $backup['path'],
                backupFilename: $backup['filename'],
                databaseName: $backup['database'],
                generatedAt: $backup['generated_at'],
                backupSize: $backup['size'],
            ));

            Cache::forever(
                (string) config('database-backup.last_success_cache_key'),
                now((string) config('database-backup.timezone'))->toIso8601String(),
            );

            $this->components->info('Database backup emailed to '.$recipientAddress.'.');

            return self::SUCCESS;
        } catch (Throwable $exception) {
            report($exception);
            $this->components->error('Database backup failed: '.Str::limit($exception->getMessage(), 240));

            return self::FAILURE;
        } finally {
            if ($backupPath && is_file($backupPath)) {
                @unlink($backupPath);
            }
        }
    }

    private function backupWasSentRecently(): bool
    {
        $lastSuccess = Cache::get((string) config('database-backup.last_success_cache_key'));

        if (! $lastSuccess) {
            return false;
        }

        try {
            $lastSuccessAt = Carbon::parse((string) $lastSuccess);
        } catch (Throwable) {
            return false;
        }

        return now((string) config('database-backup.timezone'))
            ->diffInSeconds($lastSuccessAt) < max(1, (int) config('database-backup.interval_hours', 48)) * 3600;
    }

    /**
     * @return array{path: string, filename: string, database: string, generated_at: string, size: int}
     */
    private function createBackup(): array
    {
        $connectionName = (string) config('database.default');
        $connection = (array) config('database.connections.'.$connectionName, []);
        $driver = (string) ($connection['driver'] ?? '');

        if (! in_array($driver, ['mysql', 'mariadb'], true)) {
            throw new RuntimeException('Database backups currently support MySQL and MariaDB connections only.');
        }

        $database = trim((string) ($connection['database'] ?? ''));
        $username = (string) ($connection['username'] ?? '');
        $password = (string) ($connection['password'] ?? '');
        $host = (string) ($connection['host'] ?? '127.0.0.1');
        $port = (string) ($connection['port'] ?? 3306);
        $directory = (string) config('database-backup.path');

        if ($database === '' || $username === '') {
            throw new RuntimeException('Database name and username must be configured before creating a backup.');
        }

        if (! is_dir($directory) && ! @mkdir($directory, 0700, true) && ! is_dir($directory)) {
            throw new RuntimeException('The database backup directory could not be created.');
        }

        if (! is_writable($directory)) {
            throw new RuntimeException('The database backup directory is not writable.');
        }

        $generatedAt = now((string) config('database-backup.timezone'));
        $filename = sprintf(
            '%s-%s-%s.sql.gz',
            Str::slug((string) config('app.name', 'application')),
            Str::slug($database),
            $generatedAt->format('Ymd-His'),
        );
        $backupPath = rtrim($directory, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$filename;
        $output = @gzopen($backupPath, 'wb9');

        if ($output === false) {
            throw new RuntimeException('The compressed database backup file could not be opened.');
        }

        $command = [
            (string) config('database-backup.binary', 'mysqldump'),
            '--host='.$host,
            '--port='.$port,
            '--user='.$username,
            '--single-transaction',
            '--quick',
            '--routines',
            '--events',
            '--triggers',
            '--no-tablespaces',
            $database,
        ];
        $environment = $password === '' ? [] : ['MYSQL_PWD' => $password];
        $process = new Process(
            $command,
            base_path(),
            $environment,
            null,
            max(10, (int) config('database-backup.timeout', 300)),
        );
        $errorOutput = '';

        try {
            $process->run(function (string $type, string $buffer) use ($output, &$errorOutput): void {
                if ($type === Process::OUT) {
                    gzwrite($output, $buffer);

                    return;
                }

                $errorOutput = Str::limit($errorOutput.$buffer, 4000, '');
            });
        } finally {
            gzclose($output);
        }

        if (! $process->isSuccessful()) {
            @unlink($backupPath);

            throw new RuntimeException(
                'The database dump command failed.'.($errorOutput !== '' ? ' '.$errorOutput : '')
            );
        }

        $size = (int) filesize($backupPath);
        $maximumBytes = max(1, (int) config('database-backup.max_attachment_mb', 20)) * 1024 * 1024;

        if ($size <= 0) {
            @unlink($backupPath);

            throw new RuntimeException('The database dump produced an empty backup.');
        }

        if ($size > $maximumBytes) {
            @unlink($backupPath);

            throw new RuntimeException('The compressed backup exceeds the configured email attachment limit.');
        }

        return [
            'path' => $backupPath,
            'filename' => $filename,
            'database' => $database,
            'generated_at' => $generatedAt->toDateTimeString(),
            'size' => $size,
        ];
    }
}
