<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class AdmissionFormSale extends Model {
 protected $fillable = ['school_id','academic_year_id','school_class_id','admission_candidate_id','form_no','candidate_name','father_name','mother_name','mobile','date_of_birth','gender','application_date','amount','receipt_no','payment_mode','status','approved_for_registration','hostel_required','login_id','created_by','remarks'];
 protected $casts = ['date_of_birth'=>'date','application_date'=>'date','amount'=>'decimal:2','approved_for_registration'=>'boolean','hostel_required'=>'boolean'];
 public function academicYear(){return $this->belongsTo(AcademicYear::class);}
 public function schoolClass(){return $this->belongsTo(SchoolClass::class);}
 public function candidate(){return $this->belongsTo(AdmissionCandidate::class,'admission_candidate_id');}
 public function creator(){return $this->belongsTo(User::class,'created_by');}
}
