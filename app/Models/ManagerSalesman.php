<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManagerSalesman extends Model
{
    protected $table = 'manager_salesman';
    
    protected $fillable = [
        'manager_id',
        'salesman_name',
    ];

    /**
     * Get the manager user.
     */
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }
}
