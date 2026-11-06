<?php
namespace App\Http\Controllers;
use App\Models\TeacherAssignment;
use App\Models\StudentEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
class TeacherStudentLeaveController extends Controller {
 private function assignments(Request $r) {
  $u=$r->user();abort_unless($u->school_id,403);
  $q=TeacherAssignment::with(['academicYear','schoolClass','section'])->where('school_id',$u->school_id)->where('status',true)->where('is_class_teacher',true);
  if(!$u->hasRole('Super Admin'))$q->where('teacher_user_id',$u->id);
  return $q;
 }
 private function enrollmentAllowed(Request $r,int $id) {
  $e=StudentEnrollment::where('school_id',$r->user()->school_id)->findOrFail($id);
  abort_unless($this->assignments($r)->where('academic_year_id',$e->academic_year_id)->where('school_class_id',$e->school_class_id)->where('section_id',$e->section_id)->exists(),403);
  return $e;
 }
 public function index(Request $r) {
  $assignments=$this->assignments($r)->get();$students=collect();$leaves=collect();$assignment=null;
  if($r->filled('assignment_id')) {
   $assignment=$this->assignments($r)->findOrFail($r->integer('assignment_id'));
   $students=StudentEnrollment::with('student')->where('school_id',$r->user()->school_id)->where('academic_year_id',$assignment->academic_year_id)->where('school_class_id',$assignment->school_class_id)->where('section_id',$assignment->section_id)->where('status',true)->get();
   $leaves=DB::table('teacher_student_leaves')->where('school_id',$r->user()->school_id)->where('academic_year_id',$assignment->academic_year_id)->whereIn('student_enrollment_id',$students->pluck('id'))->orderByDesc('id')->paginate(20)->withQueryString();
  }
  return view('teacher.student-leaves.index',compact('assignments','assignment','students','leaves'));
 }
 public function store(Request $r) {
  $d=$r->validate(['student_enrollment_id'=>'required|integer','start_date'=>'required|date','end_date'=>'required|date|after_or_equal:start_date','reason'=>'required|string|max:3000']);
  $e=$this->enrollmentAllowed($r,(int)$d['student_enrollment_id']);
  DB::table('teacher_student_leaves')->insert($d+['school_id'=>$r->user()->school_id,'academic_year_id'=>$e->academic_year_id,'requested_by'=>$r->user()->id,'status'=>'pending','created_at'=>now(),'updated_at'=>now()]);
  return back()->with('success','Student leave request submitted.');
 }
 public function review(Request $r,int $leave) {
  $d=$r->validate(['status'=>['required',Rule::in(['approved','rejected'])],'review_note'=>'nullable|string|max:3000']);
  $row=DB::table('teacher_student_leaves')->where('school_id',$r->user()->school_id)->where('id',$leave)->first();abort_unless($row,404);
  $this->enrollmentAllowed($r,(int)$row->student_enrollment_id);
  abort_unless($row->status==='pending',422,'Only pending requests can be reviewed.');
  DB::table('teacher_student_leaves')->where('id',$leave)->where('status','pending')->update(['status'=>$d['status'],'review_note'=>$d['review_note']??null,'reviewed_by'=>$r->user()->id,'reviewed_at'=>now(),'updated_at'=>now()]);
  return back()->with('success','Leave request reviewed.');
 }
}
