<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdmissionSetup extends Model
{
    protected $fillable = [
        'school_id','academic_year_id','school_class_id','admission_code','status',
        'no_of_seats','required_age_months','dob_check_date','dob_check_start','dob_check_end',
        'opens_on','closes_on','default_exam_at','syllabus_path','form_fee','form_fee_label',
        'registration_fee','registration_fee_label','online_registration_fee',
    ];
    protected $casts = [
        'dob_check_date'=>'date','dob_check_start'=>'date','dob_check_end'=>'date',
        'opens_on'=>'date','closes_on'=>'date','default_exam_at'=>'datetime',
        'form_fee'=>'decimal:2','registration_fee'=>'decimal:2','online_registration_fee'=>'decimal:2',
    ];
    public function academicYear() { return $this->belongsTo(AcademicYear::class); }
    public function schoolClass() { return $this->belongsTo(SchoolClass::class); }
    public function school() { return $this->belongsTo(School::class); }
}
