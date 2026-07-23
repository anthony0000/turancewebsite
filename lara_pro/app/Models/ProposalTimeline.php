<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProposalTimeline extends Model
{
    protected $fillable = [
        'proposal_id',
        'phase_title',
        'description',
        'start_date',
        'end_date',
        'duration',
        'deliverables',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function proposal(): BelongsTo
    {
        return $this->belongsTo(Proposal::class);
    }
}
