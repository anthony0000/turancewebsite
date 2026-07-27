<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromotionSetting extends Model
{
    protected $fillable = ['key', 'enabled', 'years', 'discount_percent', 'promo_code', 'ends_at'];

    protected $casts = [
        'enabled' => 'boolean',
        'years' => 'integer',
        'discount_percent' => 'decimal:2',
        'ends_at' => 'datetime',
    ];
}
