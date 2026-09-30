<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\StudentFeeDue;
use App\Models\StudentFeeWaiverAssignment;
use App\Models\StudentFeeWaiverAssignmentDue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Services\FeeLedgerService;

class FeeWaiverAssignmentController extends Controller
{
    protected FeeLedgerService $ledger;

    public function __construct(
        FeeLedgerService $ledger
    ) {
        $this->ledger = $ledger;
    }

    /*
    |--------------------------------------------------------------------------
    | LIST
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        $query = StudentFeeWaiverAssignment::with([
                'academicYear',
                'student',
                'enrollment.schoolClass',
                'enrollment.section',
                'assignedBy',
                'approvedBy',
                'dueItems.due',
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

        if ($request->filled('student')) {
            $search = trim($request->student);

            $query->whereHas('student', function ($q) use ($search) {
                $q->where('student_name', 'like', "%{$search}%")
                  ->orWhere('admission_no', 'like', "%{$search}%");
            });
        }

        $assignments = $query
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $academicYears = AcademicYear::where(
                'school_id',
                $schoolId
            )
            ->orderByDesc('start_date')
            ->get();

        return view(
            'fees.waiver-assignments.index',
            compact(
                'assignments',
                'academicYears'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */
    public function create()
    {
        $schoolId = Auth::user()->school_id;

        $academicYears = AcademicYear::where(
                'school_id',
                $schoolId
            )
            ->where('status', 1)
            ->orderByDesc('start_date')
            ->get();

        return view(
            'fees.waiver-assignments.create',
            compact('academicYears')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | AJAX STUDENTS
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

        $enrollments = StudentEnrollment::with([
                'student',
                'schoolClass',
                'section',
            ])
            ->where('school_id', $schoolId)
            ->where(
                'academic_year_id',
                $request->academic_year_id
            )
            ->where('status', 1)
            ->where('enrollment_status', 'active')
            ->whereHas('student', function ($q) use ($schoolId) {
                $q->where('school_id', $schoolId)
                  ->where('status', 1);
            })
            ->orderBy('school_class_id')
            ->orderBy('section_id')
            ->get();

        $data = $enrollments->map(function ($enrollment) {
            return [
                'enrollment_id' => $enrollment->id,

                'student_id' =>
                    $enrollment->student_id,

                'student_name' =>
                    $enrollment->student->student_name ?? '',

                'admission_no' =>
                    $enrollment->student->admission_no ?? '',

                'class_name' =>
                    $enrollment->schoolClass->name ?? '',

                'section_name' =>
                    $enrollment->section->name ?? '',
            ];
        });

        return response()->json($data);
    }


    /*
    |--------------------------------------------------------------------------
    | AJAX ELIGIBLE DUES
    |--------------------------------------------------------------------------
    |
    | Only:
    | - same school
    | - same student/year/enrollment
    | - unpaid dues
    | - fee components where allow_waiver = 1
    |
    */
    public function dues(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        $validated = $request->validate([
            'academic_year_id' => [
                'required',
                'integer',
            ],

            'student_id' => [
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
                'id',
                $validated['student_enrollment_id']
            )
            ->where(
                'student_id',
                $validated['student_id']
            )
            ->where(
                'academic_year_id',
                $validated['academic_year_id']
            )
            ->firstOrFail();

        $dues = StudentFeeDue::with([
                'feeHead',
                'feeInstallment',
            ])
            ->where('school_id', $schoolId)
            ->where(
                'academic_year_id',
                $validated['academic_year_id']
            )
            ->where(
                'student_id',
                $validated['student_id']
            )
            ->where(
                'student_enrollment_id',
                $enrollment->id
            )
            ->where('status', 1)

            // First version:
            // no waiver after payment starts.
            ->where('paid_amount', 0)

            ->where('balance_amount', '>', 0)

            ->whereHas('feeHead', function ($q) {
                $q->where('allow_waiver', 1);
            })

            ->orderBy('id')
            ->get();

        $data = $dues->map(function ($due) {

            $availableForWaiver = max(
                0,
                (float) $due->base_amount
                - (float) $due->discount_amount
                - (float) ($due->waiver_amount ?? 0)
            );

            return [
                'id' => $due->id,

                'fee_head' =>
                    $due->fee_head_name
                    ?? $due->feeHead->name
                    ?? 'Fee',

                'installment' =>
                    $due->installment_name
                    ?? $due->feeInstallment->installment_name
                    ?? '-',

                'base_amount' =>
                    (float) $due->base_amount,

                'discount_amount' =>
                    (float) $due->discount_amount,

                'existing_waiver' =>
                    (float) ($due->waiver_amount ?? 0),

                'fine_amount' =>
                    (float) $due->fine_amount,

                'balance_amount' =>
                    (float) $due->balance_amount,

                'available_for_waiver' =>
                    $availableForWaiver,
            ];
        });

        return response()->json($data);
    }


    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        $validated = $request->validate([
            'academic_year_id' => [
                'required',
                'integer',
            ],

            'student_id' => [
                'required',
                'integer',
            ],

            'student_enrollment_id' => [
                'required',
                'integer',
            ],

            'assigned_date' => [
                'required',
                'date',
            ],

            'waiver_mode' => [
                'required',
                Rule::in([
                    'full',
                    'fixed',
                    'percentage',
                ]),
            ],

            'waiver_value' => [
                'nullable',
                'numeric',
                'min:0',
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

            'reason' => [
                'required',
                'string',
                'max:2000',
            ],
        ]);

        if (
            in_array(
                $validated['waiver_mode'],
                ['fixed', 'percentage']
            )
            &&
            empty($validated['waiver_value'])
        ) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Waiver value is required.'
                );
        }

        if (
            $validated['waiver_mode'] === 'percentage'
            &&
            (float) $validated['waiver_value'] > 100
        ) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Percentage cannot exceed 100%.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Verify enrollment
        |--------------------------------------------------------------------------
        */

        $enrollment = StudentEnrollment::where(
                'school_id',
                $schoolId
            )
            ->where(
                'id',
                $validated['student_enrollment_id']
            )
            ->where(
                'student_id',
                $validated['student_id']
            )
            ->where(
                'academic_year_id',
                $validated['academic_year_id']
            )
            ->where('status', 1)
            ->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | Verify selected dues
        |--------------------------------------------------------------------------
        */

        $dueIds = array_values(
            array_unique(
                array_map(
                    'intval',
                    $validated['due_ids']
                )
            )
        );

        $validDues = StudentFeeDue::where(
                'school_id',
                $schoolId
            )
            ->where(
                'academic_year_id',
                $validated['academic_year_id']
            )
            ->where(
                'student_id',
                $validated['student_id']
            )
            ->where(
                'student_enrollment_id',
                $enrollment->id
            )
            ->whereIn('id', $dueIds)
            ->where('status', 1)
            ->where('paid_amount', 0)
            ->whereHas('feeHead', function ($q) {
                $q->where('allow_waiver', 1);
            })
            ->count();

        if ($validDues !== count($dueIds)) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'One or more selected fee dues are not eligible for waiver.'
                );
        }

