<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\{StudentAppAccount, StudentEnrollment, StudentAttendance, TeacherClasswork, SchoolCalendarEvent};
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;

class StudentAcademicController extends Controller
{
    private function context(Request $request): StudentEnrollment
    {
        $account = $request->user();
        abort_unless($account instanceof StudentAppAccount && $account->is_active && $account->student && $account->student->status && (int)$account->student->school_id === (int)$account->school_id && $account->tokenCan('student:mobile'), 403);
        $enrollment = StudentEnrollment::where('school_id', $account->school_id)
            ->where('student_id', $account->student_id)->where('status', 1)
            ->where('is_current', 1)->where('enrollment_status', 'active')->first();
        abort_unless($enrollment, 404, 'No active current enrollment found.');
        return $enrollment;
    }

    private function month(Request $request): array
    {
        $data = $request->validate(['month' => 'nullable|date_format:Y-m']);
        $start = CarbonImmutable::createFromFormat('!Y-m', $data['month'] ?? now()->format('Y-m'));
        return [$start, $start->addMonth()];
    }

    public function attendance(Request $request)
    {
        $e = $this->context($request);
        [$start, $end] = $this->month($request);
        $rows = StudentAttendance::query()->where('student_attendances.school_id', $e->school_id)
            ->where('student_attendances.student_id', $e->student_id)
            ->join('student_attendance_sessions as sess', 'sess.id', '=', 'student_attendances.attendance_session_id')
            ->where('sess.school_id', $e->school_id)->where('sess.academic_year_id', $e->academic_year_id)
            ->where('sess.status', 1)->where('sess.attendance_date', '>=', $start->toDateString())
            ->where('sess.attendance_date', '<', $end->toDateString())
            ->orderBy('sess.attendance_date')->orderByDesc('student_attendances.id')
            ->get(['sess.attendance_date as date', 'student_attendances.attendance_status as status', 'student_attendances.remarks'])
            ->unique('date')->keyBy(fn($row) => substr($row->date, 0, 10));
        $summary = array_fill_keys(['present','absent','late','leave','half_day'], 0);
        $days = [];
        for ($day = $start; $day->lt($end); $day = $day->addDay()) {
            $row = $rows->get($day->toDateString());
            if ($row && isset($summary[$row->status])) $summary[$row->status]++;
            $days[] = ['date'=>$day->toDateString(), 'status'=>$row->status ?? 'not_marked', 'remarks'=>$row->remarks ?? null];
        }
        $marked = array_sum($summary);
        return response()->json(['success'=>true,'data'=>['month'=>$start->format('Y-m'),'summary'=>$summary + ['marked_days'=>$marked,'attendance_percentage'=>$marked ? round(100*($summary['present']+$summary['late']+0.5*$summary['half_day'])/$marked,2) : null], 'days'=>$days]]);
    }

    public function classwork(Request $request) { return $this->work($request, 'classwork'); }
    public function homework(Request $request) { return $this->work($request, 'homework'); }

    private function work(Request $request, string $type)
    {
        $e = $this->context($request);
        [$start,$end] = $this->month($request);
        $data = $request->validate(['page'=>'nullable|integer|min:1']);
        $items = TeacherClasswork::where('school_id',$e->school_id)->where('type',$type)
            ->where('approval_status','approved')->where('work_date','>=',$start->toDateString())
            ->where('work_date','<',$end->toDateString())->whereDate('work_date','<=',now()->toDateString())
            ->whereHas('assignment', function($q) use ($e) {
                $q->where('school_id',$e->school_id)->where('academic_year_id',$e->academic_year_id)
                    ->where('school_class_id',$e->school_class_id)->where(function($q) use ($e) {
                        $q->whereNull('section_id');
                        if ($e->section_id !== null) $q->orWhere('section_id',$e->section_id);
                    });
            })->with(['assignment.subject','teacher:id,name'])->orderByDesc('work_date')->orderByDesc('id')->paginate(25);
        $items->getCollection()->transform(fn($w)=>['id'=>$w->id,'type'=>$w->type,'title'=>$w->title,'description'=>$w->description,'work_date'=>$w->work_date?->toDateString(),'due_date'=>$w->due_date?->toDateString(),'subject'=>$w->assignment?->subject?->name,'teacher'=>$w->teacher?->name]);
        return response()->json(['success'=>true,'data'=>$items]);
    }

    public function calendar(Request $request)
    {
        $e = $this->context($request);
        [$start,$end] = $this->month($request);
        $events = SchoolCalendarEvent::where('school_id',$e->school_id)->where('academic_year_id',$e->academic_year_id)
            ->where('status',1)->where('start_at','<',$end->toDateTimeString())
            ->where(function($q) use ($start) {
                $q->where('end_at','>=',$start->toDateTimeString())->orWhere(function($q) use ($start) {
                    $q->whereNull('end_at')->where('start_at','>=',$start->toDateTimeString());
                });
            })->with('eventType')->orderBy('start_at')->get()->map(fn($event)=>[
                'id'=>$event->id,'title'=>$event->title,'description'=>$event->description,
                'event_type'=>$event->eventType?->name,'start_at'=>$event->start_at?->toIso8601String(),
                'end_at'=>$event->end_at?->toIso8601String(),'is_all_day'=>$event->is_all_day,'is_holiday'=>$event->is_holiday]);
        return response()->json(['success'=>true,'data'=>['month'=>$start->format('Y-m'),'events'=>$events]]);
    }
}
