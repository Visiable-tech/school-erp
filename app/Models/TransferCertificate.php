<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferCertificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'student_id',
        'student_enrollment_id',
        'tc_number',
        'application_date',
        'issue_date',
        'leaving_date',
        'tc_reason_id',
        'tc_remark_option_id',
        'tc_last_result_option_id',
        'remarks',
        'conduct',
        'next_school',
        'next_class',
        'fees_cleared',
        'library_cleared',
        'transport_cleared',
        'status',
        'created_by',
        'issued_by',
        'issued_at',
        'cancelled_by',
        'cancelled_at',
        'cancellation_reason',
        'is_manual',
        'manual_admission_no',
        'manual_student_name',
        'manual_father_name',
        'manual_mother_name',
        'manual_date_of_birth',
        'manual_class',
        'manual_section',
        'manual_academic_year',
    ];

    protected $casts = [
        'application_date' => 'date',
        'issue_date' => 'date',
        'leaving_date' => 'date',
        'manual_date_of_birth' => 'date',

        'is_manual' => 'boolean',
        'fees_cleared' => 'boolean',
        'library_cleared' => 'boolean',
        'transport_cleared' => 'boolean',

        'issued_at' => 'datetime',
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

    public function reason()
    {
        return $this->belongsTo(
            TcReason::class,
            'tc_reason_id'
        );
    }

    public function remarkOption()
    {
        return $this->belongsTo(
            TcRemarkOption::class,
            'tc_remark_option_id'
        );
    }

    public function lastResultOption()
    {
        return $this->belongsTo(
            TcLastResultOption::class,
            'tc_last_result_option_id'
        );
    }

    public function creator()
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    public function issuer()
    {
        return $this->belongsTo(
            User::class,
            'issued_by'
        );
    }
}