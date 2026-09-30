<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\ConcessionType;
use App\Models\StudentConcessionAssignment;
use App\Models\StudentConcessionAssignmentDue;
use App\Models\StudentEnrollment;
use App\Models\StudentFeeDue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Services\FeeLedgerService;

class ConcessionAssignmentController extends Controller
{
    protected FeeLedgerService $ledger;

    public function __construct(
        FeeLedgerService $ledger
    ) {
        $this->ledger = $ledger;
    }


    public function index(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        $query = StudentConcessionAssignment::with([
            'academicYear',
            'student',
            'enrollment.schoolClass',
            'enrollment.section',
            'concessionType',
            'assignedBy',
            'approvedBy',
        ])
        ->where('school_id', $schoolId);

        if ($request->filled('academic_year_id')) {
            $query->where(
                'academic_year_id',
                $request->academic_year_id
            );
        }

        if ($request->filled('approval_status')) {
            $query->where(
                'approval_status',
                $request->approval_status
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
            'concession-assignments.index',
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

        $concessionTypes = ConcessionType::where(
            'school_id',
            $schoolId
        )
        ->where('status', 1)
        ->orderBy('sort_order')
        ->orderBy('name')
        ->get();

        return view(
            'concession-assignments.create',
            compact(
                'academicYears',
                'concessionTypes'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Search enrolled students
    |--------------------------------------------------------------------------
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
        ->where('school_id', $schoolId)
        ->where(
            'academic_year_id',
            $request->academic_year_id
        )
        ->where('status', 1);

        if ($request->filled('search')) {

            $search = trim($request->search);

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

        $students = $query
            ->limit(100)
            ->get()
            ->map(function ($enrollment) {

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
            });

        return response()->json($students);
    }


    /*
    |--------------------------------------------------------------------------
    | Get unpaid dues for selected student
    |--------------------------------------------------------------------------
    */

    public function dues(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        $validated = $request->validate([
            'academic_year_id' => [
                'required',
                'integer',
            ],

            'student_enrollment_id' => [
                'required',
                'integer',
            ],
        ]);

        $enrollment = StudentEnrollment::where(
            'school_id',
            $schoolId
        )
        ->where(
            'academic_year_id',
            $validated['academic_year_id']
        )
        ->findOrFail(
            $validated['student_enrollment_id']
        );

        $dues = StudentFeeDue::where(
            'school_id',
            $schoolId
        )
        ->where(
            'student_id',
            $enrollment->student_id
        )
        ->where(
            'academic_year_id',
            $validated['academic_year_id']
        )
        ->where('status', 1)

        /*
         * Do not alter paid/partially paid dues.
         */
        ->where('paid_amount', 0)

        ->orderBy('due_date')
        ->get([
            'id',
            'fee_head_name',
            'installment_name',
            'due_date',
            'base_amount',
            'discount_amount',
            'payable_amount',
            'balance_amount',
        ]);

        return response()->json($dues);
    }


    /*
    |--------------------------------------------------------------------------
    | Store
    |--------------------------------------------------------------------------
    */

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

            'concession_type_id' => [
                'required',
                Rule::exists(
                    'concession_types',
                    'id'
                )->where(
                    fn ($q) =>
                        $q->where(
                            'school_id',
                            $schoolId
                        )
                ),
            ],

            'concession_mode' => [
                'required',
                Rule::in([
                    'fixed',
                    'percentage',
                ]),
            ],

            'concession_value' => [
                'required',
                'numeric',
                'min:0.01',
            ],

            'maximum_amount' => [
                'nullable',
                'numeric',
                'min:0',
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

            'due_ids' => [
                'required',
                'array',
                'min:1',
            ],

            'due_ids.*' => [
                'required',
                'integer',
            ],

            'remarks' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);


        if (
            $validated['concession_mode']
                === 'percentage'
            &&
            $validated['concession_value'] > 100
        ) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Percentage concession cannot exceed 100%.'
                );
        }


        $enrollment = StudentEnrollment::where(
            'school_id',
            $schoolId
        )
        ->where(
            'academic_year_id',
            $validated['academic_year_id']
        )
        ->findOrFail(
            $validated['student_enrollment_id']
        );


        $type = ConcessionType::where(
            'school_id',
            $schoolId
        )
        ->where('status', 1)
        ->findOrFail(
            $validated['concession_type_id']
        );


        /*
         * Approval comes from the master.
         */
        $approvalStatus =
            $type->requires_approval
                ? 'pending'
                : 'approved';


        DB::transaction(
            function () use (
                $validated,
                $enrollment,
                $type,
                $approvalStatus,
                $schoolId
            ) {

                $assignment =
                    StudentConcessionAssignment::create([

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

                        'concession_type_id' =>
                            $type->id,

                        'concession_mode' =>
                            $validated[
                                'concession_mode'
                            ],

                        'concession_value' =>
                            $validated[
                                'concession_value'
                            ],

                        'maximum_amount' =>
                            $validated[
                                'maximum_amount'
                            ] ?? null,

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

                        // ADD HERE
                        'selected_due_ids' =>
                            array_map(
                                'intval',
                                $validated['due_ids']
                            ),    

                        'approval_status' =>
                            $approvalStatus,

                        'approved_by' =>
                            $approvalStatus === 'approved'
                                ? Auth::id()
                                : null,

                        'approved_at' =>
                            $approvalStatus === 'approved'
                                ? now()
                                : null,

                        'remarks' =>
                            $validated['remarks']
                            ?? null,

                        'assigned_by' =>
                            Auth::id(),

                        'status' => true,
                    ]);


                /*
                 * Pending concession does NOT
                 * modify the financial ledger.
                 */
                if (
                    $approvalStatus === 'approved'
                ) {
                    $this->applyConcession(
                        $assignment,
                        $validated['due_ids']
                    );
                }
            }
        );


        $message =
            $approvalStatus === 'pending'
                ? 'Concession submitted for approval.'
                : 'Concession assigned successfully.';


        return redirect()
            ->route(
                'concession-assignments.index'
            )
            ->with(
                'success',
                $message
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Approve
    |--------------------------------------------------------------------------
    */

    public function approve(
        StudentConcessionAssignment $concessionAssignment
    ) {
        $this->authorizeSchool(
            $concessionAssignment
        );

        if (
            $concessionAssignment->approval_status
            !== 'pending'
        ) {
            return back()->with(
                'error',
                'Only pending concessions can be approved.'
            );
        }

        if (
            empty(
                $concessionAssignment->selected_due_ids
            )
        ) {
            return back()->with(
                'error',
                'No fee dues are attached to this concession request.'
            );
        }

        DB::transaction(
            function () use (
                $concessionAssignment
            ) {

                $this->applyConcession(
                    $concessionAssignment,
                    $concessionAssignment
                        ->selected_due_ids
                );

                $concessionAssignment->update([
                    'approval_status' =>
                        'approved',

                    'approved_by' =>
                        Auth::id(),

                    'approved_at' =>
                        now(),
                ]);
            }
        );

        return back()->with(
            'success',
            'Concession approved and applied successfully.'
        );
    }

    public function reject(
        StudentConcessionAssignment $concessionAssignment
    ) {
        $this->authorizeSchool(
            $concessionAssignment
        );

        if (
            $concessionAssignment->approval_status
            !== 'pending'
        ) {
            return back()->with(
                'error',
                'Only pending concessions can be rejected.'
            );
        }

        $concessionAssignment->update([
            'approval_status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return back()->with(
            'success',
            'Concession request rejected.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Delete / Reverse
    |--------------------------------------------------------------------------
    */

    public function destroy(
        StudentConcessionAssignment
            $concessionAssignment
    ) {
        $this->authorizeSchool(
            $concessionAssignment
        );


        DB::transaction(
            function () use (
                $concessionAssignment
            ) {

                /*
                 * Reverse only our own concession
                 * contribution.
                 */
                foreach (
                    $concessionAssignment
                        ->dueItems()
                        ->with('due')
                        ->get()
                    as $item
                ) {

                    $due = $item->due;

                    if (!$due) {
                        continue;
                    }

                    if ((float) $due->paid_amount > 0) {
                        throw new \RuntimeException(
                            'Cannot remove concession because one or more affected dues have payment history.'
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Remove only this concession assignment's contribution
                    |--------------------------------------------------------------------------
                    */

                    $newDiscount = max(
                        0,
                        (float) ($due->discount_amount ?? 0)
                        -
                        (float) ($item->concession_amount ?? 0)
                    );

                    $due->discount_amount = round(
                        $newDiscount,
                        2
                    );

                    $due->save();

                    /*
                    |--------------------------------------------------------------------------
                    | Recalculate complete fee ledger
                    |--------------------------------------------------------------------------
                    */

                    $this->ledger->recalculateDue($due);
                }


                $concessionAssignment
                    ->delete();
            }
        );


        return back()->with(
            'success',
            'Concession assignment removed successfully.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Apply concession
    |--------------------------------------------------------------------------
    */

    private function applyConcession(
        StudentConcessionAssignment $assignment,
        array $dueIds
    ): void {

        $dues = StudentFeeDue::where(
            'school_id',
            $assignment->school_id
        )
        ->where(
            'student_id',
            $assignment->student_id
        )
        ->where(
            'academic_year_id',
            $assignment->academic_year_id
        )
        ->whereIn('id', $dueIds)
        ->where('status', 1)
        ->where('paid_amount', 0)
        ->lockForUpdate()
        ->get();


        /*
         * Fixed concession is treated as the
         * total amount distributed across the
         * selected dues.
         */
        $fixedRemaining =
            $assignment->concession_mode === 'fixed'
                ? (float)
                    $assignment->concession_value
                : null;


        $overallRemaining =
            $assignment->maximum_amount !== null
                ? (float)
                    $assignment->maximum_amount
                : null;


        foreach ($dues as $due) {

            /*
             * Existing concessions must be
             * respected.
             */
            $availableAmount = max(
                0,
                (float) $due->base_amount
                -
                (float) $due->discount_amount
            );


            if ($availableAmount <= 0) {
                continue;
            }


            if (
                $assignment->concession_mode
                    === 'percentage'
            ) {

                $amount =
                    $availableAmount
                    *
                    (
                        (float)
                        $assignment->concession_value
                        / 100
                    );

            } else {

                if ($fixedRemaining <= 0) {
                    break;
                }

                $amount = min(
                    $availableAmount,
                    $fixedRemaining
                );
            }


            if ($overallRemaining !== null) {

                if ($overallRemaining <= 0) {
                    break;
                }

                $amount = min(
                    $amount,
                    $overallRemaining
                );
            }


            $amount = round(
                max(0, $amount),
                2
            );


            if ($amount <= 0) {
                continue;
            }


            $newDiscount =
                round(
                    (float)
                    $due->discount_amount
                    + $amount,
                    2
                );


            $newPayable = max(
                0,
                (float) $due->base_amount
                -
                $newDiscount
                +
                (float) $due->fine_amount
            );


            $due->update([

                'discount_amount' =>
                    $newDiscount,

                'payable_amount' =>
                    round(
                        $newPayable,
                        2
                    ),

                'balance_amount' =>
                    round(
                        $newPayable,
                        2
                    ),
            ]);


            StudentConcessionAssignmentDue::create([

                'student_concession_assignment_id' =>
                    $assignment->id,

                'student_fee_due_id' =>
                    $due->id,

                'concession_amount' =>
                    $amount,
            ]);


            if ($fixedRemaining !== null) {

                $fixedRemaining -=
                    $amount;
            }


            if ($overallRemaining !== null) {

                $overallRemaining -=
                    $amount;
            }
        }
    }


    private function authorizeSchool(
        StudentConcessionAssignment $assignment
    ): void {

        abort_unless(
            (int) $assignment->school_id ===
            (int) Auth::user()->school_id,
            403
        );
    }
}