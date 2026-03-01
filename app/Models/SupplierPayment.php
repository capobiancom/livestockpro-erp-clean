<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SupplierPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'purchase_source_id',
        'purchase_source_type',
        'payment_date',
        'amount',
        'payment_method',
        'reference_number',
        'notes',
        'farm_id',
        'user_id',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'payment_method' => PaymentMethod::class,
        'amount' => 'float',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchase_source(): MorphTo
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
        return 'SPY-' . now()->format('YmdHis') . '-' . uniqid();
    }
}
