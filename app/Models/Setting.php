<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'company_name',
        'logo_path',
        'activity',
        'address',
        'phone',
        'description',
        'extra_metadata',
    ];

    protected $casts = [
        'extra_metadata' => 'array',
    ];
}
