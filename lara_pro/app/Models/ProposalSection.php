<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProposalSection extends Model
{
    protected $fillable = [
        'proposal_id',
        'type',
        'title',
        'eyebrow',
        'body',
        'payload',
        'layout_style',
        'is_visible',
        'sort_order',
    ];

    protected $casts = [
        'payload' => 'array',
        'is_visible' => 'boolean',
    ];

    public function proposal(): BelongsTo
    {
        return $this->belongsTo(Proposal::class);
    }
}
