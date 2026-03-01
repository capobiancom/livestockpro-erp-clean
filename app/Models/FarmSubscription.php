<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FarmSubscription extends Model
{
    protected $fillable = [
        'farm_id',
        'subscription_plan_id',
        'billing_period',
        'starts_on',
        'ends_on',
        'next_billing_on',
        'cancelled_at',
        'meta',
    ];

    protected $casts = [
        'starts_on' => 'date',
        'ends_on' => 'date',
        'next_billing_on' => 'date',
        'cancelled_at' => 'datetime',
        'meta' => 'array',
    ];

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    public function isActive(): bool
    {
        if ($this->cancelled_at) {
            return false;
        }

        if (!$this->ends_on) {
            return false;
        }

        return $this->ends_on->endOfDay()->gte(now());
    }

    public function isExpired(): bool
    {
        return !$this->isActive();
    }
}
