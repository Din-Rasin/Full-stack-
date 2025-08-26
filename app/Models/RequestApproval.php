<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestApproval extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'request_id',
        'approver_id',
        'step_number',
        'status',
        'comments',
        'approved_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'approved_at' => 'datetime',
    ];

    /**
     * Get the request for this approval.
     */
    public function request()
    {
        return $this->belongsTo(Request::class);
    }

    /**
     * Get the approver for this approval.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }
}
