<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class StudentInformationDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        abort_unless($user && $user->school_id, 403, 'School assignment required.');
        abort_unless($user->hasAnyRole(['Admin', 'Super Admin']) || $user->can('student.view'), 403);
        $school = (int) $user->school_id;
        $years = DB::table('academic_years')->where('school_id', $school)->orderByDesc('start_date')->get(['id','name','is_current']);
        $requestedYear = $request->filled('academic_year_id') ? (int)$request->input('academic_year_id') : null;
        $year = $years->firstWhere('id', $requestedYear) ?: ($years->firstWhere('is_current', 1) ?: $years->first());
        $yearId = $year?->id;
        $date = $request->input('date', now()->toDateString());
        if (!is_string($date) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) $date = now()->toDateString();
        try { $date = Carbon::createFromFormat('!Y-m-d', $date)->toDateString(); }
        catch (\Throwable $e) { $date = now()->toDateString(); }
        $status = $request->input('student_status', 'active');
        if (!in_array($status, ['active','inactive','all'], true)) $status='active';

        $students = DB::table('students as st')->where('st.school_id',$school);
        $students->join('student_enrollments as se','se.student_id','=','st.id')
            ->where('se.school_id',$school)->where('se.is_current',1);
        if ($yearId) $students->where('se.academic_year_id',$yearId);
        else $students->whereRaw('1=0');
        if ($status !== 'all') $students->where('st.status', $status === 'active' ? 1 : 0);
        $totalStudents = (clone $students)->distinct()->count('st.id');
        $totalClasses = $yearId ? DB::table('school_classes')->where('school_id',$school)
            ->whereIn('id', DB::table('student_enrollments')->where('school_id',$school)
                ->where('academic_year_id',$yearId)->where('is_current',1)->select('school_class_id'))
            ->count() : 0;
        $totalTeachers = Schema::hasTable('teacher_assignments') && $yearId
            ? DB::table('teacher_assignments')->where('school_id',$school)->where('academic_year_id',$yearId)
                ->distinct()->count('teacher_user_id') : null;
        $presentEmployees = Schema::hasTable('hr_daily_attendance')
            ? DB::table('hr_daily_attendance as ha')->join('hr_attendance_statuses as hs',function($j)use($school){
                $j->on('hs.id','=','ha.attendance_status_id')->where('hs.school_id','=',$school);
            })->where('ha.school_id',$school)->where('ha.attendance_date',$date)
              ->whereIn('hs.count_as',['present','late','on_duty'])->distinct()->count('ha.user_id')
            : (Schema::hasTable('teacher_staff_attendances')
                ? DB::table('teacher_staff_attendances')->where('school_id',$school)->where('attendance_date',$date)
                    ->whereIn('status',['present','late'])->distinct()->count('user_id') : null);

        $genderRows = (clone $students)
            ->join('school_classes as sc','sc.id','=','se.school_class_id')
            ->where('sc.school_id',$school)
            ->select('sc.id','sc.name', 'sc.sort_order',
                DB::raw("COUNT(DISTINCT CASE WHEN LOWER(st.gender) IN ('male','m','boy') THEN st.id END) AS boys"),
                DB::raw("COUNT(DISTINCT CASE WHEN LOWER(st.gender) IN ('female','f','girl') THEN st.id END) AS girls"))
            ->groupBy('sc.id','sc.name','sc.sort_order')->orderBy('sc.sort_order')->orderBy('sc.name')->get();

        $attendance = DB::table('student_attendances as a')
            ->join('student_attendance_sessions as sess',function($j)use($school){
                $j->on('sess.id','=','a.attendance_session_id')->where('sess.school_id','=',$school);
            })
            ->join('sections as sec',function($j)use($school){
                $j->on('sec.id','=','sess.section_id')->where('sec.school_id','=',$school);
            })
            ->join('school_classes as sc',function($j)use($school){
                $j->on('sc.id','=','sess.school_class_id')->where('sc.school_id','=',$school);
            })
            ->where('a.school_id',$school)->where('sess.attendance_date',$date);
        if ($yearId) $attendance->where('sess.academic_year_id',$yearId);
        else $attendance->whereRaw('1=0');
        if ($status !== 'all') $attendance->join('students as ast',function($j)use($school){
            $j->on('ast.id','=','a.student_id')->where('ast.school_id','=',$school);
        })->where('ast.status',$status==='active'?1:0);
        $attendanceRows = $attendance->select('sc.name as class_name','sc.sort_order','sec.name as section_name',
            DB::raw("SUM(CASE WHEN a.attendance_status IN ('present','late') THEN 1 ELSE 0 END) as present_count"),
            DB::raw("SUM(CASE WHEN a.attendance_status='absent' THEN 1 ELSE 0 END) as absent_count"),
            DB::raw("SUM(CASE WHEN a.attendance_status='leave' THEN 1 ELSE 0 END) as leave_count"),
            DB::raw("SUM(CASE WHEN a.attendance_status='half_day' THEN 1 ELSE 0 END) as other_count"))
            ->groupBy('sc.id','sc.name','sc.sort_order','sec.id','sec.name')
            ->orderBy('sc.sort_order')->orderBy('sc.name')->orderBy('sec.name')->get();
        $presentStudents = $attendanceRows->sum('present_count');

        $monthStart=Carbon::parse($date)->startOfMonth();
        $monthEnd=$monthStart->copy()->endOfMonth();
        $events = DB::table('school_calendar_events')->where('school_id',$school)
            ->where('status',1)->where('start_at','<=',$monthEnd->endOfDay())
            ->where('end_at','>=',$monthStart->startOfDay());
        if ($yearId) $events->where('academic_year_id',$yearId);
        $events=$events->orderBy('start_at')->get(['title','start_at','end_at','is_holiday']);
        return view('student-information.dashboard', compact('years','yearId','date','status',
            'totalStudents','totalTeachers','totalClasses','presentStudents','presentEmployees',
            'genderRows','attendanceRows','events'));
    }
}
