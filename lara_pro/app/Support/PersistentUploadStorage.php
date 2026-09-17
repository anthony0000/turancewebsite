<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use Throwable;

final class PersistentUploadStorage
{
    public const DISK = 'public_uploads';

    public const LEGACY_DISK = 'legacy_public_uploads';

    public static function exists(?string $path): bool
    {
        $path = self::normalizePath($path);

        if ($path === null) {
            return false;
        }

        try {
            if (Storage::disk(self::DISK)->exists($path)) {
                return true;
            }
        } catch (Throwable) {
            // Keep legacy files readable while a persistent mount is being repaired.
        }

        try {
            return Storage::disk(self::LEGACY_DISK)->exists($path);
        } catch (Throwable) {
            return false;
        }
    }

    public static function absolutePath(?string $path): ?string
    {
        $path = self::normalizePath($path);

        if ($path === null) {
            return null;
        }

        try {
            $disk = Storage::disk(self::DISK);

            if ($disk->exists($path)) {
                return $disk->path($path);
            }
        } catch (Throwable) {
            $disk = null;
        }

        if ($disk !== null && self::migrateFromLegacy($path) && $disk->exists($path)) {
            return $disk->path($path);
        }

        try {
            $legacyDisk = Storage::disk(self::LEGACY_DISK);

            return $legacyDisk->exists($path) ? $legacyDisk->path($path) : null;
        } catch (Throwable) {
            return null;
        }
    }

    public static function migrateFromLegacy(string $path): bool
    {
        $path = self::normalizePath($path);

        if ($path === null) {
            return false;
        }

        $disk = null;
        $stream = null;
        try {
            $disk = Storage::disk(self::DISK);

            if ($disk->exists($path)) {
                return true;
            }

            $legacyDisk = Storage::disk(self::LEGACY_DISK);

            if (! $legacyDisk->exists($path)) {
                return false;
            }

            $stream = $legacyDisk->readStream($path);

            if (! is_resource($stream)) {
                return false;
            }

            $written = $disk->writeStream($path, $stream);
            fclose($stream);
            $stream = null;

            if (! $written || ! $disk->exists($path) || $disk->size($path) !== $legacyDisk->size($path)) {
                $disk->delete($path);

                return false;
            }

            $legacyDisk->delete($path);

            return true;
        } catch (Throwable) {
            if (is_resource($stream)) {
                fclose($stream);
            }

            if ($disk !== null) {
                try {
                    $disk->delete($path);
                } catch (Throwable) {
                    // The verified legacy copy remains available.
                }
            }

            return false;
        }
    }

    public static function delete(?string $path): void
    {
        $path = self::normalizePath($path);

        if ($path === null) {
            return;
        }

        try {
            $disk = Storage::disk(self::DISK);

            if ($disk->exists($path)) {
                $disk->delete($path);

                return;
            }
        } catch (Throwable) {
            // Try the legacy location below.
        }

        try {
            Storage::disk(self::LEGACY_DISK)->delete($path);
        } catch (Throwable) {
            // Storage cleanup must not mask the original application operation.
        }
    }

    private static function normalizePath(?string $path): ?string
    {
        $path = ltrim(trim((string) $path), '/\\');

        return $path !== '' ? $path : null;
    }
}
