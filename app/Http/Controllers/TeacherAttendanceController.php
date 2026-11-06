<?php
namespace App\Http\Controllers;

use App\Models\{TeacherAssignment,StudentEnrollment,StudentAttendance,StudentAttendanceSession};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TeacherAttendanceController extends Controller
{
    private function scope(Request $request)
    {
        $user=$request->user();
        abort_unless($user && $user->school_id,403);
        return TeacherAssignment::query()->where('school_id',$user->school_id)->where('teacher_user_id',$user->id)
            ->where('status',true)->where('is_class_teacher',true);
    }
    private function assignments(Request $request)
    {
        return $this->scope($request)->with(['academicYear','schoolClass','section'])->get()
            ->unique(fn($a)=>$a->academic_year_id.':'.$a->section_id)->values();
    }
    private function selected(Request $request)
    {
        $id=$request->integer('assignment_id');
        if (!$id) return null;
        return $this->scope($request)->findOrFail($id);
    }
    private function enrollments(Request $request, $assignment)
    {
        return StudentEnrollment::query()->with('student')->where('school_id',$request->user()->school_id)
            ->where('academic_year_id',$assignment->academic_year_id)
            ->where('school_class_id',$assignment->school_class_id)->where('section_id',$assignment->section_id)
            ->where('is_current',true)->orderBy('roll_no')->get();
    }
    public function index(Request $request)
    {
        $assignments=$this->assignments($request);
        $selected=$this->selected($request);
        $date=$request->input('date',now()->toDateString());
        abort_unless(preg_match('/^\d{4}-\d{2}-\d{2}$/',$date),422);
        $students=collect();$marks=collect();$session=null;
        if($selected){
            $students=$this->enrollments($request,$selected);
            $session=StudentAttendanceSession::where('school_id',$request->user()->school_id)
                ->where('academic_year_id',$selected->academic_year_id)->where('section_id',$selected->section_id)
                ->whereDate('attendance_date',$date)->first();
            if($session)$marks=StudentAttendance::where('attendance_session_id',$session->id)->get()->keyBy('student_enrollment_id');
        }
        return view('teacher.attendance.index',compact('assignments','selected','date','students','marks','session'));
    }
    public function store(Request $request)
    {
        $request->validate(['assignment_id'=>'required|integer','date'=>'required|date_format:Y-m-d|before_or_equal:today',
            'marks'=>'required|array','marks.*'=>['required',Rule::in(['present','absent','late','leave','half_day'])]]);
        $selected=$this->selected($request);abort_unless($selected,404);
        $students=$this->enrollments($request,$selected);
        $allowed=$students->pluck('id')->map(fn($v)=>(string)$v)->sort()->values()->all();
        $submitted=collect(array_keys($request->input('marks')))->map(fn($v)=>(string)$v)->sort()->values()->all();
        abort_unless($allowed===$submitted,422,'Attendance must include exactly the enrolled students in the assigned section.');
        DB::transaction(function() use ($request,$selected,$students){
            $session=StudentAttendanceSession::firstOrCreate([
                'school_id'=>$request->user()->school_id,'academic_year_id'=>$selected->academic_year_id,
                'school_class_id'=>$selected->school_class_id,'section_id'=>$selected->section_id,
                'attendance_date'=>$request->date('date')->format('Y-m-d')
            ],['marked_by'=>$request->user()->id,'status'=>true]);
            foreach($students as $enrollment){
                StudentAttendance::updateOrCreate([
                    'attendance_session_id'=>$session->id,'student_id'=>$enrollment->student_id
                ],['school_id'=>$request->user()->school_id,'student_enrollment_id'=>$enrollment->id,
                   'attendance_status'=>$request->input('marks.'.$enrollment->id)]);
            }
        });
        return redirect()->route('teacher.attendance.index',['assignment_id'=>$selected->id,'date'=>$request->date('date')->format('Y-m-d')])->with('success','Attendance saved.');
    }
    public function report(Request $request)
    {
        $assignments=$this->assignments($request);$selected=$this->selected($request);
        $month=$request->input('month',now()->format('Y-m'));
        abort_unless(preg_match('/^\d{4}-(0[1-9]|1[0-2])$/',$month),422);
        $students=collect();$counts=[];
        if($selected){
            $students=$this->enrollments($request,$selected);
            $records=StudentAttendance::query()->join('student_attendance_sessions as s','s.id','=','student_attendances.attendance_session_id')
                ->where('student_attendances.school_id',$request->user()->school_id)
                ->where('s.school_id',$request->user()->school_id)->where('s.academic_year_id',$selected->academic_year_id)
                ->where('s.section_id',$selected->section_id)->where('s.attendance_date','like',$month.'%')
                ->select('student_attendances.student_enrollment_id','student_attendances.attendance_status')->get();
            foreach($records as $record){$id=$record->student_enrollment_id;$status=$record->attendance_status;
                $counts[$id][$status]=($counts[$id][$status]??0)+1;}
        }
        return view('teacher.attendance.report',compact('assignments','selected','month','students','counts'));
    }
}
