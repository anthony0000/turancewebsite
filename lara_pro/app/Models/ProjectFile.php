<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ProjectFile extends Model
{
    protected $fillable = [
        'project_id',
        'uploaded_by',
        'original_name',
        'path',
        'mime_type',
        'size',
        'description',
        'share_token',
        'is_shared',
        'shared_at',
    ];

    protected function casts(): array
    {
        return [
            'size' => 'integer',
            'is_shared' => 'boolean',
            'shared_at' => 'datetime',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function hasStoredFile(): bool
    {
        return filled($this->path) && Storage::disk('project-files')->exists($this->path);
    }

    public function sizeLabel(): string
    {
        if (! $this->size) {
            return 'Size unavailable';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $size = (float) $this->size;
        $unit = 0;

        while ($size >= 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }

        return number_format($size, $unit === 0 ? 0 : 1).' '.$units[$unit];
    }

    public function fileKind(): string
    {
        return match (true) {
            $this->mime_type === 'application/pdf' => 'PDF',
            str_starts_with((string) $this->mime_type, 'image/') => 'Image',
            in_array($this->mime_type, [
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ], true) => 'Document',
            in_array($this->mime_type, [
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'text/csv',
            ], true) => 'Spreadsheet',
            default => 'File',
        };
    }
}
