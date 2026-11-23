<?php
namespace App\Http\Controllers;
use App\Models\{AcademicYear,FeeHead,StudentEnrollment,StudentFeeAssignment,StudentFeeDue,StudentTransportAssignment,TransportRoute,TransportRouteRate,TransportRouteStop,TransportStopRate};
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth,DB};
use Illuminate\Validation\ValidationException;
class TransportFeeController extends Controller {
 private function school(){return Auth::user()->school_id;}
 public function index()
 {
  $school = $this->school();
  $enrollments = StudentEnrollment::with('student')
      ->where('school_id', $school)->where('is_current', 1)->get();
  // Use the existing master tables; no new routes or migrations are needed.
  $classNames = DB::table('school_classes')->where('school_id', $school)->pluck('name', 'id');
  $sectionNames = DB::table('sections')->where('school_id', $school)->pluck('name', 'id');
  $enrollmentOptions = $enrollments->map(function ($e) use ($classNames, $sectionNames) {
      return [
          'id' => $e->id,
          'academic_year_id' => $e->academic_year_id,
          'school_class_id' => $e->school_class_id,
          'class_name' => $classNames[$e->school_class_id] ?? ('Class #'.$e->school_class_id),
          'section_id' => $e->section_id,
          'section_name' => $sectionNames[$e->section_id] ?? ('Section #'.$e->section_id),
          'student_name' => $e->student?->student_name ?? 'Student',
          'admission_no' => $e->student?->admission_no ?? (string) $e->student_id,
      ];
  })->values();
  return view('transport.index', [
      'routes' => TransportRoute::with(['rates','stops.rates'])->where('school_id',$school)->orderBy('name')->get(),
      'years' => AcademicYear::where('school_id',$school)->orderByDesc('start_date')->get(),
      'assignments' => StudentTransportAssignment::with('student','route','stop')->where('school_id',$school)->latest()->paginate(25),
      'enrollments' => $enrollments,
      'enrollmentOptions' => $enrollmentOptions,
  ]);
 }
 public function saveRoute(Request $r){$v=$r->validate(['route_id'=>'nullable|integer','code'=>'required|string|max:30','name'=>'required|string|max:255','academic_year_id'=>'required|integer','monthly_amount'=>'required|numeric|min:0','status'=>'nullable|boolean']);$school=$this->school();abort_unless(AcademicYear::where('school_id',$school)->whereKey($v['academic_year_id'])->exists(),422);DB::transaction(function()use($v,$school){$route=isset($v['route_id'])?TransportRoute::where('school_id',$school)->findOrFail($v['route_id']):new TransportRoute();$route->fill(['school_id'=>$school,'code'=>$v['code'],'name'=>$v['name'],'status'=>request()->boolean('status')])->save();TransportRouteRate::updateOrCreate(['transport_route_id'=>$route->id,'academic_year_id'=>$v['academic_year_id']],['monthly_amount'=>$v['monthly_amount']]);});return back()->with('success','Transport route and academic-year rate saved. Existing dues are unchanged.');}
 public function saveStop(Request $r){
  $v=$r->validate(['stop_id'=>'nullable|integer','transport_route_id'=>'required|integer','name'=>'required|string|max:255','sort_order'=>'required|integer|min:0','academic_year_id'=>'required|integer','monthly_amount'=>'required|numeric|min:0','status'=>'nullable|boolean']);
  $school=$this->school();$route=TransportRoute::where('school_id',$school)->findOrFail($v['transport_route_id']);
  AcademicYear::where('school_id',$school)->findOrFail($v['academic_year_id']);
  DB::transaction(function()use($v,$route){
   $stop=isset($v['stop_id'])?TransportRouteStop::where('transport_route_id',$route->id)->findOrFail($v['stop_id']):new TransportRouteStop();
   $stop->fill(['transport_route_id'=>$route->id,'name'=>$v['name'],'sort_order'=>$v['sort_order'],'status'=>request()->boolean('status')])->save();
   TransportStopRate::updateOrCreate(['transport_route_stop_id'=>$stop->id,'academic_year_id'=>$v['academic_year_id']],['monthly_amount'=>$v['monthly_amount']]);
  });return back()->with('success','Stop and academic-year monthly fee saved. Previously generated dues remain unchanged.');
 }
 public function assign(Request $r){$v=$r->validate(['assignment_id'=>'nullable|integer','student_enrollment_id'=>'required|integer','transport_route_id'=>'required|integer','transport_route_stop_id'=>'required|integer','starts_on'=>'required|date','ends_on'=>'nullable|date|after_or_equal:starts_on']);$school=$this->school();$enrollment=StudentEnrollment::where('school_id',$school)->findOrFail($v['student_enrollment_id']);$route=TransportRoute::where('school_id',$school)->where('status',1)->findOrFail($v['transport_route_id']);$year=AcademicYear::where('school_id',$school)->findOrFail($enrollment->academic_year_id);$stop=TransportRouteStop::where('transport_route_id',$route->id)->where('status',1)->findOrFail($v['transport_route_stop_id']);$rate=TransportStopRate::where('transport_route_stop_id',$stop->id)->where('academic_year_id',$year->id)->firstOrFail();$start=Carbon::parse($v['starts_on']);$end=Carbon::parse($v['ends_on']??$year->end_date);if($start->lt($year->start_date)||$end->gt($year->end_date))throw ValidationException::withMessages(['starts_on'=>'Dates must be within the enrollment academic year.']);DB::transaction(function()use($v,$school,$enrollment,$route,$stop,$rate,$year,$start,$end){
  $editing = !empty($v['assignment_id']);
  $a = $editing ? StudentTransportAssignment::where('school_id',$school)->lockForUpdate()->findOrFail($v['assignment_id']) : null;
  if ($a && $a->student_enrollment_id != $enrollment->id) {
   throw ValidationException::withMessages(['student_enrollment_id'=>'An existing assignment cannot be moved to another student.']);
  }
  $existing=StudentTransportAssignment::where('student_enrollment_id',$enrollment->id)->lockForUpdate()->get();
  foreach($existing as $other){
   if($a && $other->id === $a->id) continue;
   if($start->lte($other->ends_on??$year->end_date)&&$end->gte($other->starts_on))
    throw ValidationException::withMessages(['starts_on'=>'Transport assignment overlaps an existing period.']);
  }
  if ($a) {
   $dues = StudentFeeDue::where('student_transport_assignment_id',$a->id)->lockForUpdate()->get();
   $dueIds = $dues->pluck('id')->all();
   foreach ($dues as $due) {
    if ((float)$due->paid_amount > 0 || (float)$due->discount_amount > 0 || (float)$due->waiver_amount > 0 || (float)$due->fine_waiver_amount > 0 || (float)$due->fine_amount > 0 || $due->payment_status !== 'unpaid') {
     throw ValidationException::withMessages(['assignment_id'=>'This assignment has financially adjusted dues. Do not edit it directly; use a future-effective route change.']);
    }
   }
   if ($dueIds) {
    foreach (['fee_collection_items','fee_refund_items','student_composite_concession_dues','student_concession_assignment_dues','student_fee_waiver_assignment_dues','student_fine_waiver_assignment_dues'] as $table) {
     if (DB::table($table)->whereIn('student_fee_due_id',$dueIds)->exists()) {
      throw ValidationException::withMessages(['assignment_id'=>'Transport dues have linked financial records and cannot be replaced.']);
     }
    }
    StudentFeeDue::whereIn('id',$dueIds)->delete();
   }
  } else {
   $a = new StudentTransportAssignment();
  }
  $a->fill(['school_id'=>$school,'student_id'=>$enrollment->student_id,'student_enrollment_id'=>$enrollment->id,'academic_year_id'=>$year->id,'transport_route_id'=>$route->id,'transport_route_stop_id'=>$stop->id,'starts_on'=>$start,'ends_on'=>$end,'monthly_amount'=>$rate->monthly_amount]);
  $a->save();
  $this->generate($a);
 });return back()->with('success',!empty($v['assignment_id'])?'Transport assignment updated and untouched monthly dues regenerated.':'Route assigned and monthly transport dues generated.');}
 private function generate(StudentTransportAssignment $a):void {$assignment=StudentFeeAssignment::where('school_id',$a->school_id)->where('student_enrollment_id',$a->student_enrollment_id)->where('academic_year_id',$a->academic_year_id)->where('status',1)->latest('id')->first();if(!$assignment)throw ValidationException::withMessages(['student_enrollment_id'=>'Assign the student a regular Fee Template first, then assign transport.']);$head=FeeHead::where('school_id',$a->school_id)->where('code','TRANSPORT')->first();if(!$head)throw ValidationException::withMessages(['transport_route_id'=>'Create a Fee Component with code TRANSPORT in Fee Management first.']);$year=AcademicYear::findOrFail($a->academic_year_id);$from=$a->starts_on->copy()->startOfMonth();$last=($a->ends_on??$year->end_date)->copy()->startOfMonth();for($m=$from;$m->lte($last);$m->addMonth()){$start=$m->copy()->max($a->starts_on)->max($year->start_date);$end=$m->copy()->endOfMonth()->min($a->ends_on??$year->end_date)->min($year->end_date);if($start->gt($end))continue;StudentFeeDue::firstOrCreate(['student_transport_assignment_id'=>$a->id,'period_start'=>$start->toDateString()],['school_id'=>$a->school_id,'student_fee_assignment_id'=>$assignment->id,'student_id'=>$a->student_id,'student_enrollment_id'=>$a->student_enrollment_id,'academic_year_id'=>$a->academic_year_id,'fee_structure_id'=>$assignment->fee_structure_id,'fee_structure_item_id'=>null,'fee_installment_id'=>null,'fee_head_id'=>$head->id,'fee_head_name'=>'Transport Fee','installment_name'=>'Transport '.$m->format('M Y'),'period_end'=>$end->toDateString(),'due_date'=>$start->toDateString(),'base_amount'=>$a->monthly_amount,'discount_amount'=>0,'fine_amount'=>0,'payable_amount'=>$a->monthly_amount,'paid_amount'=>0,'balance_amount'=>$a->monthly_amount,'payment_status'=>'unpaid','status'=>1]);}}
}
