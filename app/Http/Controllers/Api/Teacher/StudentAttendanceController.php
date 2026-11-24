<?php
namespace App\Http\Controllers\Api\Teacher;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\PersonalAccessToken;

class StudentAttendanceController extends Controller
{
    private function scope(Request $r)
    {
        $u = $r->user();
        abort_unless($u instanceof User && $u->status && $u->school_id && $u->hasRole('Teacher') && $r->bearerToken() && $u->currentAccessToken() instanceof PersonalAccessToken && $u->tokenCan('teacher'), 403);
        abort_unless(DB::table('schools')->where('id',$u->school_id)->where('status',1)->exists(),403);
        return DB::table('teacher_assignments as a')
            ->join('academic_years as y','y.id','=','a.academic_year_id')
            ->join('school_classes as c','c.id','=','a.school_class_id')
            ->join('sections as s','s.id','=','a.section_id')
            ->where('a.school_id',$u->school_id)->where('a.teacher_user_id',$u->id)
            ->where('a.status',1)->where('a.is_class_teacher',1)
            ->where('y.school_id',$u->school_id)->where('y.is_current',1)->where('y.status',1)
            ->where('c.school_id',$u->school_id)->where('c.status',1)
            ->where('s.school_id',$u->school_id)->where('s.status',1)
            ->whereColumn('s.academic_year_id','a.academic_year_id')->whereColumn('s.school_class_id','a.school_class_id');
    }
    private function ok($data) { return response()->json(['success'=>true,'data'=>$data]); }
    public function classes(Request $r)
    {
        return $this->ok(['date'=>now()->toDateString(),'classes'=>$this->scope($r)->distinct()->orderBy('c.sort_order')->get(['c.id as school_class_id','c.name','c.sort_order','y.id as academic_year_id','y.start_date','y.end_date'])]);
    }
    public function sections(Request $r)
    {
        $d=$r->validate(['school_class_id'=>'required|integer|min:1']);
        return $this->ok($this->scope($r)->where('a.school_class_id',$d['school_class_id'])->distinct()->orderBy('s.sort_order')->get(['s.id as section_id','s.name','s.sort_order','a.academic_year_id']));
    }
    private function selection(Request $r)
    {
        $d=$r->validate(['school_class_id'=>'required|integer|min:1','section_id'=>'required|integer|min:1','date'=>'required|date_format:Y-m-d|before_or_equal:today']);
        $a=$this->scope($r)->where('a.school_class_id',$d['school_class_id'])->where('a.section_id',$d['section_id'])->first(['a.school_id','a.academic_year_id','a.school_class_id','a.section_id','y.start_date','y.end_date']);
        abort_unless($a,403,'Class or section is not assigned to you.');
        abort_unless($d['date'] >= $a->start_date && $d['date'] <= $a->end_date,422,'Date outside current academic year.');
        return ['school_id'=>$a->school_id,'academic_year_id'=>$a->academic_year_id,'school_class_id'=>$a->school_class_id,'section_id'=>$a->section_id,'attendance_date'=>$d['date']];
    }
    private function roster(array $key)
    {
        return DB::table('student_enrollments as e')->join('students as s','s.id','=','e.student_id')
            ->where('e.school_id',$key['school_id'])->where('s.school_id',$key['school_id'])
            ->where('e.academic_year_id',$key['academic_year_id'])->where('e.school_class_id',$key['school_class_id'])->where('e.section_id',$key['section_id'])
            ->where('e.is_current',1)->where('e.status',1)->where('e.enrollment_status','active')->where('s.status',1)
            ->where('e.enrollment_date','<=',$key['attendance_date'])->orderBy('e.roll_no')->orderBy('s.student_name')
            ->get(['e.id as student_enrollment_id','e.student_id','e.roll_no','s.student_name','s.admission_no']);
    }
    private function payload(array $key)
    {
        $session=DB::table('student_attendance_sessions')->where($key)->first();
        $marks=$session ? DB::table('student_attendances')->where('school_id',$key['school_id'])->where('attendance_session_id',$session->id)->get()->keyBy('student_enrollment_id') : collect();
        $students=$this->roster($key)->map(function($s)use($marks){$m=$marks->get($s->student_enrollment_id);$s->attendance_status=$m->attendance_status??null;$s->remarks=$m->remarks??null;return $s;});
        return $key+['session_id'=>$session->id??null,'already_submitted'=>$session!==null,'editable'=>!$session || (bool)$session->status,'total_students'=>$students->count(),'marked_students'=>$students->whereNotNull('attendance_status')->count(),'students'=>$students];
    }
    public function students(Request $r) { return $this->ok($this->payload($this->selection($r))); }
    public function store(Request $r)
    {
        $key=$this->selection($r);
        $d=$r->validate(['attendance'=>'required|array|min:1|max:2000','attendance.*.student_enrollment_id'=>'required|integer|min:1|distinct','attendance.*.attendance_status'=>'required|in:present,absent,late,leave,half_day','attendance.*.remarks'=>'nullable|string|max:500']);
        DB::transaction(function()use($r,$key,$d){
            // Serialize API writes for this section; database unique keys also prevent duplicates.
            DB::table('sections')->where('id',$key['section_id'])->where('school_id',$key['school_id'])->lockForUpdate()->first();
            $this->selection($r);
            $students=$this->roster($key)->keyBy('student_enrollment_id');
            $submitted=collect($d['attendance'])->keyBy('student_enrollment_id');
            abort_unless($students->isNotEmpty() && $students->keys()->sort()->values()->all()===$submitted->keys()->sort()->values()->all(),422,'Submit exactly all eligible students in this section.');
            $now=now();
            DB::table('student_attendance_sessions')->upsert([$key+['marked_by'=>$r->user()->id,'status'=>1,'created_at'=>$now,'updated_at'=>$now]],array_keys($key),['attendance_date']);
            $session=DB::table('student_attendance_sessions')->where($key)->lockForUpdate()->first();
            abort_unless($session->status,409,'Attendance session is inactive.');
            $rows=[];
            foreach($students as $id=>$s){$m=$submitted->get($id);$rows[]=['school_id'=>$key['school_id'],'attendance_session_id'=>$session->id,'student_id'=>$s->student_id,'student_enrollment_id'=>$id,'attendance_status'=>$m['attendance_status'],'remarks'=>$m['remarks']??null,'created_at'=>$now,'updated_at'=>$now];}
            DB::table('student_attendances')->upsert($rows,['attendance_session_id','student_id'],['student_enrollment_id','attendance_status','remarks','updated_at']);
            DB::table('student_attendance_sessions')->where('id',$session->id)->update(['marked_by'=>$r->user()->id,'updated_at'=>$now]);
        },3);
        return $this->ok(['message'=>'Attendance saved successfully']+$this->payload($key));
    }
}
