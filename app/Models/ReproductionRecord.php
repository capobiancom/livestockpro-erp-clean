<?php

namespace App\Models;

use App\Scopes\FarmScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReproductionRecord extends Model
{
    use HasFactory;

    // protected static function boot()
    // {
    //     parent::boot();
    //     static::addGlobalScope(new FarmScope());
    // }

    protected $fillable = ['animal_id', 'event', 'partner_id', 'event_date', 'outcome', 'notes', 'farm_id', 'heat_stage', 'performed_by', 'artificial_insemination_id', 'user_id'];

    protected $casts = [
        'event_date' => 'date',
    ];

    public function animal()
    {
        return $this->belongsTo(Animal::class);
    }

    public function partner()
    {
        return $this->belongsTo(Animal::class, 'partner_id');
    }

    public function performer()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    public function artificialInsemination()
    {
        return $this->belongsTo(ArtificialInsemination::class);
    }

    public function pregnancy()
    {
        return $this->hasOne(Pregnancy::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
