<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\StudentFeeDue;
use App\Models\FeeCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FeeDashboardController extends Controller
{
    public function index(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        $academicYears = AcademicYear::where('school_id', $schoolId)
            ->orderByDesc('start_date')
            ->get();

        $currentAcademicYear = $academicYears
            ->firstWhere('is_current', true);

        $academicYearId = $request->academic_year_id
            ?: optional($currentAcademicYear)->id;

        /*
         * Overall summary
         */
        $dueQuery = StudentFeeDue::where('school_id', $schoolId)
            ->where('status', 1);

        if ($academicYearId) {
            $dueQuery->where(
                'academic_year_id',
                $academicYearId
            );
        }

        $summary = [
            'actual' => (clone $dueQuery)
                ->sum('base_amount'),

            'discount' => (clone $dueQuery)
                ->sum('discount_amount'),

            'fine' => (clone $dueQuery)
                ->sum('fine_amount'),

            'payable' => (clone $dueQuery)
                ->sum('payable_amount'),

            'paid' => (clone $dueQuery)
                ->sum('paid_amount'),

            'pending' => (clone $dueQuery)
                ->sum('balance_amount'),
        ];


        /*
         * Today's collection
         */
        $todayCollectionQuery = FeeCollection::where(
                'school_id',
                $schoolId
            )
            ->whereDate(
                'payment_date',
                now()->toDateString()
            )
            ->where('status', 'posted');

        if ($academicYearId) {
            $todayCollectionQuery->where(
                'academic_year_id',
                $academicYearId
            );
        }

        $todayCollection = (clone $todayCollectionQuery)
            ->sum('total_amount');

        $todayReceipts = (clone $todayCollectionQuery)
            ->count();


        /*
         * Payment mode summary
         */
        $paymentModes = (clone $todayCollectionQuery)
            ->select(
                'payment_mode',
                DB::raw('SUM(total_amount) as total')
            )
            ->groupBy('payment_mode')
            ->pluck('total', 'payment_mode');


        /*
         * Class wise summary
         */
        $classes = SchoolClass::where(
                'school_id',
                $schoolId
            )
            ->where('status', 1)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();


        $classWise = [];

        foreach ($classes as $class) {

            $query = StudentFeeDue::query()
                ->join(
                    'student_enrollments',
                    'student_enrollments.id',
                    '=',
                    'student_fee_dues.student_enrollment_id'
                )
                ->where(
                    'student_fee_dues.school_id',
                    $schoolId
                )
                ->where(
                    'student_fee_dues.status',
                    1
                )
                ->where(
                    'student_enrollments.school_class_id',
                    $class->id
                );

            if ($academicYearId) {
                $query->where(
                    'student_fee_dues.academic_year_id',
                    $academicYearId
                );
            }

            $row = $query
                ->selectRaw('
                    COALESCE(SUM(student_fee_dues.base_amount),0) as actual,
                    COALESCE(SUM(student_fee_dues.discount_amount),0) as adjustment,
                    COALESCE(SUM(student_fee_dues.paid_amount),0) as paid,
                    COALESCE(SUM(student_fee_dues.balance_amount),0) as pending
                ')
                ->first();

            $classWise[] = [
                'class' => $class->name,
                'actual' => (float) $row->actual,
                'adjustment' => (float) $row->adjustment,
                'paid' => (float) $row->paid,
                'pending' => (float) $row->pending,
            ];
        }


        /*
         * Component/Fee Head summary
         */
        $componentSummary = StudentFeeDue::where(
                'student_fee_dues.school_id',
                $schoolId
            )
            ->where(
                'student_fee_dues.status',
                1
            )
            ->when(
                $academicYearId,
                function ($query) use ($academicYearId) {
                    $query->where(
                        'academic_year_id',
                        $academicYearId
                    );
                }
            )
            ->select(
                'fee_head_name',
                DB::raw(
                    'SUM(base_amount) as actual_amount'
                ),
                DB::raw(
                    'SUM(discount_amount) as concession'
                ),
                DB::raw(
                    'SUM(paid_amount) as paid'
                ),
                DB::raw(
                    'SUM(balance_amount) as balance'
                )
            )
            ->groupBy('fee_head_name')
            ->orderBy('fee_head_name')
            ->get();


        return view(
            'fees.dashboard',
            compact(
                'academicYears',
                'currentAcademicYear',
                'academicYearId',
                'summary',
                'todayCollection',
                'todayReceipts',
                'paymentModes',
                'classWise',
                'componentSummary'
            )
        );
    }
}