<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'start_time',
        'end_time',
        'google_event_id',
        'meet_link',
    ];

    /**
     * Transient property used to flag if a Google Meet should be created
     */
    public $create_meet = false;

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
