<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MissionRequest extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'request_id',
        'destination',
        'purpose',
        'estimated_cost',
        'transportation_mode',
        'accommodation_details',
        'budget_approved',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'estimated_cost' => 'decimal:2',
        'budget_approved' => 'boolean',
    ];

    /**
     * Get the request for this mission request.
     */
    public function request()
    {
        return $this->belongsTo(Request::class);
    }
}
