<?php
namespace App\Http\Controllers;
use App\Models\TeacherAssignment;
use App\Models\TeacherClasswork;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
class TeacherClassworkController extends Controller {
 private function school(Request $r):int {abort_unless($r->user()?->school_id,403);return (int)$r->user()->school_id;}
 private function manager(Request $r):bool{return $r->user()->hasAnyRole(['Super Admin','Admin']) && $r->user()->can('teacher-classwork.review');}
 private function assignments(Request $r){$q=TeacherAssignment::where('school_id',$this->school($r))->where('status',1);
   if(!$this->manager($r))$q->where('teacher_user_id',$r->user()->id);
   return $q->with(['schoolClass','section','subject'])->orderBy('school_class_id')->get();}
 private function scoped(Request $r){$q=TeacherClasswork::where('school_id',$this->school($r));
   if(!$this->manager($r))$q->where('teacher_user_id',$r->user()->id);return $q;}
 public function index(Request $r){$status=$r->query('status','all');abort_unless(in_array($status,['all','pending','approved','rejected','not-uploaded']),404);
   $assignments=$this->assignments($r);$items=null;$missing=collect();
   if($status==='not-uploaded'){
    $date=$r->date('date')?->toDateString() ?? now()->toDateString();
    $uploaded=TeacherClasswork::where('school_id',$this->school($r))->whereDate('work_date',$date)->pluck('teacher_assignment_id')->all();
    $missing=$assignments->whereNotIn('id',$uploaded);
   }else{$q=$this->scoped($r)->with(['assignment.schoolClass','assignment.section','assignment.subject','teacher'])->latest('work_date');
    if($status!=='all')$q->where('approval_status',$status);$items=$q->paginate(20)->withQueryString();}
   return view('teacher.classwork.index',compact('items','missing','assignments','status'));}
 public function create(Request $r){$assignments=$this->assignments($r)->where('teacher_user_id',$r->user()->id);abort_if($assignments->isEmpty(),403,'No active teaching assignment.');return view('teacher.classwork.form',compact('assignments'));}
 public function store(Request $r){$school=$this->school($r);$data=$r->validate([
  'teacher_assignment_id'=>['required','integer',Rule::exists('teacher_assignments','id')->where('school_id',$school)->where('teacher_user_id',$r->user()->id)->where('status',1)],
  'work_date'=>['required','date'],'title'=>['required','string','max:200'],'type'=>['required',Rule::in(['classwork','homework','assignment'])],
  'description'=>['required','string','max:10000'],'due_date'=>['nullable','date','after_or_equal:work_date']]);
  TeacherClasswork::create($data+['school_id'=>$school,'teacher_user_id'=>$r->user()->id,'approval_status'=>'pending']);
  return redirect()->route('teacher.classwork.index')->with('success','Submitted for approval.');}
 public function edit(Request $r,TeacherClasswork $classwork){abort_unless($classwork->school_id===$this->school($r) && $classwork->teacher_user_id===$r->user()->id && $classwork->approval_status==='pending',403);
  $assignments=$this->assignments($r)->where('teacher_user_id',$r->user()->id);return view('teacher.classwork.form',compact('assignments','classwork'));}
 public function update(Request $r,TeacherClasswork $classwork){abort_unless($classwork->school_id===$this->school($r) && $classwork->teacher_user_id===$r->user()->id && $classwork->approval_status==='pending',403);
  $data=$r->validate(['work_date'=>['required','date'],'title'=>['required','string','max:200'],'type'=>['required',Rule::in(['classwork','homework','assignment'])],'description'=>['required','string','max:10000'],'due_date'=>['nullable','date','after_or_equal:work_date']]);
  $classwork->update($data);return redirect()->route('teacher.classwork.index')->with('success','Updated.');}
 public function review(Request $r,TeacherClasswork $classwork){abort_unless($this->manager($r) && $classwork->school_id===$this->school($r),403);
  $data=$r->validate(['approval_status'=>['required',Rule::in(['approved','rejected'])],'review_note'=>['nullable','string','max:2000']]);
  $classwork->update($data+['reviewed_by'=>$r->user()->id,'reviewed_at'=>now()]);return back()->with('success','Review saved.');}
}
