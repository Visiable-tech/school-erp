<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentFineWaiverAssignmentDue extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_fine_waiver_assignment_id',
        'student_fee_due_id',
        'waiver_amount',
    ];

    protected $casts = [
        'waiver_amount' => 'decimal:2',
    ];


    public function assignment()
    {
        return $this->belongsTo(
            StudentFineWaiverAssignment::class,
            'student_fine_waiver_assignment_id'
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