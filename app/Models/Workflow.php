<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Workflow extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'type',
        'department_id',
        'approval_steps',
        'conditions',
        'is_active',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'approval_steps' => 'array',
        'conditions' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the department for the workflow.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the creator of the workflow.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the requests for the workflow.
     */
    public function requests()
    {
        return $this->hasMany(Request::class);
    }
}
