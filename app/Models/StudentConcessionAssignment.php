<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentConcessionAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'academic_year_id',
        'student_id',
        'student_enrollment_id',
        'concession_type_id',
        'concession_mode',
        'concession_value',
        'maximum_amount',
        'assigned_date',
        'effective_from',
        'effective_to',
        'approval_status',
        'approved_by',
        'approved_at',
        'remarks',
        'assigned_by',
        'status',
        'selected_due_ids',
    ];

    protected $casts = [
        'concession_value' => 'decimal:2',
        'maximum_amount' => 'decimal:2',
        'assigned_date' => 'date',
        'effective_from' => 'date',
        'effective_to' => 'date',
        'approved_at' => 'datetime',
        'status' => 'boolean',
        'selected_due_ids' => 'array',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
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

    public function concessionType()
    {
        return $this->belongsTo(
            ConcessionType::class
        );
    }

    public function assignedBy()
    {
        return $this->belongsTo(
            User::class,
            'assigned_by'
        );
    }

    public function approvedBy()
    {
        return $this->belongsTo(
            User::class,
            'approved_by'
        );
    }

    public function dueItems()
    {
        return $this->hasMany(
            StudentConcessionAssignmentDue::class,
            'student_concession_assignment_id'
        );
    }
}