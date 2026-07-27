<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    protected $fillable = [
        'name',
        'email',
        'topic',
        'promo_code',
        'promo_discount_percent',
        'message',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'promo_discount_percent' => 'decimal:2',
    ];
}
