<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LuxuryQuote extends Model
{
    protected $fillable = [
        'quote_number',
        'template',
        'project_category',
        'company_name',
        'company_industry',
        'recipient_name',
        'recipient_title',
        'recipient_email',
        'recipient_phone',
        'project_title',
        'executive_summary',
        'investment_amount',
        'discount_percent',
        'discount_amount',
        'promo_code',
        'exchange_rate',
        'timeline',
        'valid_until',
        'scope_items',
        'line_items',
        'outcomes',
        'milestones',
        'optional_addons',
        'intro_message',
        'closing_note',
    ];

    protected $casts = [
        'investment_amount' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'exchange_rate' => 'decimal:4',
        'valid_until' => 'date',
        'scope_items' => 'array',
        'line_items' => 'array',
        'outcomes' => 'array',
        'milestones' => 'array',
        'optional_addons' => 'array',
    ];

    public function staffContracts(): HasMany
    {
        return $this->hasMany(StaffContract::class, 'luxury_quote_id');
    }
}
