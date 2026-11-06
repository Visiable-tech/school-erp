<?php
namespace App\Http\Controllers;
use App\Models\TeacherAssignment;
use App\Models\StudentEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
class TeacherPeriodAttendanceController extends Controller {
 private function assignments(Request $r) {
  $u=$r->user();abort_unless($u->school_id,403);
  $q=TeacherAssignment::with(['academicYear','schoolClass','section','subject'])->where('school_id',$u->school_id)->where('status',true);
  if(!$u->hasRole('Super Admin')) $q->where('teacher_user_id',$u->id);
  return $q;
 }
 private function assignment(Request $r) {return $this->assignments($r)->findOrFail($r->integer('assignment_id'));}
 public function index(Request $r) {
  $assignments=$this->assignments($r)->get();$assignment=null;$students=collect();$records=collect();
  $date=$r->input('attendance_date',now()->toDateString());$period=(int)$r->input('period_number',1);
  if($r->filled('assignment_id')) {
   $assignment=$this->assignment($r);
   $students=StudentEnrollment::with('student')->where('school_id',$r->user()->school_id)->where('academic_year_id',$assignment->academic_year_id)->where('school_class_id',$assignment->school_class_id)->where('section_id',$assignment->section_id)->where('status',true)->get();
   $records=DB::table('teacher_period_attendance')->where('teacher_assignment_id',$assignment->id)->whereDate('attendance_date',$date)->where('period_number',$period)->pluck('attendance_status','student_enrollment_id');
  }
  return view('teacher.period-attendance.index',compact('assignments','assignment','students','records','date','period'));
 }
 public function store(Request $r) {
  $data=$r->validate(['assignment_id'=>'required|integer','attendance_date'=>'required|date|before_or_equal:today','period_number'=>'required|integer|min:1|max:20','statuses'=>'required|array|min:1','statuses.*'=>['required',Rule::in(['present','absent','late','leave'])]]);
  $assignment=$this->assignment($r);
  $ids=array_keys($data['statuses']);
  $eligible=StudentEnrollment::where('school_id',$r->user()->school_id)->where('academic_year_id',$assignment->academic_year_id)->where('school_class_id',$assignment->school_class_id)->where('section_id',$assignment->section_id)->where('status',true)->whereIn('id',$ids)->pluck('id')->map(fn($id)=>(string)$id)->all();
  abort_unless(count($eligible)===count($ids) && count(array_diff(array_map('strval',$ids),$eligible))===0,403,'Invalid student enrollment.');
  DB::transaction(function() use($data,$assignment,$r) {
   foreach($data['statuses'] as $enrollmentId=>$status) {
    $key=['teacher_assignment_id'=>$assignment->id,'student_enrollment_id'=>$enrollmentId,'attendance_date'=>$data['attendance_date'],'period_number'=>$data['period_number']];
    DB::table('teacher_period_attendance')->updateOrInsert($key,['school_id'=>$r->user()->school_id,'academic_year_id'=>$assignment->academic_year_id,'attendance_status'=>$status,'marked_by'=>$r->user()->id,'updated_at'=>now(),'created_at'=>DB::raw('COALESCE(created_at, CURRENT_TIMESTAMP)')]);
   }
  });
  return redirect()->route('teacher.period-attendance.index',['assignment_id'=>$assignment->id,'attendance_date'=>$data['attendance_date'],'period_number'=>$data['period_number']])->with('success','Period attendance saved.');
 }
}
