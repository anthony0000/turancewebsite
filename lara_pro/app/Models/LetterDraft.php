<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LetterDraft extends Model
{
    protected $fillable = ['content'];

    protected $casts = ['content' => 'array'];
}
