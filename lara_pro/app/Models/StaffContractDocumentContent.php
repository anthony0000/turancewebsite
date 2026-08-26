<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffContractDocumentContent extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'staff_contract_id',
        'contents',
    ];

    protected $hidden = [
        'contents',
    ];

    public function staffContract(): BelongsTo
    {
        return $this->belongsTo(StaffContract::class);
    }
}
