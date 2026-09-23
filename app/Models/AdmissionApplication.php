<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdmissionApplication extends Model
{
    use HasFactory;

    protected $fillable = [

        'school_id',
        'admission_enquiry_id',
        'admission_session_id',
        'academic_year_id',
        'school_class_id',

        'application_no',
        'application_date',

        'student_name',
        'date_of_birth',
        'gender',
        'blood_group',
        'nationality',
        'religion',
        'category',
        'mother_tongue',

        'father_name',
        'father_mobile',
        'father_email',
        'father_occupation',

        'mother_name',
        'mother_mobile',
        'mother_email',
        'mother_occupation',

        'guardian_name',
        'guardian_relation',
        'guardian_mobile',

        'present_address',
        'present_city',
        'present_state',
        'present_pin_code',

        'permanent_address',
        'permanent_city',
        'permanent_state',
        'permanent_pin_code',

        'previous_school',
        'previous_class',
        'previous_board',

        'application_status',
        'remarks',

        'created_by',
        'status',

        'reviewed_by',
        'reviewed_at',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'review_remarks',
    ];

    protected $casts = [
        'application_date' => 'date',
        'date_of_birth' => 'date',
        'status' => 'boolean',
        'reviewed_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];


    public function school()
    {
        return $this->belongsTo(School::class);
    }


    public function enquiry()
    {
        return $this->belongsTo(
            AdmissionEnquiry::class,
            'admission_enquiry_id'
        );
    }


    public function admissionSession()
    {
        return $this->belongsTo(
            AdmissionSession::class
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


    public function creator()
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    public function documents()
    {
        return $this->hasMany(
            AdmissionApplicationDocument::class,
            'admission_application_id'
        );
    }

    public function reviewer()
    {
        return $this->belongsTo(
            User::class,
            'reviewed_by'
        );
    }

    public function approver()
    {
        return $this->belongsTo(
            User::class,
            'approved_by'
        );
    }

    public function rejector()
    {
        return $this->belongsTo(
            User::class,
            'rejected_by'
        );
    }

    public function student()
    {
        return $this->hasOne(
            Student::class,
            'admission_application_id'
        );
    }
}