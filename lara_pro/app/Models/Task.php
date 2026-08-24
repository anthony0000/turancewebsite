<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    protected $fillable = [
        'project_id', 'board_column_id', 'reporter_id', 'assignee_id', 'milestone_id', 'sprint_id',
        'parent_task_id', 'task_number', 'title', 'description', 'type', 'status', 'priority',
        'starts_on', 'due_on', 'position', 'estimated_hours', 'story_points', 'completed_at', 'archived_at',
    ];

    protected $casts = [
        'starts_on' => 'date',
        'due_on' => 'date',
        'estimated_hours' => 'decimal:2',
        'story_points' => 'decimal:2',
        'completed_at' => 'datetime',
        'archived_at' => 'datetime',
    ];

    protected $appends = ['task_key', 'is_overdue'];

    public function project(): BelongsTo { return $this->belongsTo(Project::class); }
    public function column(): BelongsTo { return $this->belongsTo(BoardColumn::class, 'board_column_id'); }
    public function reporter(): BelongsTo { return $this->belongsTo(User::class, 'reporter_id'); }
    public function assignee(): BelongsTo { return $this->belongsTo(User::class, 'assignee_id'); }
    public function milestone(): BelongsTo { return $this->belongsTo(Milestone::class); }
    public function sprint(): BelongsTo { return $this->belongsTo(Sprint::class); }
    public function parent(): BelongsTo { return $this->belongsTo(Task::class, 'parent_task_id'); }
    public function subtasks(): HasMany { return $this->hasMany(Task::class, 'parent_task_id')->orderBy('position'); }
    public function labels(): BelongsToMany { return $this->belongsToMany(Label::class, 'task_labels')->withTimestamps(); }
    public function collaborators(): BelongsToMany { return $this->belongsToMany(User::class, 'task_collaborators')->withTimestamps(); }
    public function watchers(): BelongsToMany { return $this->belongsToMany(User::class, 'task_watchers')->withTimestamps(); }
    public function comments(): HasMany { return $this->hasMany(Comment::class)->latest(); }
    public function attachments(): HasMany { return $this->hasMany(ProjectAttachment::class)->latest(); }
    public function timeEntries(): HasMany { return $this->hasMany(TimeEntry::class); }
    public function checklists(): HasMany { return $this->hasMany(Checklist::class)->orderBy('position'); }
    public function dependencies(): BelongsToMany { return $this->belongsToMany(Task::class, 'task_dependencies', 'task_id', 'depends_on_task_id')->withPivot('type')->withTimestamps(); }

    public function getTaskKeyAttribute(): string
    {
        $prefix = $this->relationLoaded('project') && $this->project ? $this->project->project_number : 'TASK';

        return $prefix.'-'.$this->task_number;
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->due_on !== null && $this->completed_at === null && $this->due_on->isBefore(today());
    }
}
