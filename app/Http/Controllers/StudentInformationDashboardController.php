<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class StudentInformationDashboardController extends Controller
{
 public function index(Request $request)
 {
  $user=$request->user();
  abort_unless($user && $user->school_id,403);
  abort_unless($user->hasAnyRole(['Admin','Super Admin']) || $user->can('student.view'),403);
  $school=$user->school_id;
  $request->validate(['academic_year_id'=>'nullable|integer','date'=>'nullable|date_format:Y-m-d']);
  $years=DB::table('academic_years')->where('school_id',$school)->orderByDesc('start_date')->get();
  $today=now()->toDateString();
  $currentYear=$years->first(fn($y) => $y->start_date <= $today && $y->end_date >= $today);
  $year=$years->firstWhere('id',$request->academic_year_id) ?? $currentYear ?? $years->firstWhere('is_current',1) ?? $years->first();
  $yearId=$year->id ?? null;
  $date=$request->date ?? now()->toDateString();
  $base=DB::table('student_enrollments as e')->join('students as s','s.id','=','e.student_id')->where('e.school_id',$school)->where('s.school_id',$school)->where('e.academic_year_id',$yearId);
  $total=(clone $base)->distinct()->count('s.id');
  $active=(clone $base)->where('s.status',1)->where('e.status',1)->where('e.enrollment_status','active');
  $activeCount=(clone $active)->distinct()->count('s.id');
  $boys=(clone $active)->whereIn(DB::raw('LOWER(s.gender)'),['male','m','boy'])->distinct()->count('s.id');
  $girls=(clone $active)->whereIn(DB::raw('LOWER(s.gender)'),['female','f','girl'])->distinct()->count('s.id');
  $new=$year?(clone $base)->whereBetween('s.admission_date',[$year->start_date,$year->end_date])->distinct()->count('s.id'):0;
  $classes=(clone $active)->join('school_classes as c','c.id','=','e.school_class_id')->where('c.school_id',$school)->groupBy('c.id','c.name','c.sort_order')->orderBy('c.sort_order')->select('c.id','c.name',DB::raw('COUNT(DISTINCT s.id) as strength, COUNT(DISTINCT e.section_id) as sections'),DB::raw("COUNT(DISTINCT CASE WHEN LOWER(s.gender) IN ('male','m','boy') THEN s.id END) as boys, COUNT(DISTINCT CASE WHEN LOWER(s.gender) IN ('female','f','girl') THEN s.id END) as girls"))->get();
  $ids=(clone $active)->select('s.id')->distinct();
  $att=DB::table('student_attendances as a')->join('student_attendance_sessions as t','t.id','=','a.attendance_session_id')->where('a.school_id',$school)->where('t.school_id',$school)->where('t.academic_year_id',$yearId)->where('t.status',1)->whereIn('a.student_id',$ids);
  $daily=(clone $att)->where('t.attendance_date',$date)->select('a.student_id','a.attendance_status','t.school_class_id')->orderBy('a.id')->get()->keyBy('student_id');
  $counts=$daily->countBy('attendance_status');
  $present=($counts['present']??0)+($counts['late']??0)+($counts['half_day']??0);
  $marked=$daily->count(); $rate=$marked?round(100*$present/$marked,1):null;
  $classRates=$classes->map(function($c)use($daily){$r=$daily->where('school_class_id',$c->id);return $r->count()?round(100*$r->whereIn('attendance_status',['present','late','half_day'])->count()/$r->count(),1):null;});
  $start=Carbon::parse($date)->subDays(29)->toDateString();
  $history=(clone $att)->whereBetween('t.attendance_date',[$start,$date])->select('t.attendance_date','a.student_id','a.attendance_status')->orderBy('a.id')->get()->groupBy('attendance_date');
  $trend=$history->map(function($r){$r=$r->keyBy('student_id');return round(100*$r->whereIn('attendance_status',['present','late','half_day'])->count()/$r->count(),1);})->sortKeys();
  $low=$history->flatten(1)->groupBy('student_id')->filter(function($r){$r=$r->keyBy('attendance_date');return $r->count() && $r->whereIn('attendance_status',['present','late','half_day'])->count()/$r->count()<.75;})->count();
  $missing=(clone $active)->whereNotExists(function($q)use($school){$q->selectRaw('1')->from('student_documents as d')->whereColumn('d.student_id','s.id')->where('d.school_id',$school)->where('d.status',1);})->distinct()->count('s.id');
  $photos=(clone $active)->where(function($q){$q->whereNull('s.student_photo')->orWhere('s.student_photo','');})->distinct()->count('s.id');
  $events=DB::table('school_calendar_events')->where('school_id',$school)->where('academic_year_id',$yearId)->where('status',1)->where('end_at','>=',$date.' 00:00:00')->orderBy('start_at')->limit(7)->get(['title','start_at','end_at','is_holiday']);
  return view('student-information.dashboard',compact('years','yearId','date','total','activeCount','boys','girls','new','classes','counts','present','marked','rate','classRates','trend','low','missing','photos','events'));
 }
}
