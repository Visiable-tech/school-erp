<?php
namespace App\Http\Controllers;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class HrAttendancePhase3Controller extends Controller {
 private function school(Request $r):int {
  abort_unless($r->user()?->school_id,403,'School assignment required');
  abort_unless($r->user()->hasAnyRole(['Admin','Super Admin']),403);
  return (int)$r->user()->school_id;
 }
 private function staff(int $school) {
  return User::where('school_id',$school)->orderBy('name')->get(['id','name']);
 }
 private function validUser(int $school,int $id):void {
  abort_unless(User::where('school_id',$school)->whereKey($id)->exists(),422,'Employee not found in this school');
 }
 private function month(string $m):string {
  abort_unless(preg_match('/^\d{4}-(0[1-9]|1[0-2])$/',$m),422,'Invalid month');
  return $m;
 }
 private function draftOnly(int $school,string $month):void {
  abort_if(DB::table('hr_payroll_runs')->where('school_id',$school)->where('pay_month',$month)->where('status','finalized')->exists(),422,'Finalized payroll cannot be modified.');
 }
 public function attendance(Request $r) {
  $school=$this->school($r);$date=$r->input('date',date('Y-m-d'));
  abort_unless(preg_match('/^\d{4}-\d{2}-\d{2}$/',$date),422);
  $staff=$this->staff($school);
  $statuses=DB::table('hr_attendance_statuses')->where('school_id',$school)->where('is_active',1)->orderBy('name')->get();
  $entries=DB::table('hr_daily_attendance as a')->join('users as u','u.id','=','a.user_id')
   ->join('hr_attendance_statuses as s','s.id','=','a.attendance_status_id')
   ->where('a.school_id',$school)->where('a.attendance_date',$date)
   ->select('a.*','u.name as employee_name','s.name as status_name')->orderBy('u.name')->get();
  return view('hr-payroll.phase3.attendance',compact('date','staff','statuses','entries'));
 }
 public function saveAttendance(Request $r) {
  $school=$this->school($r);
  $d=$r->validate([
   'user_id'=>['required','integer',Rule::exists('users','id')->where('school_id',$school)],
   'attendance_date'=>'required|date',
   'attendance_status_id'=>['required','integer',Rule::exists('hr_attendance_statuses','id')->where('school_id',$school)],
   'check_in'=>'nullable|date_format:H:i','check_out'=>'nullable|date_format:H:i',
   'overtime_minutes'=>'required|integer|between:0,1440','note'=>'nullable|string|max:2000'
  ]);
  $this->validUser($school,(int)$d['user_id']);
  abort_unless(DB::table('hr_attendance_statuses')->where('id',$d['attendance_status_id'])->where('school_id',$school)->where('is_active',1)->exists(),422,'Inactive status');
  $month=substr($d['attendance_date'],0,7);$this->draftOnly($school,$month);
  DB::table('hr_daily_attendance')->updateOrInsert(
   ['school_id'=>$school,'user_id'=>$d['user_id'],'attendance_date'=>$d['attendance_date']],
   ['attendance_status_id'=>$d['attendance_status_id'],'check_in'=>$d['check_in']??null,
    'check_out'=>$d['check_out']??null,'overtime_minutes'=>$d['overtime_minutes'],
    'note'=>$d['note']??null,'marked_by'=>$r->user()->id,'updated_at'=>now()]
  );
  return back()->with('success','Attendance saved. Recalculate paid days before generating draft payroll.');
 }
 public function shifts(Request $r) {
  $school=$this->school($r);$staff=$this->staff($school);
  $shifts=DB::table('hr_shift_definitions')->where('school_id',$school)->where('is_active',1)->orderBy('name')->get();
  $assignments=DB::table('hr_employee_shift_assignments as a')
   ->join('users as u','u.id','=','a.user_id')
   ->join('hr_shift_definitions as s','s.id','=','a.shift_id')
   ->where('a.school_id',$school)->select('a.*','u.name as employee_name','s.name as shift_name')
   ->orderByDesc('a.effective_from')->paginate(30);
  return view('hr-payroll.phase3.shifts',compact('staff','shifts','assignments'));
 }
 public function saveShift(Request $r) {
  $school=$this->school($r);
  $d=$r->validate([
   'user_id'=>['required','integer',Rule::exists('users','id')->where('school_id',$school)],
   'shift_id'=>['required','integer',Rule::exists('hr_shift_definitions','id')->where('school_id',$school)],
   'effective_from'=>'required|date','effective_to'=>'nullable|date|after_or_equal:effective_from'
  ]);
  abort_unless(DB::table('hr_shift_definitions')->where('school_id',$school)->where('id',$d['shift_id'])->where('is_active',1)->exists(),422,'Inactive shift');
  DB::transaction(function()use($school,$d){
   DB::table('schools')->where('id',$school)->lockForUpdate()->first();
   $conflict=DB::table('hr_employee_shift_assignments')->where('school_id',$school)->where('user_id',$d['user_id'])
    ->where('effective_from','<=',$d['effective_to']??'9999-12-31')
    ->where(function($q)use($d){$q->whereNull('effective_to')->orWhere('effective_to','>=',$d['effective_from']);})->exists();
   abort_if($conflict,422,'Shift dates overlap an existing assignment.');
   DB::table('hr_employee_shift_assignments')->insert(['school_id'=>$school,'user_id'=>$d['user_id'],
    'shift_id'=>$d['shift_id'],'effective_from'=>$d['effective_from'],'effective_to'=>$d['effective_to']??null,
    'created_at'=>now(),'updated_at'=>now()]);
  });
  return back()->with('success','Shift assigned.');
 }
 public function overtime(Request $r) {
  $school=$this->school($r);$month=$this->month((string)$r->input('month',date('Y-m')));
  $rows=DB::table('hr_daily_attendance as a')->join('users as u','u.id','=','a.user_id')
   ->where('a.school_id',$school)->whereBetween('a.attendance_date',[$month.'-01',Carbon::parse($month.'-01')->endOfMonth()->toDateString()])
   ->where('a.overtime_minutes','>',0)
   ->select('a.attendance_date','u.name','a.overtime_minutes','a.note')->orderByDesc('a.attendance_date')->paginate(40)->withQueryString();
  return view('hr-payroll.phase3.overtime',compact('month','rows'));
 }
 public function paidDays(Request $r) {
  $school=$this->school($r);$month=$this->month((string)$r->input('month',date('Y-m')));
  $staff=$this->staff($school);
  $rows=DB::table('hr_employee_paid_day_overrides')->where('school_id',$school)->where('pay_month',$month)->get()->keyBy('user_id');
  $run=DB::table('hr_payroll_runs')->where('school_id',$school)->where('pay_month',$month)->first();
  return view('hr-payroll.phase3.paid-days',compact('month','staff','rows','run'));
 }
 public function calculatePaidDays(Request $r) {
  $school=$this->school($r);
  $d=$r->validate(['month'=>'required|date_format:Y-m','working_days'=>'required|integer|between:1,31']);
  $month=$this->month($d['month']);$start=$month.'-01';
  $end=Carbon::parse($start)->endOfMonth()->toDateString();
  $this->draftOnly($school,$month);
  $staff=DB::table('hr_salary_profiles')->where('school_id',$school)->where('is_active',1)->pluck('user_id');
  DB::transaction(function()use($school,$month,$start,$end,$staff,$d,$r){
   DB::table('schools')->where('id',$school)->lockForUpdate()->first();
   $this->draftOnly($school,$month);
   foreach($staff as $userId){
    $records=DB::table('hr_daily_attendance as a')
     ->join('hr_attendance_statuses as s','s.id','=','a.attendance_status_id')
     ->where('a.school_id',$school)->where('a.user_id',$userId)
     ->whereBetween('a.attendance_date',[$start,$end])->select('a.attendance_date','s.count_as')->get();
    $approvedLeaves=DB::table('teacher_staff_leave_requests')->where('school_id',$school)
     ->where('user_id',$userId)->where('status','approved')
     ->where('start_date','<=',$end)->where('end_date','>=',$start)->get();
    $approvedDays=[];
    foreach($approvedLeaves as $leave){
     $from=Carbon::parse(max($start,$leave->start_date));$to=Carbon::parse(min($end,$leave->end_date));
     for($day=$from->copy();$day->lte($to);$day->addDay())$approvedDays[$day->toDateString()]=true;
    }
    $units=0.0;$seen=[];
    foreach($records as $a){
     $seen[$a->attendance_date]=true;
     $units+=match($a->count_as){
      'present','late','on_duty','holiday','off'=>1.0,
      'half_day'=>0.5,
      'leave'=>isset($approvedDays[$a->attendance_date])?1.0:0.0,
      default=>0.0
     };
    }
    foreach($approvedDays as $day=>$_)if(!isset($seen[$day]))$units+=1.0;
    $paid=min((float)$d['working_days'],$units);
    DB::table('hr_employee_paid_day_overrides')->updateOrInsert(
     ['school_id'=>$school,'user_id'=>$userId,'pay_month'=>$month],
     ['paid_days'=>$paid,'set_by'=>$r->user()->id,
      'calculation_note'=>'Preliminary: attendance + approved leaves, capped at working days. Verify holidays, weekends, paid leave rules, and missing attendance manually.',
      'updated_at'=>now()]
    );
   }
  });
  return back()->with('success','Preliminary paid days calculated. Review every employee before payroll generation.');
 }
 public function overridePaidDays(Request $r) {
  $school=$this->school($r);
  $d=$r->validate(['user_id'=>['required','integer',Rule::exists('users','id')->where('school_id',$school)],
   'month'=>'required|date_format:Y-m','paid_days'=>'required|numeric|between:0,31',
   'working_days'=>'required|integer|between:1,31','calculation_note'=>'required|string|max:2000']);
  $this->draftOnly($school,$d['month']);
  abort_if((float)$d['paid_days']>(int)$d['working_days'],422,'Paid days exceed working days');
  DB::table('hr_employee_paid_day_overrides')->updateOrInsert(
   ['school_id'=>$school,'user_id'=>$d['user_id'],'pay_month'=>$d['month']],
   ['paid_days'=>$d['paid_days'],'set_by'=>$r->user()->id,'calculation_note'=>$d['calculation_note'],'updated_at'=>now()]
  );
  return back()->with('success','Paid days saved. Regenerate draft payroll to apply.');
 }
}
