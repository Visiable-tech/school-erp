<?php
namespace App\Http\Controllers;
use App\Models\{AcademicYear, AdmissionApplication, AdmissionEnquiry, AdmissionSetup};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class AdmissionAnalyticsController extends Controller {
 public function index(Request $request) {
  $u=$request->user(); abort_unless($u && $u->school_id && $u->can('admission-application.view') && $u->can('admission-enquiry.view'),403);
  $school=$u->school_id;
  $years=AcademicYear::where('school_id',$school)->orderByDesc('start_date')->get();
  $year=$request->filled('year') ? $years->firstWhere('id',(int)$request->year) : ($years->firstWhere('is_current',true) ?? $years->first());
  abort_if(!$year,422,'Create an academic year first.');
  $previous=$years->filter(fn($y)=>$y->start_date < $year->start_date)->values();
  $compare=$request->filled('compare') ? $years->firstWhere('id',(int)$request->compare) : $previous->first();
  abort_if($request->filled('compare') && !$compare,422);
  $eq=fn($id)=>AdmissionEnquiry::where('school_id',$school)->where('academic_year_id',$id);
  $ap=fn($id)=>AdmissionApplication::where('school_id',$school)->where('academic_year_id',$id);
  $e=$eq($year->id); $a=$ap($year->id); $ad=(clone $a)->where('application_status','admitted');
  $total=$e->count(); $applications=$a->count(); $admitted=$ad->count();
  $process=(clone $a)->whereIn('application_status',['submitted','under_review','approved'])->count();
  $waiting=(clone $a)->where('application_status','waiting_list')->count();
  $cards=['Total Enquiries'=>$total,'Applications Received'=>$applications,'Admissions Confirmed'=>$admitted,'In Process'=>$process,'Waiting List'=>$waiting,'Conversion Rate'=>$total ? round(100*$admitted/$total,1).'%' : '—'];
  $recent=(clone $e)->with('schoolClass')->latest('enquiry_date')->limit(8)->get();
  $source=(clone $ad)->with('enquiry')->get()->groupBy(fn($r)=>$r->enquiry?->source ?: 'Unknown')->map->count();
  $classes=(clone $ad)->with('schoolClass')->get()->groupBy(fn($r)=>$r->schoolClass?->name ?: 'Unknown')->map->count();
  $series=[]; $labels=['Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec','Jan','Feb','Mar'];
  $sessions=collect([$year,$compare,$previous->first(fn($y)=>$y->id!==$compare?->id)])->filter()->unique('id');
  foreach($sessions as $session){$rows=$ap($session->id)->where('application_status','admitted')->with('student')->get(); $counts=array_fill(0,12,0); foreach($rows as $r){if($r->student?->admission_date){$counts[($r->student->admission_date->month+8)%12]++;}} $series[]=['label'=>$session->name,'data'=>$counts,'total'=>$rows->count()];}
  $trend=[]; foreach([['Enquiries',(clone $e)->get(),'enquiry_date'],['Applications',(clone $a)->get(),'application_date'],['Admissions',(clone $ad)->with('student')->get(),null]] as [$label,$rows,$field]){$v=array_fill(0,12,0);foreach($rows as $r){$d=$field?$r->$field:$r->student?->admission_date;if($d)$v[($d->month+8)%12]++;} $sum=0;foreach($v as &$n){$sum+=$n;$n=$sum;}unset($n);$trend[]=['label'=>$label,'data'=>$v];}
  $alerts=['Follow-ups due'=>(clone $e)->whereIn('enquiry_status',['open','follow_up','interested'])->whereDate('next_followup_date','<=',today())->count(),'Applications to review'=>(clone $a)->whereIn('application_status',['submitted','under_review'])->count(),'Approved, awaiting admission'=>(clone $a)->where('application_status','approved')->count(),'Waiting list'=>$waiting];
  $milestones=AdmissionSetup::where('school_id',$school)->where('academic_year_id',$year->id)->with('schoolClass')->get();
  $funnel=['Enquiries'=>$total,'Applications'=>$applications,'Reviewed or approved'=> (clone $a)->whereIn('application_status',['under_review','approved','admitted'])->count(),'Admissions'=>$admitted];
  return view('admission.analytics',compact('years','year','compare','cards','recent','source','classes','series','labels','trend','alerts','milestones','funnel','total'));
 }
}
