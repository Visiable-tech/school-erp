<?php
namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TeacherStaffAttendanceController extends Controller
{
 private function school(Request $r):int {
  abort_unless($r->user() && $r->user()->school_id,403,'School assignment required.');
  return (int)$r->user()->school_id;
 }
 private function manager(Request $r):void {
  abort_unless($r->user()->hasAnyRole(['Admin','Super Admin']),403,'Administrator access required.');
 }
 private function statuses():array {return ['present','absent','late','half_day','on_leave'];}
 public function daily(Request $r) {
  $school=$this->school($r);
  $date=$r->validate(['date'=>'nullable|date'])['date']??now()->toDateString();
  $isManager=$r->user()->hasAnyRole(['Admin','Super Admin']);
  $users=$isManager
   ? User::where('school_id',$school)->role('Teacher')->orderBy('name')->get(['id','name'])
   : User::whereKey($r->user()->id)->get(['id','name']);
  $records=DB::table('teacher_staff_attendances')->where('school_id',$school)
   ->whereDate('attendance_date',$date)->whereIn('user_id',$users->pluck('id'))->get()->keyBy('user_id');
  return view('teacher.staff-attendance.daily',compact('users','records','date','isManager'));
 }
 public function mark(Request $r) {
  $this->manager($r);$school=$this->school($r);
  $data=$r->validate([
   'user_id'=>['required','integer',Rule::exists('users','id')->where('school_id',$school)],
   'attendance_date'=>'required|date','status'=>['required',Rule::in($this->statuses())],
   'note'=>'nullable|string|max:500'
  ]);
  abort_unless(User::whereKey($data['user_id'])->role('Teacher')->exists(),422,'Select a teacher account.');
  DB::table('teacher_staff_attendances')->updateOrInsert(
   ['school_id'=>$school,'user_id'=>$data['user_id'],'attendance_date'=>$data['attendance_date']],
   ['status'=>$data['status'],'note'=>$data['note']??null,'marked_by'=>$r->user()->id,'updated_at'=>now()]
  );
  return back()->with('success','Attendance saved.');
 }
 public function monthly(Request $r) {
  $school=$this->school($r);
  $month=$r->input('month',now()->format('Y-m'));
  abort_unless(preg_match('/^\d{4}-(0[1-9]|1[0-2])$/',$month),422,'Invalid month.');
  $start=Carbon::createFromFormat('!Y-m',$month)->startOfMonth()->toDateString();
  $end=Carbon::parse($start)->endOfMonth()->toDateString();
  $isManager=$r->user()->hasAnyRole(['Admin','Super Admin']);
  $users=$isManager
   ? User::where('school_id',$school)->role('Teacher')->orderBy('name')->get(['id','name'])
   : User::whereKey($r->user()->id)->get(['id','name']);
  $records=DB::table('teacher_staff_attendances')->where('school_id',$school)
   ->whereBetween('attendance_date',[$start,$end])->whereIn('user_id',$users->pluck('id'))
   ->orderBy('attendance_date')->get()->groupBy('user_id');
  return view('teacher.staff-attendance.monthly',compact('month','users','records','isManager'));
 }
 public function leaves(Request $r) {
  $school=$this->school($r);
  $q=DB::table('teacher_staff_leave_requests')->where('school_id',$school);
  if(!$r->user()->hasAnyRole(['Admin','Super Admin']))$q->where('user_id',$r->user()->id);
  $requests=$q->orderByDesc('id')->paginate(20);
  return view('teacher.staff-attendance.requests',compact('requests')+['kind'=>'leave']);
 }
 public function requestLeave(Request $r) {
  $data=$r->validate([
   'start_date'=>'required|date','end_date'=>'required|date|after_or_equal:start_date',
   'leave_type'=>['required',Rule::in(['casual','sick','earned','unpaid','other'])],
   'reason'=>'required|string|max:3000'
  ]);
  abort_if(Carbon::parse($data['start_date'])->diffInDays(Carbon::parse($data['end_date']))>90,422,'Maximum 91 days per request.');
  DB::table('teacher_staff_leave_requests')->insert([
   'school_id'=>$this->school($r),'user_id'=>$r->user()->id,...$data,
   'status'=>'pending','created_at'=>now(),'updated_at'=>now()
  ]);
  return back()->with('success','Leave request submitted.');
 }
 public function changes(Request $r) {
  $school=$this->school($r);
  $q=DB::table('teacher_staff_attendance_changes')->where('school_id',$school);
  if(!$r->user()->hasAnyRole(['Admin','Super Admin']))$q->where('user_id',$r->user()->id);
  $requests=$q->orderByDesc('id')->paginate(20);
  return view('teacher.staff-attendance.requests',compact('requests')+['kind'=>'change']);
 }
 public function requestChange(Request $r) {
  $data=$r->validate([
   'attendance_date'=>'required|date|before_or_equal:today',
   'requested_status'=>['required',Rule::in($this->statuses())],
   'reason'=>'required|string|max:3000'
  ]);
  DB::table('teacher_staff_attendance_changes')->insert([
   'school_id'=>$this->school($r),'user_id'=>$r->user()->id,...$data,
   'status'=>'pending','created_at'=>now(),'updated_at'=>now()
  ]);
  return back()->with('success','Attendance change request submitted.');
 }
 public function review(Request $r,string $kind,int $id) {
  $this->manager($r);
  abort_unless(in_array($kind,['leave','change'],true),404);
  $data=$r->validate([
   'decision'=>['required',Rule::in(['approved','rejected'])],
   'review_note'=>'nullable|string|max:500'
  ]);
  $table=$kind==='leave'?'teacher_staff_leave_requests':'teacher_staff_attendance_changes';
  DB::transaction(function()use($r,$kind,$id,$data,$table){
   $row=DB::table($table)->where('school_id',$this->school($r))->where('id',$id)->lockForUpdate()->first();
   abort_unless($row,404);abort_unless($row->status==='pending',422,'Request already reviewed.');
   abort_if((int)$row->user_id===(int)$r->user()->id,403,'You cannot approve your own request.');
   DB::table($table)->where('id',$id)->update([
    'status'=>$data['decision'],'review_note'=>$data['review_note']??null,
    'reviewed_by'=>$r->user()->id,'reviewed_at'=>now(),'updated_at'=>now()
   ]);
   if($kind==='change' && $data['decision']==='approved') {
    DB::table('teacher_staff_attendances')->updateOrInsert(
     ['school_id'=>$row->school_id,'user_id'=>$row->user_id,'attendance_date'=>$row->attendance_date],
     ['status'=>$row->requested_status,'note'=>'Approved correction #'.$row->id,'marked_by'=>$r->user()->id,'updated_at'=>now()]
    );
   }
  });
  return back()->with('success','Request '.$data['decision'].'.');
 }
}
