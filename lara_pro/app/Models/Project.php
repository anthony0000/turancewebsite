<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $fillable = [
        'project_number',
        'name',
        'client_name',
        'client_company',
        'status',
        'starts_on',
        'ends_on',
        'description',
    ];

    protected $casts = [
        'starts_on' => 'date',
        'ends_on' => 'date',
    ];

    public function staffContracts(): HasMany
    {
        return $this->hasMany(StaffContract::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(ProjectFile::class);
    }
}
