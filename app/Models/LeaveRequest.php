<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'request_id',
        'leave_type',
        'days_requested',
        'emergency_contact',
        'medical_certificate',
        'is_paid',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_paid' => 'boolean',
    ];

    /**
     * Get the request for this leave request.
     */
    public function request()
    {
        return $this->belongsTo(Request::class);
    }
}
