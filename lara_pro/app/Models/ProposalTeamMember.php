<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProposalTeamMember extends Model
{
    protected $fillable = [
        'proposal_id',
        'name',
        'role',
        'bio',
        'profile_image_path',
        'email',
        'social_link',
        'sort_order',
    ];

    public function proposal(): BelongsTo
    {
        return $this->belongsTo(Proposal::class);
    }
}
