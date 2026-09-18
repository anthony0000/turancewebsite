<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectPaymentInvoice extends Model
{
    protected $fillable = [
        'project_id',
        'original_invoice_id',
        'invoice_number',
        'status',
        'issue_date',
        'due_date',
        'payment_terms',
        'original_invoice_number',
        'client_company',
        'client_name',
        'client_title',
        'client_email',
        'client_phone',
        'project_title',
        'project_category',
        'description',
        'payment_description',
        'currency',
        'local_currency',
        'contract_value',
        'completion_percentage',
        'subtotal_amount',
        'tax_rate',
        'tax_amount',
        'total_amount',
        'exchange_rate',
        'bank_name',
        'account_name',
        'account_number',
        'prepared_by',
        'notes',
        'paid_at',
        'paid_by',
        'payment_reference',
        'payment_notes',
        'created_by',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'contract_value' => 'decimal:2',
        'completion_percentage' => 'decimal:2',
        'subtotal_amount' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'exchange_rate' => 'decimal:4',
        'paid_at' => 'datetime',
    ];

    public function getRouteKeyName(): string
    {
        return 'invoice_number';
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function originalInvoice(): BelongsTo
    {
        return $this->belongsTo(LuxuryQuote::class, 'original_invoice_id');
    }

    public function paidBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function receipts(): HasMany
    {
        return $this->hasMany(ProjectPaymentReceipt::class);
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid' && $this->paid_at !== null;
    }

    public function currencySymbol(?string $currency = null): string
    {
        return match (strtoupper($currency ?? $this->currency)) {
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            default => strtoupper($currency ?? $this->currency).' ',
        };
    }

    public function getBalanceAmountAttribute(): float
    {
        return max(0, round((float) $this->contract_value - (float) $this->subtotal_amount, 2));
    }

    public function getLocalTotalAmountAttribute(): float
    {
        return round((float) $this->total_amount * (float) $this->exchange_rate, 2);
    }

    public function getLocalBalanceAmountAttribute(): float
    {
        return round($this->balance_amount * (float) $this->exchange_rate, 2);
    }
}
