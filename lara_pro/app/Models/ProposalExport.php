<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProposalExport extends Model
{
    protected $fillable = [
        'proposal_id',
        'format',
        'file_name',
        'exported_by',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function proposal(): BelongsTo
    {
        return $this->belongsTo(Proposal::class);
    }
}
