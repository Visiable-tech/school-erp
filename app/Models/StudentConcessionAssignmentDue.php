<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentConcessionAssignmentDue extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_concession_assignment_id',
        'student_fee_due_id',
        'concession_amount',
    ];

    protected $casts = [
        'concession_amount' => 'decimal:2',
    ];

    public function assignment()
    {
        return $this->belongsTo(
            StudentConcessionAssignment::class,
            'student_concession_assignment_id'
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