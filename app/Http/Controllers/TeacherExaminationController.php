<?php
namespace App\Http\Controllers;
use App\Models\{TeacherAssignment,TeacherAssessment,TeacherAssessmentMark,TeacherTermRemark,StudentEnrollment};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
class TeacherExaminationController extends Controller {
 private function school(Request $r): int {abort_unless($r->user()?->school_id,403);return (int)$r->user()->school_id;}
 private function assignments(Request $r) {
  $q=TeacherAssignment::with(['schoolClass','section','subject','academicYear'])->where('school_id',$this->school($r))->where('status',1);
  if(!$r->user()->hasAnyRole(['Super Admin','Admin']))$q->where('teacher_user_id',$r->user()->id);
  return $q->get();
 }
 private function assignment(Request $r,int $id): TeacherAssignment {
  $a=$this->assignments($r)->firstWhere('id',$id);abort_unless($a,403,'Not assigned to this class/section.');return $a;
 }
 private function students(TeacherAssignment $a) {
  return StudentEnrollment::with('student')->where('school_id',$a->school_id)->where('academic_year_id',$a->academic_year_id)->where('school_class_id',$a->school_class_id)->where('section_id',$a->section_id)->where('is_current',1)->orderBy('roll_no')->get();
 }
 private function assessment(Request $r,int $id): TeacherAssessment {
  $a=TeacherAssessment::where('school_id',$this->school($r))->findOrFail($id);
  $this->assignment($r,(int)$a->teacher_assignment_id);return $a;
 }
 public function index(Request $r) {
  $assignments=$this->assignments($r);$ids=$assignments->pluck('id');
  $assessments=TeacherAssessment::with('assignment')->where('school_id',$this->school($r))->whereIn('teacher_assignment_id',$ids)->latest('assessment_date')->paginate(20);
  return view('teacher.examination.index',compact('assessments','assignments'));
 }
 public function create(Request $r) {$assignments=$this->assignments($r);return view('teacher.examination.create',compact('assignments'));}
 public function store(Request $r) {
  $d=$r->validate(['teacher_assignment_id'=>'required|integer','term'=>'required|string|max:80','title'=>'required|string|max:160','assessment_date'=>'required|date','max_marks'=>'required|numeric|gt:0|lte:99999']);
  $a=$this->assignment($r,(int)$d['teacher_assignment_id']);
  $assessment=TeacherAssessment::create($d+['school_id'=>$this->school($r),'teacher_user_id'=>$r->user()->id]);
  return redirect()->route('teacher.examination.marks',$assessment)->with('success','Assessment created.');
 }
 public function marks(Request $r,int $id) {
  $assessment=$this->assessment($r,$id);$students=$this->students($this->assignment($r,(int)$assessment->teacher_assignment_id));
  $marks=TeacherAssessmentMark::where('assessment_id',$id)->get()->keyBy('student_enrollment_id');
  return view('teacher.examination.marks',compact('assessment','students','marks'));
 }
 public function saveMarks(Request $r,int $id) {
  $assessment=$this->assessment($r,$id);$a=$this->assignment($r,(int)$assessment->teacher_assignment_id);
  $d=$r->validate(['marks'=>'required|array','marks.*'=>'nullable|numeric|min:0','remarks'=>'nullable|array','remarks.*'=>'nullable|string|max:2000']);
  $allowed=$this->students($a)->pluck('id')->map(fn($v)=>(string)$v)->all();
  foreach(array_keys($d['marks']) as $key)if(!in_array((string)$key,$allowed,true))throw ValidationException::withMessages(['marks'=>'Invalid student enrollment.']);
  foreach($d['marks'] as $key=>$mark)if($mark!==null && (float)$mark>(float)$assessment->max_marks)throw ValidationException::withMessages(['marks'=>'Marks cannot exceed maximum marks.']);
  DB::transaction(function()use($id,$d){foreach($d['marks'] as $key=>$mark){TeacherAssessmentMark::updateOrCreate(['assessment_id'=>$id,'student_enrollment_id'=>$key],['marks'=>$mark,'remarks'=>$d['remarks'][$key]??null]);}});
  return back()->with('success','Marks saved.');
 }
 public function remarks(Request $r) {
  $assignments=$this->assignments($r);$assignment=null;$students=collect();$saved=collect();$term=trim((string)$r->query('term','Term 1'));
  if($r->filled('assignment_id')){$assignment=$this->assignment($r,(int)$r->query('assignment_id'));$students=$this->students($assignment);$saved=TeacherTermRemark::where('teacher_assignment_id',$assignment->id)->where('term',$term)->get()->keyBy('student_enrollment_id');}
  return view('teacher.examination.remarks',compact('assignments','assignment','students','saved','term'));
 }
 public function saveRemarks(Request $r) {
  $d=$r->validate(['assignment_id'=>'required|integer','term'=>'required|string|max:80','remarks'=>'required|array','remarks.*'=>'nullable|string|max:5000']);
  $a=$this->assignment($r,(int)$d['assignment_id']);$allowed=$this->students($a)->pluck('id')->map(fn($v)=>(string)$v)->all();
  foreach(array_keys($d['remarks']) as $key)if(!in_array((string)$key,$allowed,true))throw ValidationException::withMessages(['remarks'=>'Invalid student enrollment.']);
  DB::transaction(function()use($r,$a,$d){foreach($d['remarks'] as $key=>$value){if(trim((string)$value)===''){TeacherTermRemark::where('teacher_assignment_id',$a->id)->where('student_enrollment_id',$key)->where('term',$d['term'])->delete();continue;}
   TeacherTermRemark::updateOrCreate(['teacher_assignment_id'=>$a->id,'student_enrollment_id'=>$key,'term'=>$d['term']],['school_id'=>$a->school_id,'teacher_user_id'=>$r->user()->id,'remark'=>$value]);}});
  return redirect()->route('teacher.examination.remarks',['assignment_id'=>$a->id,'term'=>$d['term']])->with('success','Remarks saved.');
 }
}
