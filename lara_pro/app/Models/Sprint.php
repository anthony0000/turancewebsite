<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sprint extends Model
{
    protected $fillable = ['project_id', 'name', 'goal', 'starts_on', 'ends_on', 'status'];

    protected $casts = ['starts_on' => 'date', 'ends_on' => 'date'];

    public function project(): BelongsTo { return $this->belongsTo(Project::class); }

    public function tasks(): HasMany { return $this->hasMany(Task::class); }
}
