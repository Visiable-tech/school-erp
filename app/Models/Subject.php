<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'name',
        'code',
        'type',
        'is_optional',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'is_optional' => 'boolean',
        'status' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function classSubjects()
    {
        return $this->hasMany(ClassSubject::class);
    }

    public function classes()
    {
        return $this->belongsToMany(
            SchoolClass::class,
            'class_subjects',
            'subject_id',
            'school_class_id'
        )
        ->withPivot([
            'academic_year_id',
            'school_id',
            'is_optional',
            'sort_order',
            'status',
        ])
        ->withTimestamps();
    }
}