<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class TeacherAssessment extends Model {
 protected $fillable=['school_id','teacher_user_id','teacher_assignment_id','term','title','assessment_date','max_marks'];
 protected $casts=['assessment_date'=>'date'];
 public function assignment(){return $this->belongsTo(TeacherAssignment::class,'teacher_assignment_id');}
 public function marks(){return $this->hasMany(TeacherAssessmentMark::class,'assessment_id');}
}
