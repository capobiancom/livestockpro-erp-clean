<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SubscriptionPlan extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'monthly_price_cents',
        'yearly_discount_percent',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'monthly_price_cents' => 'integer',
        'yearly_discount_percent' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function features(): BelongsToMany
    {
        return $this->belongsToMany(SubscriptionFeature::class, 'subscription_plan_features')
            ->withPivot(['is_enabled'])
            ->withTimestamps();
    }

    public function enabledFeatures(): BelongsToMany
    {
        return $this->features()->wherePivot('is_enabled', true);
    }

    public function getYearlyPriceCentsAttribute(): int
    {
        $monthly = (int) ($this->monthly_price_cents ?? 0);
        $discount = (int) ($this->yearly_discount_percent ?? 0);

        $yearly = $monthly * 12;
        $discountAmount = (int) round($yearly * ($discount / 100));

        return max(0, $yearly - $discountAmount);
    }
}
