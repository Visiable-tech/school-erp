<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class HrPayrollMasterController extends Controller {
 private const CATEGORIES=[
  'leave-types'=>'Leave Types','designations'=>'Designations',
  'key-responsibility-areas'=>'Key Responsibility Areas',
  'other-income-heads'=>'Other Income Heads','kra-sections'=>'KRA Sections',
  'financial-years'=>'Financial Years','loan-advance-types'=>'Loan / Advance Types',
  'leave-policies'=>'Leave Policies','component-groups'=>'Component Groups',
  'salary-components'=>'Salary Components','packages'=>'Salary Packages',
  'configurations'=>'Configurations','leave-approval-process'=>'Leave Approval Process',
  'attendance-change-approval'=>'Attendance Change Approval Process',
  'kra-groups'=>'KRA Groups','overtime-configurations'=>'Overtime Configurations',
  'notification-configurations'=>'Auto Notification Configurations',
  'variable-compensation-rates'=>'Variable Compensation Rates',
  'increment-groups'=>'Increment Groups',
 ];
 private function school(Request $r):int {
  abort_unless($r->user()?->school_id,403,'Assign your user to a school');
  abort_unless($r->user()->hasAnyRole(['Admin','Super Admin']),403,'HR administrator access required');
  return (int)$r->user()->school_id;
 }
 private function config(string $category):array {
  if($category==='statuses')return ['hr_attendance_statuses','Attendance Statuses'];
  if($category==='shifts')return ['hr_shift_definitions','Shifts'];
  abort_unless(isset(self::CATEGORIES[$category]),404);
  return ['hr_master_options',self::CATEGORIES[$category]];
 }
 private function query(Request $r,string $category) {
  [$table]=$this->config($category);
  $q=DB::table($table)->where('school_id',$this->school($r));
  if($table==='hr_master_options')$q->where('category',$category);
  return $q;
 }
 public function index(Request $r,string $category) {
  [$table,$title]=$this->config($category);
  $q=$this->query($r,$category);
  if($r->filled('search')){
   $search=mb_substr((string)$r->input('search'),0,100);
   $q->where(function($x)use($search){$x->where('name','like',"%{$search}%")->orWhere('code','like',"%{$search}%");});
  }
  $items=$q->orderBy('name')->paginate(25)->withQueryString();
  return view('hr-payroll.masters.index',compact('items','title','category','table'));
 }
 public function create(Request $r,string $category) {
  $this->school($r);[$table,$title]=$this->config($category);
  $item=null;
  return view('hr-payroll.masters.form',compact('table','title','category','item'));
 }
 public function edit(Request $r,string $category,int $id) {
  [$table,$title]=$this->config($category);
  $item=$this->query($r,$category)->where('id',$id)->first();
  abort_unless($item,404);
  return view('hr-payroll.masters.form',compact('table','title','category','item'));
 }
 private function validateData(Request $r,string $category,?int $id=null):array {
  [$table]=$this->config($category);
  $school=$this->school($r);
  $base=['name'=>'required|string|max:150','is_active'=>'required|boolean'];
  if($table==='hr_attendance_statuses'){
   return $r->validate($base+[
    'code'=>['required','string','max:15',Rule::unique($table)->where('school_id',$school)->ignore($id)],
    'count_as'=>['required',Rule::in(['present','absent','leave','late','off','half_day','on_duty','holiday','other'])],
    'extra_deduction_days'=>'required|numeric|between:0,31',
    'bg_colour'=>['nullable','regex:/^#[0-9a-fA-F]{6}$/'],
    'font_colour'=>['nullable','regex:/^#[0-9a-fA-F]{6}$/'],
    'document_required'=>'required|boolean',
    'monthly_request_limit'=>'nullable|integer|min:0|max:9999',
    'quarterly_request_limit'=>'nullable|integer|min:0|max:9999',
    'yearly_request_limit'=>'nullable|integer|min:0|max:9999',
   ]);
  }
  if($table==='hr_shift_definitions'){
   return $r->validate($base+[
    'code'=>['required','string','max:20',Rule::unique($table)->where('school_id',$school)->ignore($id)],
    'start_time'=>'required|date_format:H:i','end_time'=>'required|date_format:H:i',
    'grace_minutes'=>'required|integer|between:0,600'
   ]);
  }
  return $r->validate($base+[
   'code'=>'nullable|string|max:40',
   'description'=>'nullable|string|max:3000',
  ]);
 }
 public function store(Request $r,string $category) {
  [$table]=$this->config($category);
  $data=$this->validateData($r,$category);
  $data['school_id']=$this->school($r);
  if($table==='hr_master_options')$data['category']=$category;
  $data['created_at']=now();$data['updated_at']=now();
  DB::table($table)->insert($data);
  return redirect()->route('hr-payroll.masters.index',$category)->with('success','Record added successfully.');
 }
 public function update(Request $r,string $category,int $id) {
  [$table]=$this->config($category);
  $q=$this->query($r,$category)->where('id',$id);
  abort_unless($q->exists(),404);
  $data=$this->validateData($r,$category,$id);
  $data['updated_at']=now();$q->update($data);
  return redirect()->route('hr-payroll.masters.index',$category)->with('success','Record updated successfully.');
 }
 public function destroy(Request $r,string $category,int $id) {
  $q=$this->query($r,$category)->where('id',$id);
  abort_unless($q->exists(),404);
  // Soft-deactivate reference/master data to preserve historical semantics.
  $q->update(['is_active'=>false,'updated_at'=>now()]);
  return back()->with('success','Record deactivated.');
 }
}
