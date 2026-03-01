<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DemoRequest extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'country',
        'preferred_date',
        'preferred_time',
        'timezone',
        'message',
        'status',
        'emailed_at',
        'scheduled_at',
        'meta',
    ];

    protected $casts = [
        'emailed_at' => 'datetime',
        'scheduled_at' => 'datetime',
        'meta' => 'array',
    ];
}
