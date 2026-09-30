<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentCompositeConcessionDue extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_composite_concession_id',
        'student_fee_due_id',

        'concession_amount',
        'fee_waiver_amount',
        'fine_waiver_amount',
    ];


    protected $casts = [
        'concession_amount' => 'decimal:2',
        'fee_waiver_amount' => 'decimal:2',
        'fine_waiver_amount' => 'decimal:2',
    ];


    public function compositeConcession()
    {
        return $this->belongsTo(
            StudentCompositeConcession::class,
            'student_composite_concession_id'
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