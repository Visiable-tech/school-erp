<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class TeacherAssignment extends Model {
 protected $fillable=['school_id','teacher_user_id','academic_year_id','school_class_id','section_id','subject_id','is_class_teacher','status'];
 protected $casts=['is_class_teacher'=>'boolean','status'=>'boolean'];
 public function teacher(){return $this->belongsTo(User::class,'teacher_user_id');}
 public function academicYear(){return $this->belongsTo(AcademicYear::class);}
 public function schoolClass(){return $this->belongsTo(SchoolClass::class);}
 public function section(){return $this->belongsTo(Section::class);}
 public function subject(){return $this->belongsTo(Subject::class);}
}
