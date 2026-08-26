<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectFileContent extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'project_file_id',
        'contents',
    ];

    protected $hidden = [
        'contents',
    ];

    public function projectFile(): BelongsTo
    {
        return $this->belongsTo(ProjectFile::class);
    }
}
