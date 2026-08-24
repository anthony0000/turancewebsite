<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Milestone extends Model
{
    protected $fillable = ['project_id', 'owner_id', 'title', 'description', 'due_on', 'status'];

    protected $casts = ['due_on' => 'date'];

    public function project(): BelongsTo { return $this->belongsTo(Project::class); }
    public function owner(): BelongsTo { return $this->belongsTo(User::class, 'owner_id'); }
    public function tasks(): HasMany { return $this->hasMany(Task::class); }

    public function getProgressPercentageAttribute(): int
    {
        $total = $this->relationLoaded('tasks') ? $this->tasks->count() : $this->tasks()->count();
        $complete = $this->relationLoaded('tasks')
            ? $this->tasks->whereNotNull('completed_at')->count()
            : $this->tasks()->whereNotNull('completed_at')->count();

        return $total > 0 ? (int) round(($complete / $total) * 100) : 0;
    }
}
