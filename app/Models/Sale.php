<?php

namespace App\Models;

use App\Enums\SaleStatus;
use App\Scopes\FarmScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany; // Keep HasMany for salesItems
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'farm_id',
        'user_id',
        'invoice_date',
        'total_amount',
        'paid_amount',
        'status',
        'invoice_number',
    ];

    protected $appends = ['due_amount'];

    protected $casts = [
        'invoice_date' => 'date',
        'total_amount' => 'float',
        'paid_amount' => 'float',
        'status' => SaleStatus::class,
    ];

    public function getDueAmountAttribute(): float
    {
        return $this->total_amount - $this->paid_amount;
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function salesItems(): HasMany
    {
        return $this->hasMany(SalesItem::class);
    }

    public function saleTransactions(): MorphMany
    {
        return $this->morphMany(SaleTransaction::class, 'sale_transaction_source');
    }

    public static function generateInvoiceNumber(): string
    {
        $latestInvoice = self::whereNotNull('invoice_number')
            ->orderByDesc('invoice_number')
            ->first();

        $nextNumber = 1;
        if ($latestInvoice) {
            $lastNumber = (int) substr($latestInvoice->invoice_number, 4); // 'INV-' is 4 chars
            $nextNumber = $lastNumber + 1;
        }

        return 'INV-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }
}
