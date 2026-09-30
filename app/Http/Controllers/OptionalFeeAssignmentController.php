<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\FeeHead;
use App\Models\FeeInstallment;
use App\Models\StudentEnrollment;
use App\Models\StudentFeeDue;
use App\Models\StudentOptionalFeeAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class OptionalFeeAssignmentController extends Controller
{
    public function index(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        $query = StudentOptionalFeeAssignment::with([
            'academicYear',
            'student',
            'enrollment.schoolClass',
            'enrollment.section',
            'feeHead',
            'assignedBy',
        ])
        ->where('school_id', $schoolId);

        if ($request->filled('academic_year_id')) {
            $query->where(
                'academic_year_id',
                $request->academic_year_id
            );
        }

        if ($request->filled('student')) {

            $search = trim($request->student);

            $query->whereHas(
                'student',
                function ($q) use ($search) {

                    $q->where(
                        'name',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'admission_no',
                        'like',
                        "%{$search}%"
                    );
                }
            );
        }

        $assignments = $query
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $academicYears = AcademicYear::where(
            'school_id',
            $schoolId
        )
        ->orderByDesc('is_current')
        ->orderByDesc('start_date')
        ->get();

        return view(
            'optional-fee-assignments.index',
            compact(
                'assignments',
                'academicYears'
            )
        );
    }


    public function create()
    {
        $schoolId = Auth::user()->school_id;

        $academicYears = AcademicYear::where(
            'school_id',
            $schoolId
        )
        ->orderByDesc('is_current')
        ->orderByDesc('start_date')
        ->get();

        $feeComponents = FeeHead::with(
            'feeCycle'
        )
        ->where(
            'school_id',
            $schoolId
        )
        ->where('is_optional', 1)
        ->where('status', 1)
        ->orderBy('sort_order')
        ->orderBy('name')
        ->get();

        return view(
            'optional-fee-assignments.create',
            compact(
                'academicYears',
                'feeComponents'
            )
        );
    }


    /*
     * AJAX student list.
     */
    public function students(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        $request->validate([
            'academic_year_id' => [
                'required',
                'integer',
            ],
        ]);

        $query = StudentEnrollment::with([
            'student',
            'schoolClass',
            'section',
        ])
        ->where(
            'school_id',
            $schoolId
        )
        ->where(
            'academic_year_id',
            $request->academic_year_id
        )
        ->where('status', 1);

        if ($request->filled('search')) {

            $search = trim(
                $request->search
            );

            $query->whereHas(
                'student',
                function ($q) use ($search) {

                    $q->where(
                        'name',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'admission_no',
                        'like',
                        "%{$search}%"
                    );
                }
            );
        }

        $enrollments = $query
            ->limit(100)
            ->get();

        return response()->json(
            $enrollments->map(
                function ($enrollment) {

                    return [
                        'id' =>
                            $enrollment->id,

                        'student_id' =>
                            $enrollment->student_id,

                        'name' =>
                            $enrollment->student->name
                            ?? $enrollment->student->student_name
                            ?? 'Student',

                        'admission_no' =>
                            $enrollment->student->admission_no
                            ?? '',

                        'class' =>
                            $enrollment->schoolClass->name
                            ?? '',

                        'section' =>
                            $enrollment->section->name
                            ?? '',
                    ];
                }
            )
        );
    }


    public function store(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        $validated = $request->validate([

            'academic_year_id' => [
                'required',

                Rule::exists(
                    'academic_years',
                    'id'
                )->where(
                    fn ($q) =>
                        $q->where(
                            'school_id',
                            $schoolId
                        )
                ),
            ],

            'student_enrollment_id' => [
                'required',

                Rule::exists(
                    'student_enrollments',
                    'id'
                )->where(
                    fn ($q) =>
                        $q->where(
                            'school_id',
                            $schoolId
                        )
                ),
            ],

            'fee_head_id' => [
                'required',

                Rule::exists(
                    'fee_heads',
                    'id'
                )->where(
                    fn ($q) =>
                        $q->where(
                            'school_id',
                            $schoolId
                        )
                        ->where(
                            'is_optional',
                            1
                        )
                ),
            ],

            'amount' => [
                'required',
                'numeric',
                'min:0.01',
            ],

            'assigned_date' => [
                'required',
                'date',
            ],

            'effective_from' => [
                'nullable',
                'date',
            ],

            'effective_to' => [
                'nullable',
                'date',
                'after_or_equal:effective_from',
            ],

            'remarks' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);


        $enrollment =
            StudentEnrollment::where(
                'school_id',
                $schoolId
            )
            ->where(
                'academic_year_id',
                $validated[
                    'academic_year_id'
                ]
            )
            ->findOrFail(
                $validated[
                    'student_enrollment_id'
                ]
            );


        $feeHead = FeeHead::where(
            'school_id',
            $schoolId
        )
        ->where('is_optional', 1)
        ->where('status', 1)
        ->findOrFail(
            $validated['fee_head_id']
        );


        DB::transaction(
            function () use (
                $validated,
                $enrollment,
                $feeHead,
                $schoolId
            ) {

                $assignment =
                    StudentOptionalFeeAssignment::create([

                        'school_id' =>
                            $schoolId,

                        'academic_year_id' =>
                            $validated[
                                'academic_year_id'
                            ],

                        'student_id' =>
                            $enrollment->student_id,

                        'student_enrollment_id' =>
                            $enrollment->id,

                        'fee_head_id' =>
                            $feeHead->id,

                        'assigned_date' =>
                            $validated[
                                'assigned_date'
                            ],

                        'effective_from' =>
                            $validated[
                                'effective_from'
                            ] ?? null,

                        'effective_to' =>
                            $validated[
                                'effective_to'
                            ] ?? null,

                        'amount' =>
                            $validated['amount'],

                        'remarks' =>
                            $validated[
                                'remarks'
                            ] ?? null,

                        'assigned_by' =>
                            Auth::id(),

                        'status' => true,
                    ]);


                /*
                 * Optional Fee Assignment initially
                 * creates one due.
                 *
                 * Later, if an optional component
                 * uses Monthly/Quarterly cycles,
                 * we can expand this using its cycle.
                 */
                $amount =
                    round(
                        (float)
                        $validated['amount'],
                        2
                    );


                StudentFeeDue::create([

                    'school_id' =>
                        $schoolId,

                    /*
                     * This is optional fee,
                     * therefore no main assignment.
                     */
                    'student_fee_assignment_id' =>
                        null,

                    'student_optional_fee_assignment_id' =>
                        $assignment->id,

                    'student_id' =>
                        $enrollment->student_id,

                    'student_enrollment_id' =>
                        $enrollment->id,

                    'academic_year_id' =>
                        $validated[
                            'academic_year_id'
                        ],

                    /*
                     * Optional fee is independent
                     * of the student's main template.
                     */
                    'fee_structure_id' =>
                        null,

                    'fee_structure_item_id' =>
                        null,

                    'fee_installment_id' =>
                        null,

                    'fee_head_id' =>
                        $feeHead->id,

                    'fee_head_name' =>
                        $feeHead->name,

                    'installment_name' =>
                        'Optional Fee',

                    'due_date' =>
                        $validated[
                            'effective_from'
                        ]
                        ?? $validated[
                            'assigned_date'
                        ],

                    'base_amount' =>
                        $amount,

                    'discount_amount' =>
                        0,

                    'fine_amount' =>
                        0,

                    'payable_amount' =>
                        $amount,

                    'paid_amount' =>
                        0,

                    'balance_amount' =>
                        $amount,

                    'payment_status' =>
                        'unpaid',

                    'status' =>
                        true,
                ]);
            }
        );


        return redirect()
            ->route(
                'optional-fee-assignments.index'
            )
            ->with(
                'success',
                'Optional fee assigned successfully.'
            );
    }


    public function destroy(
        StudentOptionalFeeAssignment
            $optionalFeeAssignment
    ) {
        abort_unless(
            (int)
            $optionalFeeAssignment->school_id ===
            (int)
            Auth::user()->school_id,
            403
        );


        $hasPayment =
            $optionalFeeAssignment
                ->dues()
                ->where(
                    'paid_amount',
                    '>',
                    0
                )
                ->exists();


        if ($hasPayment) {

            return back()->with(
                'error',
                'Optional fee cannot be deleted because payment has already been received.'
            );
        }


        DB::transaction(
            function () use (
                $optionalFeeAssignment
            ) {

                $optionalFeeAssignment
                    ->dues()
                    ->delete();

                $optionalFeeAssignment
                    ->delete();
            }
        );


        return back()->with(
            'success',
            'Optional fee assignment deleted successfully.'
        );
    }
}