<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TcRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'student_id',
        'student_enrollment_id',
        'request_date',
        'tc_reason_id',
        'request_remarks',
        'status',
        'requested_by',
        'reviewed_by',
        'reviewed_at',
        'review_remarks',
        'transfer_certificate_id',
    ];

    protected $casts = [
        'request_date' => 'date',
        'reviewed_at' => 'datetime',
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

    public function reason()
    {
        return $this->belongsTo(
            TcReason::class,
            'tc_reason_id'
        );
    }

    public function requestedBy()
    {
        return $this->belongsTo(
            User::class,
            'requested_by'
        );
    }

    public function reviewedBy()
    {
        return $this->belongsTo(
            User::class,
            'reviewed_by'
        );
    }

    public function transferCertificate()
    {
        return $this->belongsTo(
            TransferCertificate::class,
            'transfer_certificate_id'
        );
    }
}