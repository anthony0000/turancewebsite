<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageVisit extends Model
{
    protected $fillable = [
        'path',
        'route_name',
        'page_group',
        'session_id',
        'ip_address',
        'country_code',
        'country_name',
        'user_agent',
        'referrer',
    ];
}
