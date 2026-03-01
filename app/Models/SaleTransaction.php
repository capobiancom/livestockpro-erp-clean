<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SaleTransaction extends Model
{
    protected $fillable = [
        'customer_id',
        'sale_id', // Keep for existing Sale relations
        'sale_transaction_source_id',
        'sale_transaction_source_type',
        'transaction_date',
        'amount',
        'payment_method',
        'reference_number',
        'notes',
        'farm_id',
        'user_id',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'payment_method' => PaymentMethod::class,
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function sale_transaction_source(): MorphTo
    {
        return $this->morphTo();
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function generateReferenceNumber(): string
    {
        return 'TRN-' . now()->format('YmdHis') . '-' . uniqid();
    }
}
