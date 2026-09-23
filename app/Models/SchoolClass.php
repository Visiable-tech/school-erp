<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolClass extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'wing_id',
        'name',
        'code',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function wing()
    {
        return $this->belongsTo(Wing::class);
    }

    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    public function classSubjects()
    {
        return $this->hasMany(ClassSubject::class);
    }

    public function subjects()
    {
        return $this->belongsToMany(
            Subject::class,
            'class_subjects',
            'school_class_id',
            'subject_id'
        )
        ->withPivot([
            'academic_year_id',
            'school_id',
            'is_optional',
            'sort_order',
            'status'
        ])
        ->withTimestamps();
    }

    public function admissionEnquiries()
    {
        return $this->hasMany(
            AdmissionEnquiry::class
        );
    }
}