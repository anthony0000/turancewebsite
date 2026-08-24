<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $fillable = [
        'project_number',
        'name',
        'client_name',
        'client_company',
        'status',
        'starts_on',
        'ends_on',
        'description',
        'client_id',
        'project_brief',
        'project_manager_id',
        'priority',
        'budget',
        'estimated_hours',
        'progress_mode',
        'progress_override',
        'archived_at',
        'completed_at',
    ];

    protected $casts = [
        'starts_on' => 'date',
        'ends_on' => 'date',
        'budget' => 'decimal:2',
        'estimated_hours' => 'decimal:2',
        'progress_override' => 'integer',
        'archived_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function staffContracts(): HasMany
    {
        return $this->hasMany(StaffContract::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(ProjectFile::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function projectManager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'project_manager_id');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_members')->withPivot('role')->withTimestamps();
    }

    public function boardColumns(): HasMany
    {
        return $this->hasMany(BoardColumn::class)->orderBy('position');
    }

    public function labels(): HasMany
    {
        return $this->hasMany(Label::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(Milestone::class);
    }

    public function sprints(): HasMany
    {
        return $this->hasMany(Sprint::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(ProjectAttachment::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function getProgressPercentageAttribute(): int
    {
        if ($this->progress_mode === 'manual' && $this->progress_override !== null) {
            return (int) $this->progress_override;
        }

        $total = array_key_exists('tasks_count', $this->attributes)
            ? (int) $this->attributes['tasks_count']
            : ($this->relationLoaded('tasks')
            ? $this->tasks->whereNull('archived_at')->count()
            : $this->tasks()->whereNull('archived_at')->count());
        $completed = array_key_exists('completed_tasks_count', $this->attributes)
            ? (int) $this->attributes['completed_tasks_count']
            : ($this->relationLoaded('tasks')
            ? $this->tasks->whereNull('archived_at')->whereNotNull('completed_at')->count()
            : $this->tasks()->whereNull('archived_at')->whereNotNull('completed_at')->count());

        return $total > 0 ? (int) round(($completed / $total) * 100) : 0;
    }
}
