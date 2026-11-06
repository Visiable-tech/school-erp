<?php
namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TeacherPayrollExtensionController extends Controller {
 private function school(Request $r):int {
  abort_unless($r->user()?->school_id,403,'School assignment required');
  return (int)$r->user()->school_id;
 }
 private function admin(Request $r):void {
  abort_unless($r->user()->hasAnyRole(['Admin','Super Admin']),403);
 }
 private function isAdmin(Request $r):bool {
  return $r->user()->hasAnyRole(['Admin','Super Admin']);
 }
 private function types():array {return ['casual','sick','earned','unpaid','other'];}
 private function data(Request $r,string $table) {
  $q=DB::table($table)->where('school_id',$this->school($r));
  if(!$this->isAdmin($r))$q->where('user_id',$r->user()->id);
  return $q;
 }
 public function balances(Request $r) {
  $year=(int)$r->input('year',date('Y'));
  abort_unless($year>=2000 && $year<=2100,422);
  $school=$this->school($r);
  $userId=$this->isAdmin($r)?(int)$r->input('user_id',$r->user()->id):$r->user()->id;
  abort_unless(User::where('school_id',$school)->whereKey($userId)->exists(),404);
  $users=$this->isAdmin($r)?User::where('school_id',$school)->role('Teacher')->orderBy('name')->get(['id','name']):collect();
  $entitlements=DB::table('teacher_leave_entitlements')->where('school_id',$school)->where('user_id',$userId)->where('year',$year)->pluck('entitled_days','leave_type');
  $approved=DB::table('teacher_staff_leave_requests')->where('school_id',$school)->where('user_id',$userId)
   ->where('status','approved')->whereDate('start_date','<=',"$year-12-31")->whereDate('end_date','>=',"$year-01-01")->get();
  $used=[];
  foreach($approved as $leave) {
   $start=Carbon::parse($leave->start_date)->max(Carbon::create($year,1,1));
   $end=Carbon::parse($leave->end_date)->min(Carbon::create($year,12,31));
   $used[$leave->leave_type]=($used[$leave->leave_type]??0)+$start->diffInDays($end)+1;
  }
  return view('teacher.payroll.balances',compact('year','userId','users','entitlements','used')+['types'=>$this->types(),'isAdmin'=>$this->isAdmin($r)]);
 }
 public function saveEntitlement(Request $r) {
  $this->admin($r);$school=$this->school($r);
  $data=$r->validate([
   'user_id'=>['required','integer',Rule::exists('users','id')->where('school_id',$school)],
   'year'=>'required|integer|between:2000,2100',
   'leave_type'=>['required',Rule::in($this->types())],
   'entitled_days'=>'required|numeric|min:0|max:365'
  ]);
  abort_unless(User::whereKey($data['user_id'])->role('Teacher')->exists(),422,'Select a teacher');
  DB::table('teacher_leave_entitlements')->updateOrInsert([
   'school_id'=>$school,'user_id'=>$data['user_id'],'year'=>$data['year'],'leave_type'=>$data['leave_type']
  ],['entitled_days'=>$data['entitled_days'],'updated_at'=>now()]);
  return redirect()->route('teacher.payroll.balances',['year'=>$data['year'],'user_id'=>$data['user_id']])->with('success','Entitlement saved');
 }
 public function investments(Request $r) {
  $records=$this->data($r,'teacher_investment_declarations')->orderByDesc('id')->paginate(20);
  return view('teacher.payroll.requests',compact('records')+['kind'=>'investment','isAdmin'=>$this->isAdmin($r)]);
 }
 public function saveInvestment(Request $r) {
  $data=$r->validate([
   'financial_year'=>['required','regex:/^20\d{2}-\d{2}$/'],
   'category'=>'required|string|max:100',
   'declared_amount'=>'required|numeric|min:0.01|max:999999999.99',
   'description'=>'nullable|string|max:500'
  ]);
  $first=(int)substr($data['financial_year'],2,2);
  $last=(int)substr($data['financial_year'],5,2);
  abort_unless(($first+1)%100===$last,422,'Financial year must be consecutive (e.g. 2026-27).');
  DB::table('teacher_investment_declarations')->insert([
   'school_id'=>$this->school($r),'user_id'=>$r->user()->id,...$data,
   'status'=>'pending','created_at'=>now(),'updated_at'=>now()
  ]);
  return back()->with('success','Declaration submitted');
 }
 public function resignations(Request $r) {
  $records=$this->data($r,'teacher_resignation_requests')->orderByDesc('id')->paginate(20);
  return view('teacher.payroll.requests',compact('records')+['kind'=>'resignation','isAdmin'=>$this->isAdmin($r)]);
 }
 public function saveResignation(Request $r) {
  $data=$r->validate(['requested_last_day'=>'required|date|after_or_equal:today','reason'=>'required|string|max:5000']);
  abort_if(DB::table('teacher_resignation_requests')->where('school_id',$this->school($r))
   ->where('user_id',$r->user()->id)->where('status','pending')->exists(),422,'A resignation request is already pending.');
  DB::table('teacher_resignation_requests')->insert([
   'school_id'=>$this->school($r),'user_id'=>$r->user()->id,...$data,
   'status'=>'pending','created_at'=>now(),'updated_at'=>now()
  ]);
  return back()->with('success','Resignation request submitted');
 }
 public function review(Request $r,string $kind,int $id) {
  $this->admin($r);
  abort_unless(in_array($kind,['investment','resignation'],true),404);
  $data=$r->validate(['decision'=>['required',Rule::in(['approved','rejected'])],'review_note'=>'nullable|string|max:500']);
  $table=$kind==='investment'?'teacher_investment_declarations':'teacher_resignation_requests';
  DB::transaction(function()use($r,$table,$id,$data){
   $row=DB::table($table)->where('school_id',$this->school($r))->where('id',$id)->lockForUpdate()->first();
   abort_unless($row,404);
   abort_unless($row->status==='pending',422,'Already reviewed');
   abort_if((int)$row->user_id===(int)$r->user()->id,403,'Self-approval is prohibited');
   DB::table($table)->where('id',$id)->update([
    'status'=>$data['decision'],'review_note'=>$data['review_note']??null,
    'reviewed_by'=>$r->user()->id,'reviewed_at'=>now(),'updated_at'=>now()
   ]);
  });
  return back()->with('success','Request reviewed');
 }
 public function summary(Request $r) {
  $school=$this->school($r);
  $month=$r->input('month',date('Y-m'));
  abort_unless(is_string($month)&&preg_match('/^\d{4}-(0[1-9]|1[0-2])$/',$month),422,'Invalid month');
  $start=$month.'-01';$end=Carbon::parse($start)->endOfMonth()->toDateString();
  $users=$this->isAdmin($r)?User::where('school_id',$school)->role('Teacher')->orderBy('name')->get(['id','name']):
   User::whereKey($r->user()->id)->get(['id','name']);
  $rows=DB::table('teacher_staff_attendances')->where('school_id',$school)->whereBetween('attendance_date',[$start,$end])
   ->whereIn('user_id',$users->pluck('id'))->get()->groupBy('user_id');
  return view('teacher.payroll.summary',compact('month','users','rows'));
 }
}
