<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $fillable = ['project_id', 'task_id', 'actor_id', 'action', 'entity_type', 'entity_id', 'old_values', 'new_values', 'metadata'];

    protected $casts = ['old_values' => 'array', 'new_values' => 'array', 'metadata' => 'array'];

    public function project(): BelongsTo { return $this->belongsTo(Project::class); }
    public function task(): BelongsTo { return $this->belongsTo(Task::class); }
    public function actor(): BelongsTo { return $this->belongsTo(User::class, 'actor_id'); }
}
