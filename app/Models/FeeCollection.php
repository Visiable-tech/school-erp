<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeCollection extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'student_id',
        'student_enrollment_id',
        'academic_year_id',

        'receipt_no',
        'payment_date',
        'total_amount',

        'payment_mode',
        'payment_mode_id',
        'school_account_id',

        'transaction_no',
        'bank_name',
        'cheque_no',
        'cheque_date',

        'remarks',

        'collected_by',

        'status',

        'cancellation_reason',
        'cancelled_by',
        'cancelled_at',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'cheque_date' => 'date',
        'total_amount' => 'decimal:2',
        'cancelled_at' => 'datetime',
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

    public function paymentMode()
    {
        return $this->belongsTo(
            PaymentMode::class,
            'payment_mode_id'
        );
    }

    public function schoolAccount()
    {
        return $this->belongsTo(
            SchoolAccount::class,
            'school_account_id'
        );
    }

    public function items()
    {
        return $this->hasMany(
            FeeCollectionItem::class,
            'fee_collection_id'
        );
    }

    public function collector()
    {
        return $this->belongsTo(
            User::class,
            'collected_by'
        );
    }

    public function cancelledBy()
    {
        return $this->belongsTo(
            User::class,
            'cancelled_by'
        );
    }    

    public function refunds(){ 
        return $this->hasMany(
        \App\Models\FeeRefund::class,
        'fee_collection_id'); 
    }

    public function chequeDdDetail()
    {
        return $this->hasOne(
            FeeChequeDdDetail::class,
            'fee_collection_id'
        );
    }
}