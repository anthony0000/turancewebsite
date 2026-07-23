<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProposalSetting extends Model
{
    protected $fillable = [
        'proposal_id',
        'primary_color',
        'secondary_color',
        'accent_color',
        'font_family',
        'header_style',
        'footer_style',
        'page_numbering',
        'watermark',
        'options',
    ];

    protected $casts = [
        'page_numbering' => 'boolean',
        'options' => 'array',
    ];

    public function proposal(): BelongsTo
    {
        return $this->belongsTo(Proposal::class);
    }
}
