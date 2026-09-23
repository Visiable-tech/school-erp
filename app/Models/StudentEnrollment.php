<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentEnrollment extends Model
{
    use HasFactory;

    protected $fillable = [

        'school_id',
        'student_id',
        'academic_year_id',
        'school_class_id',
        'section_id',

        'roll_no',

        'enrollment_status',
        'enrollment_date',

        'is_current',
        'status',
    ];

    protected $casts = [
        'enrollment_date' => 'date',
        'is_current' => 'boolean',
        'status' => 'boolean',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function attendances()
    {
        return $this->hasMany(
            StudentAttendance::class,
            'student_enrollment_id'
        );
    }
    
}