<?php

namespace App\Models;

use App\Enums\CashAccountType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CashAccount extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'farm_id',
        'user_id',
        'account_id',
        'name',
        'type',
        'account_number',
        'bank_name',
        'branch_name',
        'opening_balance',
        'current_balance',
        'is_active',
        'description',
    ];

    protected $casts = [
        'type' => CashAccountType::class,
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function chartOfAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'account_id');
    }

    public function transactions()
    {
        return $this->hasMany(CashTransaction::class);
    }

    public function updateBalance()
    {
        $totalIn = $this->transactions()->where('direction', 'in')->sum('amount');
        $totalOut = $this->transactions()->where('direction', 'out')->sum('amount');
        $this->current_balance = $this->opening_balance + $totalIn - $totalOut;
        $this->save();
    }
}
