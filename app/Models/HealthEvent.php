<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HealthEvent extends Model
{
    use HasFactory;


    protected $fillable = [
        'animal_id',
        'event_type_id',
        'title',
        'description',
        'occurred_at',
        'resolved_at',
        'cost',
        'vet_fee',
        'lab_cost',
        'other_cost',
        'treated_by',
        'farm_id',
        'health_issue_id',
        'user_id'
    ];

    protected $casts = [
        'occurred_at' => 'date',
        'resolved_at' => 'date',
        'cost' => 'float',
        'vet_fee' => 'float',
        'lab_cost' => 'float',
        'other_cost' => 'float',
    ];

    public function animal()
    {
        return $this->belongsTo(Animal::class);
    }

    public function treatedBy()
    {
        return $this->belongsTo(StaffProfile::class, 'treated_by');
    }

    public function eventType()
    {
        return $this->belongsTo(EventType::class);
    }

    public function healthIssue()
    {
        return $this->belongsTo(HealthIssue::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