        DB::transaction(function () use (
            $schoolId,
            $validated,
            $dueIds
        ) {
            StudentFeeWaiverAssignment::create([
                'school_id' =>
                    $schoolId,

                'academic_year_id' =>
                    $validated['academic_year_id'],

                'student_id' =>
                    $validated['student_id'],

                'student_enrollment_id' =>
                    $validated['student_enrollment_id'],

                'assigned_date' =>
                    $validated['assigned_date'],

                'waiver_mode' =>
                    $validated['waiver_mode'],

                'waiver_value' =>
                    $validated['waiver_mode'] === 'full'
                        ? null
                        : $validated['waiver_value'],

                'selected_due_ids' =>
                    $dueIds,

                'reason' =>
                    $validated['reason'],

                'approval_status' =>
                    'pending',

                'assigned_by' =>
                    Auth::id(),

                'status' =>
                    true,
            ]);
        });

        return redirect()
            ->route(
                'fee-waiver-assignments.index'
            )
            ->with(
                'success',
                'Fee waiver request created successfully. Approval is pending.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | APPROVE
    |--------------------------------------------------------------------------
    */
    public function approve(
        StudentFeeWaiverAssignment $feeWaiverAssignment
    ) {
        $this->authorizeSchool(
            $feeWaiverAssignment
        );

        if (
            $feeWaiverAssignment->approval_status
            !== 'pending'
        ) {
            return back()->with(
                'error',
                'Only pending fee waivers can be approved.'
            );
        }

        if (
            empty(
                $feeWaiverAssignment->selected_due_ids
            )
        ) {
            return back()->with(
                'error',
                'No fee dues are attached to this waiver request.'
            );
        }

        try {

            DB::transaction(function () use (
                $feeWaiverAssignment
            ) {
                $this->applyWaiver(
                    $feeWaiverAssignment,
                    $feeWaiverAssignment
                        ->selected_due_ids
                );

                $feeWaiverAssignment->update([
                    'approval_status' =>
                        'approved',

                    'approved_by' =>
                        Auth::id(),

                    'approved_at' =>
                        now(),
                ]);
            });

        } catch (\Throwable $e) {

            return back()->with(
                'error',
                $e->getMessage()
            );
        }

        return back()->with(
            'success',
            'Fee waiver approved and applied successfully.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | REJECT
    |--------------------------------------------------------------------------
    */
    public function reject(
        StudentFeeWaiverAssignment $feeWaiverAssignment
    ) {
        $this->authorizeSchool(
            $feeWaiverAssignment
        );

        if (
            $feeWaiverAssignment->approval_status
            !== 'pending'
        ) {
            return back()->with(
                'error',
                'Only pending fee waivers can be rejected.'
            );
        }

        $feeWaiverAssignment->update([
            'approval_status' =>
                'rejected',

            'approved_by' =>
                Auth::id(),

            'approved_at' =>
                now(),
        ]);

        return back()->with(
            'success',
            'Fee waiver request rejected.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | DELETE / REVERSE
    |--------------------------------------------------------------------------
    */
    public function destroy(
        StudentFeeWaiverAssignment $feeWaiverAssignment
    ) {
        $this->authorizeSchool(
            $feeWaiverAssignment
        );

        /*
        |--------------------------------------------------------------------------
        | Pending / rejected
        |--------------------------------------------------------------------------
        */

        if (
            $feeWaiverAssignment->approval_status
            !== 'approved'
        ) {
            $feeWaiverAssignment->delete();

            return back()->with(
                'success',
                'Fee waiver request deleted successfully.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Do not reverse once payment exists
        |--------------------------------------------------------------------------
        */

        $hasPayment = StudentFeeWaiverAssignmentDue::where(
                'student_fee_waiver_assignment_id',
                $feeWaiverAssignment->id
            )
            ->whereHas('due', function ($q) {
                $q->where('paid_amount', '>', 0);
            })
            ->exists();

        if ($hasPayment) {
            return back()->with(
                'error',
                'This waiver cannot be reversed because payment exists against one or more affected dues.'
            );
        }

        DB::transaction(function () use (
            $feeWaiverAssignment
        ) {

            $allocations =
                StudentFeeWaiverAssignmentDue::where(
                    'student_fee_waiver_assignment_id',
                    $feeWaiverAssignment->id
                )
                ->lockForUpdate()
                ->get();

            foreach ($allocations as $allocation) {

                $due = StudentFeeDue::where(
                        'id',
                        $allocation->student_fee_due_id
                    )
                    ->lockForUpdate()
                    ->first();

                if (!$due) {
                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | Remove only this Fee Waiver assignment's contribution
                |--------------------------------------------------------------------------
                */

                $newWaiver = max(
                    0,
                    (float) ($due->waiver_amount ?? 0)
                    -
                    (float) ($allocation->waiver_amount ?? 0)
                );

                $due->waiver_amount = round(
                    $newWaiver,
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

            StudentFeeWaiverAssignmentDue::where(
                'student_fee_waiver_assignment_id',
                $feeWaiverAssignment->id
            )->delete();

            $feeWaiverAssignment->delete();
        });

        return back()->with(
            'success',
            'Fee waiver reversed successfully.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | APPLY WAIVER
    |--------------------------------------------------------------------------
    */
    private function applyWaiver(
        StudentFeeWaiverAssignment $assignment,
        array $dueIds
    ): void {

        $dues = StudentFeeDue::with('feeHead')
            ->where(
                'school_id',
                $assignment->school_id
            )
            ->where(
                'academic_year_id',
                $assignment->academic_year_id
            )
            ->where(
                'student_id',
                $assignment->student_id
            )
            ->where(
                'student_enrollment_id',
                $assignment->student_enrollment_id
            )
            ->whereIn('id', $dueIds)
            ->where('status', 1)
            ->lockForUpdate()
            ->get();

        if ($dues->count() !== count($dueIds)) {
            throw new \RuntimeException(
                'One or more selected fee dues are invalid.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Build eligible amounts
        |--------------------------------------------------------------------------
        */

        $eligible = [];

        foreach ($dues as $due) {

            if (
                !$due->feeHead
                ||
                !$due->feeHead->allow_waiver
            ) {
                throw new \RuntimeException(
                    'One or more selected fee components do not allow waiver.'
                );
            }

            if ((float) $due->paid_amount > 0) {
                throw new \RuntimeException(
                    'Waiver cannot be applied because payment already exists against one or more selected dues.'
                );
            }

            $available = max(
                0,
                (float) $due->base_amount
                -
                (float) $due->discount_amount
                -
                (float) ($due->waiver_amount ?? 0)
            );

            if ($available <= 0) {
                continue;
            }

            $eligible[$due->id] = [
                'due' => $due,
                'available' => $available,
            ];
        }

        if (empty($eligible)) {
            throw new \RuntimeException(
                'No amount is available for waiver.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Calculate waiver allocation
        |--------------------------------------------------------------------------
        */

        $allocations = [];

        if ($assignment->waiver_mode === 'full') {

            foreach ($eligible as $dueId => $item) {
                $allocations[$dueId] =
                    $item['available'];
            }

        } elseif (
            $assignment->waiver_mode === 'percentage'
        ) {

            $percentage =
                (float) $assignment->waiver_value;

            if (
                $percentage <= 0
                ||
                $percentage > 100
            ) {
                throw new \RuntimeException(
                    'Invalid waiver percentage.'
                );
            }

            foreach ($eligible as $dueId => $item) {

                $amount = round(
                    $item['available']
                    *
                    $percentage
                    /
                    100,
                    2
                );

                $allocations[$dueId] = min(
                    $amount,
                    $item['available']
                );
            }

        } else {

            /*
            |--------------------------------------------------------------------------
            | FIXED AMOUNT
            |--------------------------------------------------------------------------
            |
            | Distribute selected fixed amount sequentially
            | across selected dues.
            |
            */

            $remaining =
                (float) $assignment->waiver_value;

            if ($remaining <= 0) {
                throw new \RuntimeException(
                    'Invalid waiver amount.'
                );
            }

            foreach ($eligible as $dueId => $item) {

                if ($remaining <= 0) {
                    break;
                }

                $amount = min(
                    $remaining,
                    $item['available']
                );

                $allocations[$dueId] =
                    round($amount, 2);

                $remaining -= $amount;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Apply to ledger
        |--------------------------------------------------------------------------
        */

        foreach ($allocations as $dueId => $amount) {

            $amount = round(
                (float) $amount,
                2
            );

            if ($amount <= 0) {
                continue;
            }

            $due = $eligible[$dueId]['due'];

            $newWaiver = round(
                (float) ($due->waiver_amount ?? 0)
                +
                $amount,
                2
            );

            $newPayable = max(
                0,
                (float) $due->base_amount
                -
                (float) $due->discount_amount
                -
                $newWaiver
                +
                (float) $due->fine_amount
            );

            $newBalance = max(
                0,
                $newPayable
                -
                (float) $due->paid_amount
            );

            $due->update([
                'waiver_amount' =>
                    $newWaiver,

                'payable_amount' =>
                    $newPayable,

                'balance_amount' =>
                    $newBalance,

                'payment_status' =>
                    $this->paymentStatus(
                        (float) $due->paid_amount,
                        $newPayable
                    ),
            ]);

            StudentFeeWaiverAssignmentDue::create([
                'student_fee_waiver_assignment_id' =>
                    $assignment->id,

                'student_fee_due_id' =>
                    $due->id,

                'waiver_amount' =>
                    $amount,
            ]);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | PAYMENT STATUS
    |--------------------------------------------------------------------------
    */
    private function paymentStatus(
        float $paid,
        float $payable
    ): string {

        if ($payable <= 0) {
            return 'paid';
        }

        if ($paid <= 0) {
            return 'unpaid';
        }

        if ($paid >= $payable) {
            return 'paid';
        }

        return 'partially_paid';
    }


    /*
    |--------------------------------------------------------------------------
    | SCHOOL SECURITY
    |--------------------------------------------------------------------------
    */
    private function authorizeSchool(
        StudentFeeWaiverAssignment $assignment
    ): void {

        if (
            (int) $assignment->school_id
            !==
            (int) Auth::user()->school_id
        ) {
            abort(403);
        }
    }
}