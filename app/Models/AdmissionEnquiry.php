<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdmissionEnquiry extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'admission_session_id',
        'academic_year_id',
        'school_class_id',
        'enquiry_no',
        'enquiry_date',
        'student_name',
        'date_of_birth',
        'gender',
        'father_name',
        'mother_name',
        'guardian_name',
        'mobile',
        'alternate_mobile',
        'email',
        'address',
        'city',
        'pin_code',
        'previous_school',
        'source',
        'reference_name',
        'enquiry_status',
        'next_followup_date',
        'remarks',
        'created_by',
        'status',
    ];

    protected $casts = [
        'enquiry_date' => 'date',
        'date_of_birth' => 'date',
        'next_followup_date' => 'date',
        'status' => 'boolean',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
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

    public function followups()
    {
        return $this->hasMany(
            AdmissionFollowup::class,
            'admission_enquiry_id'
        );
    }

    public function application()
    {
        return $this->hasOne(
            AdmissionApplication::class,
            'admission_enquiry_id'
        );
    }
    
}