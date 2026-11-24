<?php
namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\StudentFeeDue;
use App\Models\FeeCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FeeDashboardController extends Controller
{
    public function index(Request $request)
    {
        $schoolId = $request->user()->school_id;
        abort_unless($schoolId, 403);
        $request->validate(['academic_year_id'=>'nullable|integer','from'=>'nullable|date_format:Y-m-d','to'=>'nullable|date_format:Y-m-d|after_or_equal:from']);
        $academicYears = AcademicYear::where('school_id',$schoolId)->orderByDesc('start_date')->get();
        $currentAcademicYear = $academicYears->firstWhere('is_current',true);
        $academicYearId = $request->input('academic_year_id') ?: optional($currentAcademicYear ?? $academicYears->first())->id;
        $year = $academicYears->firstWhere('id',$academicYearId);
        abort_if($academicYearId && !$year, 404);
        $today = now()->toDateString();
        $from = $request->input('from') ?: ($year ? Carbon::parse($year->start_date)->toDateString() : $today);
        $to = $request->input('to') ?: ($year ? min($today,Carbon::parse($year->end_date)->toDateString()) : $today);
        if (!$request->filled('to') && $to < $from) $to = $from;
        abort_if($from > $to,422,'Invalid date range.');
        $dues = StudentFeeDue::where('student_fee_dues.school_id',$schoolId)->where('student_fee_dues.academic_year_id',$academicYearId)->where('student_fee_dues.status',1);
        $summary = (array)(clone $dues)->toBase()->selectRaw('COALESCE(SUM(base_amount),0) actual, COALESCE(SUM(discount_amount),0) discount, COALESCE(SUM(fine_amount),0) fine, COALESCE(SUM(payable_amount),0) payable, COALESCE(SUM(paid_amount),0) paid, COALESCE(SUM(balance_amount),0) pending')->first();
        $receipts = FeeCollection::where('fee_collections.school_id',$schoolId)->where('fee_collections.academic_year_id',$academicYearId)->where('fee_collections.status','posted');
        $todayQuery = (clone $receipts)->where('payment_date',$today);
        $todayCollection = (clone $todayQuery)->sum('total_amount');
        $todayReceipts = $todayQuery->count();
        $period = (clone $receipts)->whereBetween('payment_date',[$from,$to]);
        $paymentModes = (clone $period)->selectRaw('payment_mode, SUM(total_amount) total')->groupBy('payment_mode')->pluck('total','payment_mode');
        $trendStart = max($from,Carbon::parse($to)->startOfMonth()->subMonths(5)->toDateString());
        $monthly = (clone $period)->where('payment_date','>=',$trendStart)->selectRaw("DATE_FORMAT(payment_date,'%Y-%m') month_key, SUM(total_amount) total")->groupBy('month_key')->pluck('total','month_key');
        $trend = [];
        for ($month=Carbon::parse($trendStart)->startOfMonth(); $month->toDateString() <= $to; $month->addMonth()) {
            $trend[] = ['label'=>$month->format('M Y'),'total'=>(float)($monthly[$month->format('Y-m')] ?? 0)];
        }
        $componentSummary = (clone $dues)->selectRaw('fee_head_name, SUM(base_amount) actual_amount, SUM(discount_amount) concession, SUM(payable_amount) payable, SUM(paid_amount) paid, SUM(balance_amount) balance')->groupBy('fee_head_name')->orderBy('fee_head_name')->get();
        $enrollments = DB::table('student_enrollments')->where('school_id',$schoolId)->where('academic_year_id',$academicYearId)->where('status',1)->where('enrollment_status','active');
        $counts = (clone $enrollments)->selectRaw('school_class_id, COUNT(DISTINCT student_id) total')->groupBy('school_class_id')->pluck('total','school_class_id');
        $classWise = (clone $dues)->join('student_enrollments as e',function($j){$j->on('e.id','=','student_fee_dues.student_enrollment_id')->on('e.school_id','=','student_fee_dues.school_id');})->join('school_classes as c',function($j){$j->on('c.id','=','e.school_class_id')->on('c.school_id','=','e.school_id');})->selectRaw('c.id, c.name as class, c.sort_order, SUM(student_fee_dues.base_amount) actual, SUM(student_fee_dues.payable_amount) payable, SUM(student_fee_dues.paid_amount) paid, SUM(student_fee_dues.balance_amount) pending')->groupBy('c.id','c.name','c.sort_order')->orderBy('c.sort_order')->orderBy('c.name')->get()->map(function($r)use($counts){$r->students=$counts[$r->id]??0;return $r;});
        $totalStudents = (clone $enrollments)->distinct()->count('student_id');
        $studentsWithDues = (clone $dues)->where('balance_amount','>',0)->distinct()->count('student_id');
        $overdueStudents = (clone $dues)->where('balance_amount','>',0)->where('due_date','<',now()->subDays(30)->toDateString())->distinct()->count('student_id');
        $recentReceipts = (clone $period)->leftJoin('students as s',function($j){$j->on('s.id','=','fee_collections.student_id')->on('s.school_id','=','fee_collections.school_id');})->leftJoin('student_enrollments as e',function($j){$j->on('e.id','=','fee_collections.student_enrollment_id')->on('e.school_id','=','fee_collections.school_id');})->leftJoin('school_classes as c',function($j){$j->on('c.id','=','e.school_class_id')->on('c.school_id','=','e.school_id');})->leftJoin('sections as sec',function($j){$j->on('sec.id','=','e.section_id');})->select('fee_collections.id','fee_collections.payment_date','fee_collections.receipt_no','fee_collections.total_amount','s.student_name','c.name as class_name','sec.name as section_name')->orderByDesc('fee_collections.payment_date')->orderByDesc('fee_collections.id')->limit(5)->get();
        return view('fees.dashboard',compact('academicYears','currentAcademicYear','academicYearId','summary','todayCollection','todayReceipts','paymentModes','classWise','componentSummary','trend','from','to','totalStudents','studentsWithDues','overdueStudents','recentReceipts'));
    }
}
