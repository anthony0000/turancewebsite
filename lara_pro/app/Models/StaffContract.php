<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffContract extends Model
{
    protected $fillable = [
        'project_id',
        'luxury_quote_id',
        'contract_number',
        'status',
        'staff_name',
        'staff_email',
        'staff_phone',
        'staff_role',
        'starts_on',
        'ends_on',
        'currency',
        'agreed_fee',
        'payment_terms',
        'scope_of_work',
        'terms',
        'company_name',
        'company_signatory_name',
        'company_signatory_title',
        'company_signed_date',
        'staff_signatory_name',
        'staff_signed_date',
        'notes',
    ];

    protected $casts = [
        'starts_on' => 'date',
        'ends_on' => 'date',
        'company_signed_date' => 'date',
        'staff_signed_date' => 'date',
        'agreed_fee' => 'decimal:2',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(LuxuryQuote::class, 'luxury_quote_id');
    }
}
