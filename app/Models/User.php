<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Services\SalesHierarchyService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'salesman_name',
        'region',
        'hierarchy_level',
        'hierarchy_value',
        'supervisor_id',
        'is_enabled',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_enabled' => 'boolean',
        ];
    }

    /**
     * The salesman managed by this user (if they are a manager).
     */
    public function managedSalesmen()
    {
        return $this->hasMany(ManagerSalesman::class, 'manager_id');
    }

    /**
     * The supervisor of this user.
     */
    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    /**
     * The subordinates (managers) under this user.
     */
    public function subordinates()
    {
        return $this->hasMany(User::class, 'supervisor_id');
    }

    /**
     * Whether this user is bound to a node of the SQL Server sales hierarchy.
     */
    public function hasHierarchyScope(): bool
    {
        return SalesHierarchyService::isValidLevel($this->hierarchy_level)
            && !empty($this->hierarchy_value);
    }

    /**
     * The column in BI_ACTIVE_CUSTOMERS this user is matched on,
     * or null when they are not bound to the hierarchy.
     */
    public function hierarchyColumn(): ?string
    {
        return $this->hasHierarchyScope()
            ? SalesHierarchyService::columnFor($this->hierarchy_level)
            : null;
    }
}
