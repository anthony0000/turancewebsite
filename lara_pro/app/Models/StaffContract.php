<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class StaffContract extends Model
{
    public function getRouteKeyName(): string
    {
        return 'contract_number';
    }

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
        'signed_document_path',
        'signed_document_original_name',
        'signed_document_mime',
        'signed_document_size',
        'notes',
    ];

    protected $casts = [
        'starts_on' => 'date',
        'ends_on' => 'date',
        'company_signed_date' => 'date',
        'staff_signed_date' => 'date',
        'agreed_fee' => 'decimal:2',
        'signed_document_size' => 'integer',
    ];

    public function hasSignedDocument(): bool
    {
        if (! filled($this->signed_document_path)) {
            return false;
        }

        $relativePath = ltrim((string) $this->signed_document_path, '/\\\\');
        $disk = Storage::disk('public_uploads');

        if ($disk->exists($relativePath)) {
            return true;
        }

        foreach ([storage_path('app'), storage_path('app/private'), storage_path('app/public')] as $root) {
            if (is_file($root.DIRECTORY_SEPARATOR.$relativePath)) {
                return true;
            }
        }

        return false;
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(LuxuryQuote::class, 'luxury_quote_id');
    }

}
