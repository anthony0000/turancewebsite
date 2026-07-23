<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proposal extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'proposal_template_id',
        'proposal_number',
        'public_token',
        'status',
        'theme_key',
        'title',
        'client_name',
        'client_company',
        'prepared_by',
        'company_name',
        'company_slogan',
        'proposal_date',
        'reference_number',
        'contact_email',
        'phone_number',
        'website',
        'business_address',
        'logo_path',
        'cover_image_path',
        'background_image_path',
        'currency',
        'subtotal',
        'discount_total',
        'tax_total',
        'grand_total',
        'metadata',
    ];

    protected $casts = [
        'proposal_date' => 'date',
        'subtotal' => 'decimal:2',
        'discount_total' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'metadata' => 'array',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(ProposalTemplate::class, 'proposal_template_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(ProposalSection::class)->orderBy('sort_order');
    }

    public function assets(): HasMany
    {
        return $this->hasMany(ProposalAsset::class);
    }

    public function pricingItems(): HasMany
    {
        return $this->hasMany(ProposalPricingItem::class)->orderBy('sort_order');
    }

    public function timelines(): HasMany
    {
        return $this->hasMany(ProposalTimeline::class)->orderBy('sort_order');
    }

    public function teamMembers(): HasMany
    {
        return $this->hasMany(ProposalTeamMember::class)->orderBy('sort_order');
    }

    public function settings(): HasOne
    {
        return $this->hasOne(ProposalSetting::class);
    }

    public function exports(): HasMany
    {
        return $this->hasMany(ProposalExport::class)->latest();
    }
}
