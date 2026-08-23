<?php

return [
    'enabled' => (bool) env('DATABASE_BACKUP_ENABLED', false),

    'recipient' => [
        'address' => env(
            'DATABASE_BACKUP_RECIPIENT_ADDRESS',
            env('CONTACT_RECIPIENT_ADDRESS', env('MAIL_FROM_ADDRESS')),
        ),
        'name' => env(
            'DATABASE_BACKUP_RECIPIENT_NAME',
            env('CONTACT_RECIPIENT_NAME', env('MAIL_FROM_NAME', 'Company mailbox')),
        ),
    ],

    'binary' => env('DATABASE_BACKUP_BINARY', 'mysqldump'),
    'timeout' => (int) env('DATABASE_BACKUP_TIMEOUT', 300),
    'max_attachment_mb' => (int) env('DATABASE_BACKUP_MAX_ATTACHMENT_MB', 20),
    'interval_hours' => (int) env('DATABASE_BACKUP_INTERVAL_HOURS', 48),
    'last_success_cache_key' => env('DATABASE_BACKUP_LAST_SUCCESS_CACHE_KEY', 'database-backup:last-success-at'),
    'path' => env('DATABASE_BACKUP_PATH', storage_path('app/private/backups')),
    'timezone' => env('DATABASE_BACKUP_TIMEZONE', env('APP_TIMEZONE', 'Africa/Lagos')),
];
