<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Scopes\FarmScope;

class ArtificialInsemination extends Model
{
    use HasFactory;

    // protected static function boot()
    // {
    //     parent::boot();
    //     static::addGlobalScope(new FarmScope());
    // }

    protected $fillable = [
        'farm_id',
        'semen_batch_no',
        'breed_id',
        'semen_company',
        'insemination_date',
        'reproduction_record_id',
        'vet_id',
        'cost',
        'remarks',
        'user_id',
    ];

    protected $casts = [
        'insemination_date' => 'date',
        'cost' => 'decimal:2',
    ];

    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }

    public function reproductionRecord()
    {
        return $this->hasOne(ReproductionRecord::class, 'artificial_insemination_id');
    }

    public function vet()
    {
        return $this->belongsTo(User::class, 'vet_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function breed()
    {
        return $this->belongsTo(Breed::class);
    }
}
