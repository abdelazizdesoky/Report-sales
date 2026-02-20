<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
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
     * Get list of salesman names this user is allowed to see.
     * Includes their own salesman name and all names they manage.
     */
    public function getManagedSalesmenNames(): array
    {
        // Admin, General Manager and Coordinator see all, handled by skipping the filter in ReportService
        if ($this->hasRole('Admin') || $this->hasRole('General Manager') || $this->hasRole('Coordinator')) {
            return [];
        }

        $names = [];

        // If Manager, Area Manager or Supervisor, get names from all subordinates recursively
        if ($this->hasRole('Manager') || $this->hasRole('Area Manager') || $this->hasRole('Supervisor')) {
            foreach ($this->subordinates()->where('is_enabled', true)->get() as $subordinate) {
                $names = array_merge($names, $subordinate->getManagedSalesmenNames());
            }
        }

        // Add names managed directly by this user (mapping in manager_salesman table)
        $managedDirectly = $this->managedSalesmen()->whereNotNull('salesman_name')->pluck('salesman_name')->toArray();
        $names = array_merge($names, $managedDirectly);

        // If the user is also a salesman, include themselves
        if (!empty($this->salesman_name)) {
            $names[] = $this->salesman_name;
        }

        return array_values(array_unique(array_filter($names)));
    }

    /**
     * Check if user is a manager of any salesmen.
     */
    public function isManager(): bool
    {
        return $this->managedSalesmen()->exists();
    }
}
