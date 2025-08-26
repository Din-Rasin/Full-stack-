<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'employee_id',
        'is_active',
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
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the roles for the user.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    /**
     * Get the departments for the user.
     */
    public function departments()
    {
        return $this->belongsToMany(Department::class, 'user_departments');
    }

    /**
     * Get the primary department for the user.
     */
    public function primaryDepartment()
    {
        return $this->belongsToMany(Department::class, 'user_departments')->wherePivot('is_primary', true);
    }

    /**
     * Get the requests submitted by the user.
     */
    public function requests()
    {
        return $this->hasMany(Request::class);
    }

    /**
     * Get the approvals for the user.
     */
    public function approvals()
    {
        return $this->hasMany(RequestApproval::class, 'approver_id');
    }

    /**
     * Get the notifications for the user.
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Get the audit logs for the user.
     */
    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    /**
     * Get the workflows created by the user.
     */
    public function workflows()
    {
        return $this->hasMany(Workflow::class, 'created_by');
    }

    /**
     * Check if user has a specific role.
     */
    public function hasRole($role)
    {
        return $this->roles()->where('name', $role)->exists();
    }

    /**
     * Check if user belongs to a specific department.
     */
    public function belongsToDepartment($department)
    {
        return $this->departments()->where('name', $department)->exists();
    }
}
