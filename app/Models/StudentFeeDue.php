<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentFeeDue extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'student_fee_assignment_id',
        'student_id',
        'student_enrollment_id',
        'academic_year_id',
        'fee_structure_id',
        'fee_structure_item_id',
        'fee_installment_id',
        'fee_head_id',

        'fee_head_name',
        'installment_name',

        'period_start',
        'period_end',
        'due_date',

        'base_amount',
        'discount_amount',
        'fine_amount',
        'payable_amount',
        'paid_amount',
        'balance_amount',

        'payment_status',
        'status',
        'student_optional_fee_assignment_id',
        'waiver_amount',
        'fine_waiver_amount',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'due_date' => 'date',

        'base_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'fine_amount' => 'decimal:2',
        'payable_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'balance_amount' => 'decimal:2',
        'fine_waiver_amount' => 'decimal:2',
        
        'status' => 'boolean',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function assignment()
    {
        return $this->belongsTo(
            StudentFeeAssignment::class,
            'student_fee_assignment_id'
        );
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

    public function feeStructureItem()
    {
        return $this->belongsTo(FeeStructureItem::class);
    }

    public function feeInstallment()
    {
        return $this->belongsTo(FeeInstallment::class);
    }

    public function feeHead()
    {
        return $this->belongsTo(FeeHead::class);
    }

    public function collectionItems()
    {
        return $this->hasMany(
            FeeCollectionItem::class,
            'student_fee_due_id'
        );
    }

    public function optionalFeeAssignment()
    {
        return $this->belongsTo(
            StudentOptionalFeeAssignment::class,
            'student_optional_fee_assignment_id'
        );
    }
}