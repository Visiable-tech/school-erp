<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class AdmissionCandidate extends Model {
 public const STATUSES=['new'=>'New','form_issued'=>'Form Issued','form_rejected'=>'Form Rejected','form_submitted'=>'Form Submitted','reg_rejected'=>'Reg. Rejected','reg_approved'=>'Reg. Approved','admitted'=>'Admitted','adm_rejected'=>'Adm. Rejected','closed'=>'Closed'];
 protected $fillable=['school_id','academic_year_id','school_class_id','candidate_name','father_name','mother_name','mobile','date_of_birth','photo_path','status','admission_enquiry_id','admission_application_id'];
 protected $casts=['date_of_birth'=>'date'];
 public function histories(){return $this->hasMany(AdmissionCandidateStatusHistory::class);}
 public function academicYear(){return $this->belongsTo(AcademicYear::class);}
 public function schoolClass(){return $this->belongsTo(SchoolClass::class);}
}
