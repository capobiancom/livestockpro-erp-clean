<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionPayment extends Model
{
    protected $fillable = [
        'farm_id',
        'subscription_invoice_id',
        'gateway',
        'amount_cents',
        'currency',
        'status',
        'provider_payment_id',
        'provider_payload',
    ];

    protected $casts = [
        'amount_cents' => 'integer',
        'provider_payload' => 'array',
    ];

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(SubscriptionInvoice::class, 'subscription_invoice_id');
    }
}
