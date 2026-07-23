<?php

namespace App\Support;

use Illuminate\Support\Str;

final class DocumentBranding
{
    public static function logoSource(?string $path): ?string
    {
        $path = trim((string) $path);

        if ($path === '') {
            return null;
        }

        if (Str::startsWith($path, 'data:image/')) {
            return $path;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        $resolvedPath = self::resolvePath($path);

        if ($resolvedPath === null || ! is_readable($resolvedPath)) {
            return null;
        }

        $contents = file_get_contents($resolvedPath);

        if ($contents === false || $contents === '') {
            return null;
        }

        $mimeType = match (Str::lower(pathinfo($resolvedPath, PATHINFO_EXTENSION))) {
            'jpg', 'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            'webp' => 'image/webp',
            default => 'image/png',
        };

        return 'data:'.$mimeType.';base64,'.base64_encode($contents);
    }

    private static function resolvePath(string $path): ?string
    {
        if (preg_match('/^(?:[A-Za-z]:[\\\\\/]|\/)/', $path) === 1) {
            return is_file($path) ? $path : null;
        }

        foreach ([base_path($path), public_path($path)] as $candidate) {
            if (is_file($candidate)) {
                return $candidate;
            }
        }

        return null;
    }
}
