<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectCredential extends Model
{
    protected $fillable = [
        'project_id',
        'service_name',
        'credential_type',
        'access_url',
        'username',
        'secret',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'username' => 'encrypted',
        'secret' => 'encrypted',
        'notes' => 'encrypted',
    ];

    protected $hidden = [
        'username',
        'secret',
        'notes',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
