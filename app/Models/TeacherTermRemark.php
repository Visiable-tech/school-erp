<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class TeacherTermRemark extends Model {
 protected $fillable=['school_id','teacher_user_id','teacher_assignment_id','student_enrollment_id','term','remark'];
}
