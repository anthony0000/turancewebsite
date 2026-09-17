<?php

namespace App\Console\Commands;

use App\Models\ProjectFile;
use App\Models\StaffContract;
use App\Support\PersistentUploadStorage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Throwable;

class MigratePersistentUploads extends Command
{
    protected $signature = 'uploads:migrate-persistent';

    protected $description = 'Move existing project and signed-contract uploads into persistent storage';

    public function handle(): int
    {
        try {
            $paths = ProjectFile::query()
                ->whereNotNull('path')
                ->pluck('path')
                ->merge(StaffContract::query()->whereNotNull('signed_document_path')->pluck('signed_document_path'))
                ->filter()
                ->map(fn (string $path) => ltrim($path, '/\\'))
                ->unique()
                ->values();
        } catch (Throwable $exception) {
            $this->components->error('Upload records could not be read from the database: '.$exception->getMessage());

            return self::FAILURE;
        }

        $migrated = 0;
        $alreadyPersistent = 0;
        $missing = 0;
        $failed = 0;
        try {
            $disk = Storage::disk(PersistentUploadStorage::DISK);
            $legacyDisk = Storage::disk(PersistentUploadStorage::LEGACY_DISK);
        } catch (Throwable $exception) {
            $this->components->error('Persistent upload storage is unavailable: '.$exception->getMessage());

            return self::FAILURE;
        }

        foreach ($paths as $path) {
            if ($disk->exists($path)) {
                $alreadyPersistent++;

                continue;
            }

            if (! $legacyDisk->exists($path)) {
                $missing++;

                continue;
            }

            if (PersistentUploadStorage::migrateFromLegacy($path)) {
                $migrated++;
            } else {
                $failed++;
            }
        }

        $this->components->info("Persistent upload migration complete: {$migrated} moved, {$alreadyPersistent} already stored.");

        if ($missing > 0) {
            $this->components->warn("{$missing} database records reference files that were not found on either disk.");
        }

        if ($failed > 0) {
            $this->components->error("{$failed} files could not be moved. The legacy copies were retained.");

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
