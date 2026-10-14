<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class AdmissionCandidateStatusHistory extends Model {
 protected $fillable=['admission_candidate_id','status','enquiry_level','registration_level','remarks','created_by'];
 public function creator(){return $this->belongsTo(User::class,'created_by');}
 public function candidate(){return $this->belongsTo(AdmissionCandidate::class,'admission_candidate_id');}
}
