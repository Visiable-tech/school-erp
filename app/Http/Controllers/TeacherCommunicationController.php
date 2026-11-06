<?php
namespace App\Http\Controllers;

use App\Models\TeacherAssignment;
use App\Models\StudentEnrollment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TeacherCommunicationController extends Controller
{
 private function school(Request $r): int {
  abort_unless($r->user()?->school_id,403,'School assignment required.');
  return (int)$r->user()->school_id;
 }
 private function teacherSections(Request $r) {
  return TeacherAssignment::where('school_id',$this->school($r))
   ->where('teacher_user_id',$r->user()->id)->where('status',true)
   ->get(['academic_year_id','school_class_id','section_id']);
 }
 private function allowedEnrollments(Request $r) {
  $assignments=$this->teacherSections($r);
  return StudentEnrollment::where('school_id',$this->school($r))->where('is_current',true)
   ->where(function($q)use($assignments){
    foreach($assignments as $a) $q->orWhere(function($s)use($a){
     $s->where('academic_year_id',$a->academic_year_id)->where('school_class_id',$a->school_class_id)->where('section_id',$a->section_id);
    });
    if($assignments->isEmpty())$q->whereRaw('1=0');
   });
 }
 private function isManager(Request $r): bool {
  return $r->user()->hasAnyRole(['Super Admin','Admin']);
 }
 private function noticeScope(Request $r) {
  $q=DB::table('teacher_notices')->where('school_id',$this->school($r));
  if(!$this->isManager($r)) {
   $sections=$this->teacherSections($r);
   $q->where(function($q)use($r,$sections){
    $q->whereNull('section_id')->orWhere('created_by',$r->user()->id);
    foreach($sections as $a) $q->orWhere(function($s)use($a){
     $s->where('academic_year_id',$a->academic_year_id)->where('school_class_id',$a->school_class_id)->where('section_id',$a->section_id);
    });
   });
  }
  return $q;
 }
 public function inbox(Request $r) {
  $messages=DB::table('teacher_messages')->where('school_id',$this->school($r))->where('recipient_user_id',$r->user()->id)
   ->orderByDesc('id')->paginate(20);
  $senders=User::whereIn('id',$messages->pluck('sender_user_id'))->pluck('name','id');
  return view('teacher.communication.messages',compact('messages','senders')+['mode'=>'inbox']);
 }
 public function sent(Request $r) {
  $messages=DB::table('teacher_messages')->where('school_id',$this->school($r))->where('sender_user_id',$r->user()->id)
   ->orderByDesc('id')->paginate(20);
  $senders=User::whereIn('id',$messages->pluck('recipient_user_id'))->pluck('name','id');
  return view('teacher.communication.messages',compact('messages','senders')+['mode'=>'sent']);
 }
 public function compose(Request $r) {
  $users=User::where('school_id',$this->school($r))->whereKeyNot($r->user()->id)->orderBy('name')->get(['id','name','email']);
  return view('teacher.communication.compose',compact('users'));
 }
 public function send(Request $r) {
  $school=$this->school($r);
  $data=$r->validate([
   'recipient_user_id'=>['required','integer',Rule::exists('users','id')->where('school_id',$school)],
   'subject'=>'required|string|max:180','body'=>'required|string|max:10000'
  ]);
  abort_if((int)$data['recipient_user_id']===(int)$r->user()->id,422,'Select another recipient.');
  DB::table('teacher_messages')->insert([
   'school_id'=>$school,'sender_user_id'=>$r->user()->id,
   'recipient_user_id'=>$data['recipient_user_id'],'subject'=>$data['subject'],'body'=>$data['body'],
   'created_at'=>now(),'updated_at'=>now()
  ]);
  return redirect()->route('teacher.communication.sent')->with('success','Message sent within ERP.');
 }
 public function showMessage(Request $r,int $message) {
  $row=DB::table('teacher_messages')->where('school_id',$this->school($r))->where('id',$message)
   ->where(function($q)use($r){$q->where('sender_user_id',$r->user()->id)->orWhere('recipient_user_id',$r->user()->id);})->first();
  abort_unless($row,404);
  if($row->recipient_user_id===$r->user()->id && !$row->read_at)
   DB::table('teacher_messages')->where('id',$row->id)->where('recipient_user_id',$r->user()->id)->update(['read_at'=>now(),'updated_at'=>now()]);
  return view('teacher.communication.message',compact('row'));
 }
 public function notices(Request $r,string $type) {
  abort_unless(in_array($type,['circular','broadcast','newsletter'],true),404);
  $notices=$this->noticeScope($r)->where('type',$type)->orderByDesc('published_on')->orderByDesc('id')->paginate(20);
  return view('teacher.communication.notices',compact('notices','type'));
 }
 public function createNotice(Request $r,string $type) {
  abort_unless(in_array($type,['circular','broadcast','newsletter'],true),404);
  $assignments=$this->teacherSections($r);
  return view('teacher.communication.notice-form',compact('type','assignments'));
 }
 public function storeNotice(Request $r,string $type) {
  abort_unless(in_array($type,['circular','broadcast','newsletter'],true),404);
  $data=$r->validate([
   'title'=>'required|string|max:180','body'=>'required|string|max:10000',
   'published_on'=>'required|date','section_id'=>'nullable|integer'
  ]);
  $year=null;$class=null;$section=null;
  if($type==='broadcast'){
   $r->validate(['section_id'=>'required|integer']);
   $a=$this->teacherSections($r)->firstWhere('section_id',(int)$data['section_id']);
   abort_unless($a,403,'This section is not assigned to you.');
   $year=$a->academic_year_id;$class=$a->school_class_id;$section=$a->section_id;
  }else{
   abort_unless($this->isManager($r),403,'Only school administrators can publish school-wide notices.');
  }
  DB::table('teacher_notices')->insert([
   'school_id'=>$this->school($r),'created_by'=>$r->user()->id,'type'=>$type,
   'title'=>$data['title'],'body'=>$data['body'],'published_on'=>$data['published_on'],
   'academic_year_id'=>$year,'school_class_id'=>$class,'section_id'=>$section,
   'created_at'=>now(),'updated_at'=>now()
  ]);
  return redirect()->route('teacher.communication.notices',$type)->with('success','Notice published in ERP.');
 }
 public function remarks(Request $r) {
  $ids=$this->allowedEnrollments($r)->pluck('id');
  $remarks=DB::table('teacher_student_remarks')->where('school_id',$this->school($r))
   ->whereIn('student_enrollment_id',$ids)->where('teacher_user_id',$r->user()->id)
   ->orderByDesc('id')->paginate(20);
  $students=StudentEnrollment::with('student')->whereIn('id',$ids)->get();
  return view('teacher.communication.remarks',compact('remarks','students'));
 }
 public function storeRemark(Request $r) {
  $data=$r->validate([
   'student_enrollment_id'=>'required|integer',
   'category'=>['required',Rule::in(['academic','behavior','appreciation','other'])],
   'remark'=>'required|string|max:3000','remark_date'=>'required|date'
  ]);
  abort_unless($this->allowedEnrollments($r)->whereKey($data['student_enrollment_id'])->exists(),403);
  DB::table('teacher_student_remarks')->insert([
   'school_id'=>$this->school($r),'teacher_user_id'=>$r->user()->id,
   ...$data,'created_at'=>now(),'updated_at'=>now()
  ]);
  return redirect()->route('teacher.communication.remarks')->with('success','Student remark saved.');
 }
}
