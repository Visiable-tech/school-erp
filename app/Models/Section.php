<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'academic_year_id',
        'school_class_id',
        'name',
        'capacity',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
        'capacity' => 'integer',
        'sort_order' => 'integer',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function sectionGroups()
    {
        return $this->belongsToMany(
            SectionGroup::class,
            'section_group_sections',
            'section_id',
            'section_group_id'
        )->withTimestamps();
    }
}