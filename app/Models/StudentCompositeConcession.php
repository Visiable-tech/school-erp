<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentCompositeConcession extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'academic_year_id',
        'student_id',
        'student_enrollment_id',

        'assigned_date',

        'concession_mode',
        'concession_value',

        'fee_waiver_mode',
        'fee_waiver_value',

        'fine_waiver_mode',
        'fine_waiver_value',

        'selected_due_ids',

        'reason',

        'approval_status',

        'assigned_by',
        'approved_by',
        'approved_at',

        'status',
    ];


    protected $casts = [
        'assigned_date' => 'date',

        'concession_value' => 'decimal:2',
        'fee_waiver_value' => 'decimal:2',
        'fine_waiver_value' => 'decimal:2',

        'selected_due_ids' => 'array',

        'approved_at' => 'datetime',

        'status' => 'boolean',
    ];


    public function academicYear()
    {
        return $this->belongsTo(
            AcademicYear::class
        );
    }


    public function student()
    {
        return $this->belongsTo(
            Student::class
        );
    }


    public function enrollment()
    {
        return $this->belongsTo(
            StudentEnrollment::class,
            'student_enrollment_id'
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
            StudentCompositeConcessionDue::class,
            'student_composite_concession_id'
        );
    }
}