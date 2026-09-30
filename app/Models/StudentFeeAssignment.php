<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentFeeAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'student_id',
        'student_enrollment_id',
        'academic_year_id',
        'fee_structure_id',
        'assigned_date',
        'discount_type',
        'discount_value',
        'remarks',
        'assigned_by',
        'status',
    ];

    protected $casts = [
        'assigned_date' => 'date',
        'discount_value' => 'decimal:2',
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

    public function enrollment()
    {
        return $this->belongsTo(
            StudentEnrollment::class,
            'student_enrollment_id'
        );
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function feeStructure()
    {
        return $this->belongsTo(FeeStructure::class);
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function dues()
    {
        return $this->hasMany(StudentFeeDue::class);
    }
}