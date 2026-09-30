<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeCompileQueue extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'academic_year_id',
        'school_class_id',
        'section_id',
        'fee_structure_id',
        'compile_date',

        'status',

        'total_students',
        'processed_students',
        'compiled_students',
        'skipped_students',
        'failed_students',

        'progress',

        'enrollment_ids',
        'error_details',
        'error_message',

        'started_at',
        'completed_at',

        'created_by',
    ];

    protected $casts = [
        'compile_date' => 'date',

        'total_students' => 'integer',
        'processed_students' => 'integer',
        'compiled_students' => 'integer',
        'skipped_students' => 'integer',
        'failed_students' => 'integer',

        'progress' => 'integer',

        'enrollment_ids' => 'array',
        'error_details' => 'array',

        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function school()
    {
        return $this->belongsTo(
            School::class
        );
    }

    public function academicYear()
    {
        return $this->belongsTo(
            AcademicYear::class
        );
    }

    public function schoolClass()
    {
        return $this->belongsTo(
            SchoolClass::class
        );
    }

    public function section()
    {
        return $this->belongsTo(
            Section::class
        );
    }

    public function feeStructure()
    {
        return $this->belongsTo(
            FeeStructure::class
        );
    }

    public function createdBy()
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'pending' => 'Pending',
            'processing' => 'Processing',
            'completed' => 'Completed',
            'failed' => 'Failed',
            'cancelled' => 'Cancelled',
            default => ucfirst($this->status),
        };
    }
}