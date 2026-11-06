<?php
namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class HrPayrollProcessingController extends Controller {
 private const MONEY=['basic','hra','allowance','other_earning','pf','professional_tax','tds','other_deduction'];
 private function school(Request $r):int {abort_unless($r->user()?->school_id,403,'School assignment required');return (int)$r->user()->school_id;}
 private function admin(Request $r):void {abort_unless($r->user()->hasAnyRole(['Admin','Super Admin']),403);}
 private function staff(int $school){return User::where('school_id',$school)->orderBy('name')->get(['id','name','email']);}
 private function validUser(int $school,int $id):void {abort_unless(User::where('school_id',$school)->whereKey($id)->exists(),422,'Invalid employee for school');}
 private function month(Request $r):string {$m=(string)$r->input('pay_month',date('Y-m'));abort_unless(preg_match('/^\d{4}-(0[1-9]|1[0-2])$/',$m),422,'Invalid pay month');return $m;}
 private function toPaise($value):int {return (int)round(((float)$value)*100);}
 private function amount(int $paise):string {return number_format($paise/100,2,'.','');}
 private function draftOnly(int $school,string $month):void {
  abort_if(DB::table('hr_payroll_runs')->where('school_id',$school)->where('pay_month',$month)->where('status','finalized')->exists(),422,'Payroll is finalized for this month');
 }
 public function profiles(Request $r){$this->admin($r);$school=$this->school($r);
  $profiles=DB::table('hr_salary_profiles as p')->join('users as u','u.id','=','p.user_id')->where('p.school_id',$school)->select('p.*','u.name')->orderBy('u.name')->paginate(30);
  $staff=$this->staff($school);return view('hr-payroll.processing.profiles',compact('profiles','staff'));
 }
 public function saveProfile(Request $r){$this->admin($r);$school=$this->school($r);
  $rules=['user_id'=>['required','integer',Rule::exists('users','id')->where('school_id',$school)],'is_active'=>'required|boolean'];
  foreach(self::MONEY as $col)$rules[$col]='required|numeric|between:0,9999999999.99';
  $d=$r->validate($rules);$this->validUser($school,(int)$d['user_id']);
  DB::table('hr_salary_profiles')->updateOrInsert(['school_id'=>$school,'user_id'=>$d['user_id']],array_merge($d,['updated_at'=>now()]));
  return back()->with('success','Salary structure saved. Existing finalized payslips remain unchanged.');
 }
 public function advances(Request $r){$this->admin($r);$school=$this->school($r);
  $advances=DB::table('hr_salary_advances as a')->join('users as u','u.id','=','a.user_id')
   ->leftJoin('hr_advance_recoveries as rec',function($j){$j->on('rec.salary_advance_id','=','a.id');})
   ->where('a.school_id',$school)->groupBy('a.id','u.name')
   ->select('a.*','u.name',DB::raw('COALESCE(SUM(rec.amount),0) as recovered'))
   ->orderByDesc('a.id')->paginate(30);
  $staff=$this->staff($school);return view('hr-payroll.processing.advances',compact('advances','staff'));
 }
 public function saveAdvance(Request $r){$this->admin($r);$school=$this->school($r);
  $d=$r->validate(['user_id'=>['required','integer',Rule::exists('users','id')->where('school_id',$school)],
   'amount'=>'required|numeric|between:0.01,9999999999.99','monthly_recovery'=>'required|numeric|between:0.01,9999999999.99','reason'=>'nullable|string|max:500']);
  abort_if($this->toPaise($d['monthly_recovery'])>$this->toPaise($d['amount']),422,'Recovery cannot exceed advance');
  DB::table('hr_salary_advances')->insert(['school_id'=>$school,'user_id'=>$d['user_id'],'amount'=>$d['amount'],
   'monthly_recovery'=>$d['monthly_recovery'],'reason'=>$d['reason']??null,'status'=>'pending','created_at'=>now(),'updated_at'=>now()]);
  return back()->with('success','Advance request recorded; approval does not disburse funds automatically.');
 }
 public function reviewAdvance(Request $r,int $id){$this->admin($r);$school=$this->school($r);
  $d=$r->validate(['decision'=>['required',Rule::in(['approved','rejected'])]]);
  DB::transaction(function()use($school,$id,$d,$r){
   $q=DB::table('hr_salary_advances')->where('school_id',$school)->where('id',$id);
   $a=$q->lockForUpdate()->first();abort_unless($a,404);abort_unless($a->status==='pending',422,'Advance already reviewed');
   $q->update(['status'=>$d['decision'],'approved_on'=>$d['decision']==='approved'?now()->toDateString():null,
    'reviewed_by'=>$r->user()->id,'updated_at'=>now()]);
  });return back()->with('success','Advance reviewed.');
 }
 public function adjustments(Request $r){$this->admin($r);$school=$this->school($r);$month=$this->month($r);
  $items=DB::table('hr_payroll_adjustments as a')->join('users as u','u.id','=','a.user_id')->where('a.school_id',$school)->where('a.pay_month',$month)
   ->select('a.*','u.name')->orderBy('u.name')->paginate(30)->withQueryString();$staff=$this->staff($school);
  return view('hr-payroll.processing.adjustments',compact('items','staff','month'));
 }
 public function saveAdjustment(Request $r){$this->admin($r);$school=$this->school($r);
  $d=$r->validate(['user_id'=>['required','integer',Rule::exists('users','id')->where('school_id',$school)],
   'pay_month'=>'required|date_format:Y-m','type'=>['required',Rule::in(['earning','deduction'])],
   'label'=>'required|string|max:150','amount'=>'required|numeric|between:0.01,9999999999.99']);
  $this->draftOnly($school,$d['pay_month']);
  DB::table('hr_payroll_adjustments')->insert(array_merge($d,['school_id'=>$school,'created_at'=>now(),'updated_at'=>now()]));
  return back()->with('success','Adjustment saved. Regenerate draft payroll to include it.');
 }
 public function deleteAdjustment(Request $r,int $id){$this->admin($r);$school=$this->school($r);
  $q=DB::table('hr_payroll_adjustments')->where('school_id',$school)->where('id',$id);$item=$q->first();abort_unless($item,404);
  $this->draftOnly($school,$item->pay_month);$q->delete();return back()->with('success','Adjustment deleted. Regenerate draft payroll.');
 }
 public function runs(Request $r){$this->admin($r);$school=$this->school($r);
  $runs=DB::table('hr_payroll_runs as r')->leftJoin('hr_payslips as p','p.payroll_run_id','=','r.id')
   ->where('r.school_id',$school)->groupBy('r.id')->select('r.*',DB::raw('COUNT(p.id) as employee_count'),DB::raw('COALESCE(SUM(p.net_salary),0) as net_total'))
   ->orderByDesc('r.pay_month')->paginate(24);return view('hr-payroll.processing.runs',compact('runs'));
 }
 public function generate(Request $r){$this->admin($r);$school=$this->school($r);
  $d=$r->validate(['pay_month'=>'required|date_format:Y-m','working_days'=>'required|integer|between:1,31',
   'default_paid_days'=>'required|integer|between:0,31']);
  abort_if($d['default_paid_days']>$d['working_days'],422,'Paid days cannot exceed working days');
  $month=$d['pay_month'];
  DB::transaction(function()use($r,$school,$d,$month){
   DB::table('schools')->where('id',$school)->lockForUpdate()->first();
   $run=DB::table('hr_payroll_runs')->where('school_id',$school)->where('pay_month',$month)->lockForUpdate()->first();
   abort_if($run && $run->status==='finalized',422,'Finalized payroll cannot be regenerated');
   if(!$run){$id=DB::table('hr_payroll_runs')->insertGetId(['school_id'=>$school,'pay_month'=>$month,'working_days'=>$d['working_days'],
    'status'=>'draft','created_by'=>$r->user()->id,'created_at'=>now(),'updated_at'=>now()]);}
   else{$id=$run->id;DB::table('hr_payroll_runs')->where('id',$id)->update(['working_days'=>$d['working_days'],'updated_at'=>now()]);}
   $oldIds=DB::table('hr_payslips')->where('payroll_run_id',$id)->pluck('id');
   if($oldIds->isNotEmpty())DB::table('hr_advance_recoveries')->whereIn('payslip_id',$oldIds)->delete();
   DB::table('hr_payslips')->where('payroll_run_id',$id)->delete();
   $profiles=DB::table('hr_salary_profiles as p')->join('users as u','u.id','=','p.user_id')
    ->where('p.school_id',$school)->where('u.school_id',$school)->where('p.is_active',1)->select('p.*')->get();
   abort_if($profiles->isEmpty(),422,'No active salary profiles. Add employee salary structures first.');
   foreach($profiles as $p){
    $earn=[];$ded=[];
    foreach(['basic','hra','allowance','other_earning'] as $col)$earn[$col]=$this->toPaise($p->$col);
    foreach(['pf','professional_tax','tds','other_deduction'] as $col)$ded[$col]=$this->toPaise($p->$col);
    $override=DB::table('hr_employee_paid_day_overrides')->where('school_id',$school)
     ->where('user_id',$p->user_id)->where('pay_month',$month)->first();
    abort_unless($override,422,'Missing reviewed paid-days record for employee #'.$p->user_id.'. Complete Phase 3 Paid Days first.');
    $employeePaidDays=(float)$override->paid_days;
    abort_if($employeePaidDays<0 || $employeePaidDays>$d['working_days'],422,'Invalid paid days for employee #'.$p->user_id);
    $gross=array_sum($earn);$prorated=(int)round($gross*$employeePaidDays/$d['working_days']);
    $baseDed=array_sum($ded);
    $adj=DB::table('hr_payroll_adjustments')->where('school_id',$school)->where('user_id',$p->user_id)->where('pay_month',$month)->get();
    $extraEarn=0;$extraDed=0;$adjustmentLines=[];
    foreach($adj as $a){$v=$this->toPaise($a->amount);if($a->type==='earning')$extraEarn+=$v;else $extraDed+=$v;
     $adjustmentLines[]=['label'=>$a->label,'type'=>$a->type,'amount'=>$this->amount($v)];}
    $advances=DB::table('hr_salary_advances')->where('school_id',$school)->where('user_id',$p->user_id)->where('status','approved')->orderBy('id')->get();
    $recoveries=[];$recovered=0;
    $recoverable=max(0,$prorated+$extraEarn-$baseDed-$extraDed);
    foreach($advances as $a){
     $already=$this->toPaise(DB::table('hr_advance_recoveries')->where('salary_advance_id',$a->id)->sum('amount'));
     $balance=max(0,$this->toPaise($a->amount)-$already);
     $take=min($balance,$this->toPaise($a->monthly_recovery),max(0,$recoverable-$recovered));
     if($take>0){$recoveries[]=['advance_id'=>$a->id,'amount'=>$take];$recovered+=$take;}
    }
    $net=$prorated+$extraEarn-$baseDed-$extraDed-$recovered;
    $breakdown=['earnings'=>array_map(fn($x)=>$this->amount($x),$earn),
     'deductions'=>array_map(fn($x)=>$this->amount($x),$ded),'adjustments'=>$adjustmentLines];
    $payslipId=DB::table('hr_payslips')->insertGetId(['school_id'=>$school,'payroll_run_id'=>$id,'user_id'=>$p->user_id,
     'pay_month'=>$month,'working_days'=>$d['working_days'],'paid_days'=>$employeePaidDays,
     'gross_salary'=>$this->amount($gross),'prorated_earnings'=>$this->amount($prorated),
     'adjustment_earnings'=>$this->amount($extraEarn),'base_deductions'=>$this->amount($baseDed),
     'adjustment_deductions'=>$this->amount($extraDed),'advance_recovery'=>$this->amount($recovered),
     'net_salary'=>$this->amount($net),'breakdown'=>json_encode($breakdown),'created_at'=>now(),'updated_at'=>now()]);
    foreach($recoveries as $rec)DB::table('hr_advance_recoveries')->insert(['school_id'=>$school,'salary_advance_id'=>$rec['advance_id'],
     'payslip_id'=>$payslipId,'amount'=>$this->amount($rec['amount']),'created_at'=>now(),'updated_at'=>now()]);
   }
  });return redirect()->route('hr-payroll.processing.runs')->with('success','Draft payroll generated. Review every payslip before finalizing.');
 }
 public function finalize(Request $r,int $id){$this->admin($r);$school=$this->school($r);
  DB::transaction(function()use($r,$school,$id){
   $q=DB::table('hr_payroll_runs')->where('school_id',$school)->where('id',$id);
   $run=$q->lockForUpdate()->first();abort_unless($run,404);abort_unless($run->status==='draft',422,'Already finalized');
   abort_unless(DB::table('hr_payslips')->where('payroll_run_id',$id)->exists(),422,'No payslips in run');
   $q->update(['status'=>'finalized','finalized_by'=>$r->user()->id,'finalized_at'=>now(),'updated_at'=>now()]);
  });return back()->with('success','Payroll finalized. Payslips are now locked.');
 }
 public function slips(Request $r){$school=$this->school($r);$isAdmin=$r->user()->hasAnyRole(['Admin','Super Admin']);
  $q=DB::table('hr_payslips as p')->join('users as u','u.id','=','p.user_id')->join('hr_payroll_runs as r','r.id','=','p.payroll_run_id')
   ->where('p.school_id',$school)->select('p.*','u.name','r.status as run_status');
  if(!$isAdmin)$q->where('p.user_id',$r->user()->id)->where('r.status','finalized');
  if($r->filled('pay_month')){$month=$this->month($r);$q->where('p.pay_month',$month);}
  $slips=$q->orderByDesc('p.pay_month')->orderBy('u.name')->paginate(30)->withQueryString();
  return view('hr-payroll.processing.slips',compact('slips','isAdmin'));
 }
 public function showSlip(Request $r,int $id){$school=$this->school($r);$isAdmin=$r->user()->hasAnyRole(['Admin','Super Admin']);
  $q=DB::table('hr_payslips as p')->join('users as u','u.id','=','p.user_id')->join('hr_payroll_runs as r','r.id','=','p.payroll_run_id')
   ->where('p.school_id',$school)->where('p.id',$id)->select('p.*','u.name','u.email','r.status as run_status');
  if(!$isAdmin)$q->where('p.user_id',$r->user()->id)->where('r.status','finalized');
  $slip=$q->first();abort_unless($slip,404);$details=json_decode($slip->breakdown,true)?:[];
  return view('hr-payroll.processing.payslip',compact('slip','details'));
 }
}
