<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\StudentEnrollment;
use App\Models\StudentFeeDue;
use App\Models\StudentFineWaiverAssignment;
use App\Models\StudentFineWaiverAssignmentDue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class FineWaiverAssignmentController extends Controller
{
    public function index(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        $query = StudentFineWaiverAssignment::with([
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
            'fees.fine-waiver-assignments.index',
            compact('assignments', 'academicYears')
        );
    }


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
            'fees.fine-waiver-assignments.create',
            compact('academicYears')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | AJAX - Students
    |--------------------------------------------------------------------------
    */
    public function students(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        $request->validate([
            'academic_year_id' => 'required|integer',
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

        return response()->json(
            $enrollments->map(function ($enrollment) {
                return [
                    'student_id' =>
                        $enrollment->student_id,

                    'enrollment_id' =>
                        $enrollment->id,

                    'student_name' =>
                        $enrollment->student->student_name ?? '',

                    'admission_no' =>
                        $enrollment->student->admission_no ?? '',

                    'class_name' =>
                        $enrollment->schoolClass->name ?? '',

                    'section_name' =>
                        $enrollment->section->name ?? '',
                ];
            })
        );
    }


    /*
    |--------------------------------------------------------------------------
    | AJAX - Fine Dues
    |--------------------------------------------------------------------------
    */
    public function dues(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        $validated = $request->validate([
            'academic_year_id' =>
                'required|integer',

            'student_id' =>
                'required|integer',

            'student_enrollment_id' =>
                'required|integer',
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
            ->where('paid_amount', 0)
            ->where('fine_amount', '>', 0)
            ->whereColumn(
                'fine_amount',
                '>',
                'fine_waiver_amount'
            )
            ->orderBy('id')
            ->get();

        $data = $dues->map(function ($due) {

            $availableFine = max(
                0,
                (float) $due->fine_amount
                -
                (float) ($due->fine_waiver_amount ?? 0)
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

                'fine_amount' =>
                    (float) $due->fine_amount,

                'fine_waiver_amount' =>
                    (float) ($due->fine_waiver_amount ?? 0),

                'available_fine' =>
                    $availableFine,
            ];
        });

        return response()->json($data);
    }


    /*
    |--------------------------------------------------------------------------
    | Store request
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        $validated = $request->validate([
            'academic_year_id' =>
                'required|integer',

            'student_id' =>
                'required|integer',

            'student_enrollment_id' =>
                'required|integer',

            'assigned_date' =>
                'required|date',

            'waiver_mode' => [
                'required',
                Rule::in([
                    'full',
                    'fixed',
                    'percentage',
                ]),
            ],

            'waiver_value' =>
                'nullable|numeric|min:0',

            'due_ids' =>
                'required|array|min:1',

            'due_ids.*' =>
                'required|integer',

            'reason' =>
                'required|string|max:2000',
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
                    'Fine waiver value is required.'
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

        $dueIds = array_values(
            array_unique(
                array_map(
                    'intval',
                    $validated['due_ids']
                )
            )
        );

        $validCount = StudentFeeDue::where(
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
            ->where('fine_amount', '>', 0)
            ->whereColumn(
                'fine_amount',
                '>',
                'fine_waiver_amount'
            )
            ->count();

        if ($validCount !== count($dueIds)) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'One or more selected fines are not eligible for waiver.'
                );
        }

        StudentFineWaiverAssignment::create([
            'school_id' =>
                $schoolId,

            'academic_year_id' =>
                $validated['academic_year_id'],

            'student_id' =>
                $validated['student_id'],

            'student_enrollment_id' =>
                $enrollment->id,

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

        return redirect()
            ->route(
                'fine-waiver-assignments.index'
            )
            ->with(
                'success',
                'Fine waiver request created successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Approve
    |--------------------------------------------------------------------------
    */
    public function approve(
        StudentFineWaiverAssignment $fineWaiverAssignment
    ) {
        $this->authorizeSchool(
            $fineWaiverAssignment
        );

        if (
            $fineWaiverAssignment->approval_status
            !== 'pending'
        ) {
            return back()->with(
                'error',
                'Only pending fine waivers can be approved.'
            );
        }

        if (
            empty(
                $fineWaiverAssignment->selected_due_ids
            )
        ) {
            return back()->with(
                'error',
                'No fee dues are attached to this request.'
            );
        }

        try {

            DB::transaction(function () use (
                $fineWaiverAssignment
            ) {

                $this->applyWaiver(
                    $fineWaiverAssignment
                );

                $fineWaiverAssignment->update([
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
            'Fine waiver approved successfully.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Reject
    |--------------------------------------------------------------------------
    */
    public function reject(
        StudentFineWaiverAssignment $fineWaiverAssignment
    ) {
        $this->authorizeSchool(
            $fineWaiverAssignment
        );

        if (
            $fineWaiverAssignment->approval_status
            !== 'pending'
        ) {
            return back()->with(
                'error',
                'Only pending fine waivers can be rejected.'
            );
        }

        $fineWaiverAssignment->update([
            'approval_status' =>
                'rejected',

            'approved_by' =>
                Auth::id(),

            'approved_at' =>
                now(),
        ]);

        return back()->with(
            'success',
            'Fine waiver request rejected.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Delete / Reverse
    |--------------------------------------------------------------------------
    */
    public function destroy(
        StudentFineWaiverAssignment $fineWaiverAssignment
    ) {
        $this->authorizeSchool(
            $fineWaiverAssignment
        );

        if (
            $fineWaiverAssignment->approval_status
            !== 'approved'
        ) {
            $fineWaiverAssignment->delete();

            return back()->with(
                'success',
                'Fine waiver request deleted.'
            );
        }

        $hasPayment =
            StudentFineWaiverAssignmentDue::where(
                'student_fine_waiver_assignment_id',
                $fineWaiverAssignment->id
            )
            ->whereHas('due', function ($q) {
                $q->where(
                    'paid_amount',
                    '>',
                    0
                );
            })
            ->exists();

        if ($hasPayment) {
            return back()->with(
                'error',
                'Fine waiver cannot be reversed because payment exists against an affected fee due.'
            );
        }

        DB::transaction(function () use (
            $fineWaiverAssignment
        ) {

            $allocations =
                StudentFineWaiverAssignmentDue::where(
                    'student_fine_waiver_assignment_id',
                    $fineWaiverAssignment->id
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

                $newFineWaiver = max(
                    0,
                    (float) $due->fine_waiver_amount
                    -
                    (float) $allocation->waiver_amount
                );

                $newPayable = max(
                    0,

                    (float) $due->base_amount

                    - (float) $due->discount_amount

                    - (float) ($due->waiver_amount ?? 0)

                    + (float) $due->fine_amount

                    - $newFineWaiver
                );

                $newBalance = max(
                    0,
                    $newPayable
                    -
                    (float) $due->paid_amount
                );

                $due->update([
                    'fine_waiver_amount' =>
                        $newFineWaiver,

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
            }

            StudentFineWaiverAssignmentDue::where(
                'student_fine_waiver_assignment_id',
                $fineWaiverAssignment->id
            )->delete();

            $fineWaiverAssignment->delete();
        });

        return back()->with(
            'success',
            'Fine waiver reversed successfully.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Apply fine waiver
    |--------------------------------------------------------------------------
    */
    private function applyWaiver(
        StudentFineWaiverAssignment $assignment
    ): void {

        $dueIds = array_values(
            array_unique(
                array_map(
                    'intval',
                    $assignment->selected_due_ids ?? []
                )
            )
        );

        $dues = StudentFeeDue::where(
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

        $eligible = [];

        foreach ($dues as $due) {

            if ((float) $due->paid_amount > 0) {
                throw new \RuntimeException(
                    'Fine waiver cannot be applied after payment has started.'
                );
            }

            $availableFine = max(
                0,
                (float) $due->fine_amount
                -
                (float) ($due->fine_waiver_amount ?? 0)
            );

            if ($availableFine <= 0) {
                continue;
            }

            $eligible[$due->id] = [
                'due' =>
                    $due,

                'available' =>
                    $availableFine,
            ];
        }

        if (empty($eligible)) {
            throw new \RuntimeException(
                'No outstanding fine is available for waiver.'
            );
        }


        $allocations = [];


        /*
        |--------------------------------------------------------------------------
        | FULL
        |--------------------------------------------------------------------------
        */
        if ($assignment->waiver_mode === 'full') {

            foreach ($eligible as $dueId => $item) {

                $allocations[$dueId] =
                    $item['available'];
            }

        /*
        |--------------------------------------------------------------------------
        | PERCENTAGE
        |--------------------------------------------------------------------------
        */
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
                    'Invalid fine waiver percentage.'
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

                $allocations[$dueId] =
                    min(
                        $amount,
                        $item['available']
                    );
            }

        /*
        |--------------------------------------------------------------------------
        | FIXED
        |--------------------------------------------------------------------------
        */
        } else {

            $remaining =
                (float) $assignment->waiver_value;

            if ($remaining <= 0) {
                throw new \RuntimeException(
                    'Invalid fine waiver amount.'
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
        | Update ledger
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

            $due =
                $eligible[$dueId]['due'];

            $newFineWaiver = round(
                (float) ($due->fine_waiver_amount ?? 0)
                +
                $amount,
                2
            );

            $newPayable = max(
                0,

                (float) $due->base_amount

                - (float) $due->discount_amount

                - (float) ($due->waiver_amount ?? 0)

                + (float) $due->fine_amount

                - $newFineWaiver
            );

            $newBalance = max(
                0,
                $newPayable
                -
                (float) $due->paid_amount
            );

            $due->update([
                'fine_waiver_amount' =>
                    $newFineWaiver,

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

            StudentFineWaiverAssignmentDue::create([
                'student_fine_waiver_assignment_id' =>
                    $assignment->id,

                'student_fee_due_id' =>
                    $due->id,

                'waiver_amount' =>
                    $amount,
            ]);
        }
    }


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


    private function authorizeSchool(
        StudentFineWaiverAssignment $assignment
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