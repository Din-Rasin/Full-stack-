<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'request_type',
        'user_id',
        'workflow_id',
        'request_data',
        'status',
        'start_date',
        'end_date',
        'reason',
        'submitted_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'request_data' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'submitted_at' => 'datetime',
    ];

    /**
     * Get the user who submitted the request.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the workflow for the request.
     */
    public function workflow()
    {
        return $this->belongsTo(Workflow::class);
    }

    /**
     * Get the approvals for the request.
     */
    public function approvals()
    {
        return $this->hasMany(RequestApproval::class);
    }

    /**
     * Get the leave request details.
     */
    public function leaveRequest()
    {
        return $this->hasOne(LeaveRequest::class);
    }

    /**
     * Get the mission request details.
     */
    public function missionRequest()
    {
        return $this->hasOne(MissionRequest::class);
    }
}
