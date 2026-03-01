<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionInvoice extends Model
{
    protected $fillable = [
        'farm_id',
        'farm_subscription_id',
        'invoice_number',
        'invoice_date',
        'subtotal_cents',
        'discount_cents',
        'total_cents',
        'currency',
        'status',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'subtotal_cents' => 'integer',
        'discount_cents' => 'integer',
        'total_cents' => 'integer',
    ];

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(FarmSubscription::class, 'farm_subscription_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(SubscriptionPayment::class, 'subscription_invoice_id');
    }
}
