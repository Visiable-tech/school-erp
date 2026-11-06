<?php
namespace App\Http\Controllers;

use App\Models\TeacherAssignment;
use App\Models\StudentEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class TeacherAttendanceReportController extends Controller
{
    private function assignments(Request $request)
    {
        $user=$request->user();
        abort_unless($user && $user->school_id,403);
        return TeacherAssignment::query()->where('school_id',$user->school_id)
            ->where('teacher_user_id',$user->id)->where('status',true)->where('is_class_teacher',true)
            ->with(['academicYear','schoolClass','section'])->orderByDesc('academic_year_id')->get()
            ->unique(fn($a)=>$a->academic_year_id.':'.$a->section_id)->values();
    }

    public function index(Request $request, string $report)
    {
        abort_unless(in_array($report,['detail','percentage','absentees'],true),404);
        $assignments=$this->assignments($request);
        $data=$request->validate([
            'assignment_id'=>'nullable|integer',
            'from'=>'nullable|date_format:Y-m-d',
            'to'=>'nullable|date_format:Y-m-d|after_or_equal:from',
            'threshold'=>'nullable|integer|min:1|max:100',
        ]);
        $from=$data['from']??now()->startOfMonth()->toDateString();
        $to=$data['to']??now()->toDateString();
        abort_if(Carbon::parse($from)->gt(Carbon::parse($to)),422,'Start date cannot exceed end date.');
        abort_if(Carbon::parse($from)->diffInDays(Carbon::parse($to))>366,422,'Select a date range of 366 days or less.');
        $threshold=(int)($data['threshold']??75);
        $selected=null;$students=collect();$records=collect();$summary=[];$sessions=0;
        if(!empty($data['assignment_id'])) {
            $selected=$assignments->firstWhere('id',(int)$data['assignment_id']);
            abort_unless($selected,404,'Class assignment not found.');
            $students=StudentEnrollment::query()->with('student')
                ->where('school_id',$request->user()->school_id)
                ->where('academic_year_id',$selected->academic_year_id)
                ->where('school_class_id',$selected->school_class_id)
                ->where('section_id',$selected->section_id)->where('is_current',true)
                ->orderBy('roll_no')->get();
            $sessionQuery=DB::table('student_attendance_sessions')
                ->where('school_id',$request->user()->school_id)
                ->where('academic_year_id',$selected->academic_year_id)
                ->where('school_class_id',$selected->school_class_id)
                ->where('section_id',$selected->section_id)
                ->whereBetween('attendance_date',[$from,$to]);
            $sessions=(clone $sessionQuery)->count();
            $records=DB::table('student_attendances as a')
                ->join('student_attendance_sessions as s','s.id','=','a.attendance_session_id')
                ->where('a.school_id',$request->user()->school_id)
                ->where('s.school_id',$request->user()->school_id)
                ->where('s.academic_year_id',$selected->academic_year_id)
                ->where('s.school_class_id',$selected->school_class_id)
                ->where('s.section_id',$selected->section_id)
                ->whereBetween('s.attendance_date',[$from,$to])
                ->whereIn('a.student_enrollment_id',$students->pluck('id'))
                ->orderBy('s.attendance_date')
                ->select('a.student_enrollment_id','a.attendance_status','a.remarks','s.attendance_date')->get();
            foreach($students as $student){
                $marks=$records->where('student_enrollment_id',$student->id);
                $counts=$marks->countBy('attendance_status');
                $present=(int)($counts['present']??0)+(int)($counts['late']??0)+0.5*(int)($counts['half_day']??0);
                $marked=$marks->count();
                $summary[$student->id]=[
                    'present'=>(int)($counts['present']??0),'absent'=>(int)($counts['absent']??0),
                    'late'=>(int)($counts['late']??0),'leave'=>(int)($counts['leave']??0),
                    'half_day'=>(int)($counts['half_day']??0),'marked'=>$marked,
                    'percentage'=>$marked>0?round(100*$present/$marked,2):null,
                ];
            }
        }
        return view('teacher.attendance.advanced-report',compact('report','assignments','selected','students','records','summary','sessions','from','to','threshold'));
    }
}
