<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentType extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'name',
        'code',
        'description',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function school()
    {
        return $this->belongsTo(
            School::class
        );
    }

    public function enrollments()
    {
        return $this->hasMany(
            StudentEnrollment::class
        );
    }
}