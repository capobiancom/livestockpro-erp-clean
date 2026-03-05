<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeDocument extends Model
{
    use HasFactory;


    protected $fillable = [
        'employee_id',
        'farm_id',
        'user_id',
        'document_type',
        'document_number',
        'expiry_date',
        'file_path',
    ];

    /**
     * Get the URL for the employee document file.
     *
     * @return string
     */
    public function getFilePathAttribute($value)
    {
        return $value ? \Illuminate\Support\Facades\Storage::url($value) : null;
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
