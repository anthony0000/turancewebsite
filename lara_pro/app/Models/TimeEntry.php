<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimeEntry extends Model
{
    protected $fillable = ['project_id', 'task_id', 'user_id', 'started_at', 'ended_at', 'minutes', 'description'];

    protected $casts = ['started_at' => 'datetime', 'ended_at' => 'datetime', 'minutes' => 'integer'];

    public function project(): BelongsTo { return $this->belongsTo(Project::class); }
    public function task(): BelongsTo { return $this->belongsTo(Task::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
