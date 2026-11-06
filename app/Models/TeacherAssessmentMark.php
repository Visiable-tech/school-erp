<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class TeacherAssessmentMark extends Model {
 protected $fillable=['assessment_id','student_enrollment_id','marks','remarks'];
 public function enrollment(){return $this->belongsTo(StudentEnrollment::class,'student_enrollment_id');}
}
