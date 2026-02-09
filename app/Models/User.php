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
        'username', // Added for dual login
        'email',
        'password',
        'salesman_name', // Maps to SQL Server SalesMan column
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
     * Get list of salesman names this user is allowed to see.
     * Includes their own salesman name and all names they manage.
     */
    public function getManagedSalesmenNames(): array
    {
        // Admin sees all, handled by skipping the filter in ReportService
        if ($this->hasRole('Admin')) {
            return [];
        }

        $names = $this->managedSalesmen()->whereNotNull('salesman_name')->pluck('salesman_name')->toArray();

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
