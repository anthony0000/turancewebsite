<?php

namespace App\Models;

use App\Support\PersistentUploadStorage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectPaymentReceipt extends Model
{
    public const TYPES = [
        'project_part_payment' => 'Project part payment',
        'project_full_payment' => 'Project full payment',
        'staff_part_payment' => 'Staff part payment',
        'staff_full_payment' => 'Staff full payment',
        'other_payment' => 'Other payment',
    ];

    protected $fillable = [
        'project_id',
        'project_payment_invoice_id',
        'staff_contract_id',
        'payment_type',
        'counterparty',
        'amount',
        'currency',
        'paid_on',
        'reference',
        'notes',
        'receipt_path',
        'receipt_original_name',
        'receipt_mime',
        'receipt_size',
        'uploaded_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_on' => 'date',
        'receipt_size' => 'integer',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(ProjectPaymentInvoice::class, 'project_payment_invoice_id');
    }

    public function staffContract(): BelongsTo
    {
        return $this->belongsTo(StaffContract::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function hasStoredReceipt(): bool
    {
        return PersistentUploadStorage::exists($this->receipt_path);
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->payment_type] ?? 'Other payment';
    }

    public function sizeLabel(): string
    {
        $bytes = (int) ($this->receipt_size ?? 0);

        if ($bytes < 1024) {
            return $bytes.' B';
        }

        if ($bytes < 1024 * 1024) {
            return number_format($bytes / 1024, 1).' KB';
        }

        return number_format($bytes / (1024 * 1024), 1).' MB';
    }
}
