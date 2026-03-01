<?php

namespace App\Models;

use App\Enums\CashTransactionDirection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CashTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'farm_id',
        'user_id',
        'cash_account_id',
        'transaction_date',
        'amount',
        'direction',
        'reference_type',
        'reference_id',
        'description',
        'payment_method',
        'balance_after',
    ];

    protected $casts = [
        'direction' => CashTransactionDirection::class,
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
        'balance_after' => 'decimal:2',
    ];

    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cashAccount()
    {
        return $this->belongsTo(CashAccount::class);
    }

    public function reference()
    {
        return $this->morphTo();
    }

    protected static function booted()
    {
        static::created(function ($transaction) {
            $transaction->cashAccount->updateBalance();
        });

        static::updated(function ($transaction) {
            $transaction->cashAccount->updateBalance();
        });

        static::deleted(function ($transaction) {
            $transaction->cashAccount->updateBalance();
        });
    }
}
