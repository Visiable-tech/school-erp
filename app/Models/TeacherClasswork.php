<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class TeacherClasswork extends Model {
 protected $fillable=['school_id','teacher_assignment_id','teacher_user_id','work_date','title','type','description','due_date','approval_status','review_note','reviewed_by','reviewed_at'];
 protected $casts=['work_date'=>'date','due_date'=>'date','reviewed_at'=>'datetime'];
 public function assignment(){return $this->belongsTo(TeacherAssignment::class,'teacher_assignment_id');}
 public function teacher(){return $this->belongsTo(User::class,'teacher_user_id');}
}
