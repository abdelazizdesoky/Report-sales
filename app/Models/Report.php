<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Report extends Model
{
    /** @use HasFactory<\Database\Factories\ReportFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'source_name',
    ];

    public function roles()
    {
        return $this->belongsToMany(\Spatie\Permission\Models\Role::class, 'role_reports');
    }
}
