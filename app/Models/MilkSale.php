<?php

namespace App\Models;

use App\Enums\SaleStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

class MilkSale extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'customer_id',
        'sale_date',
        'quantity',
        'unit',
        'unit_price',
        'total_price',
        'paid_amount',
        'status',
        'notes',
        'farm_id',
        'user_id'
    ];

    protected $casts = [
        'sale_date' => 'date',
        'quantity' => 'float',
        'unit_price' => 'float',
        'total_price' => 'float',
        'paid_amount' => 'float',
        'status' => SaleStatus::class,
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function farm()
    {
        return $this->belongsTo(Farm::class);
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
            $lastNumber = (int) substr($latestInvoice->invoice_number, 3);
            $nextNumber = $lastNumber + 1;
        }

        return 'MS-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }
}
