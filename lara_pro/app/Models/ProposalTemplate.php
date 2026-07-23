<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProposalTemplate extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'category',
        'theme_key',
        'description',
        'palette',
        'settings',
        'preview',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'palette' => 'array',
        'settings' => 'array',
        'preview' => 'array',
        'is_active' => 'boolean',
    ];

    public function proposals(): HasMany
    {
        return $this->hasMany(Proposal::class);
    }
}
