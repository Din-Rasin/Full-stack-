<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'code',
        'manager_id',
        'workflow_config',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'workflow_config' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the manager of the department.
     */
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * Get the users in the department.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_departments');
    }

    /**
     * Get the workflows for the department.
     */
    public function workflows()
    {
        return $this->hasMany(Workflow::class);
    }
}
