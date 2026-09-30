<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentFeeWaiverAssignmentDue extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_fee_waiver_assignment_id',
        'student_fee_due_id',
        'waiver_amount',
    ];

    protected $casts = [
        'waiver_amount' => 'decimal:2',
    ];

    public function assignment()
    {
        return $this->belongsTo(
            StudentFeeWaiverAssignment::class,
            'student_fee_waiver_assignment_id'
        );
    }

    public function due()
    {
        return $this->belongsTo(
            StudentFeeDue::class,
            'student_fee_due_id'
        );
    }
}