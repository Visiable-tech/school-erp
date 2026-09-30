<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\StudentEnrollment;
use App\Models\StudentFeeDue;
use App\Models\StudentCompositeConcession;
use App\Models\StudentCompositeConcessionDue;
use App\Services\FeeLedgerService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CompositeConcessionController extends Controller
{
    protected FeeLedgerService $ledger;


    public function __construct(
        FeeLedgerService $ledger
    ) {
        $this->ledger = $ledger;
    }


    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        $query = StudentCompositeConcession::with([
                'academicYear',
                'student',
                'enrollment.schoolClass',
                'enrollment.section',
                'assignedBy',
                'approvedBy',
                'dueItems.due',
            ])
            ->where(
                'school_id',
                $schoolId
            );


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

            $search = trim(
                $request->student
            );

            $query->whereHas(
                'student',
                function ($q) use ($search) {

                    $q->where(
                        'student_name',
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
            ->orderByDesc('start_date')
            ->get();


        return view(
            'fees.composite-concessions.index',
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
            'fees.composite-concessions.create',
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


        $validated = $request->validate([
            'academic_year_id' =>
                'required|integer',
        ]);


        $enrollments = StudentEnrollment::with([
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
                $validated['academic_year_id']
            )
            ->where('status', 1)
            ->whereHas(
                'student',
                function ($q) use ($schoolId) {

                    $q->where(
                        'school_id',
                        $schoolId
                    )
                    ->where(
                        'status',
                        1
                    );
                }
            )
            ->orderBy('school_class_id')
            ->orderBy('section_id')
            ->get();


        $data = $enrollments->map(
            function ($enrollment) {

                return [

                    'student_id' =>
                        $enrollment->student_id,

                    'enrollment_id' =>
                        $enrollment->id,

                    'student_name' =>
                        $enrollment
                            ->student
                            ->student_name
                        ?? '',

                    'admission_no' =>
                        $enrollment
                            ->student
                            ->admission_no
                        ?? '',

                    'class_name' =>
                        $enrollment
                            ->schoolClass
                            ->name
                        ?? '',

                    'section_name' =>
                        $enrollment
                            ->section
                            ->name
                        ?? '',
                ];
            }
        );


        return response()->json($data);
    }


    /*
    |--------------------------------------------------------------------------
    | AJAX DUES
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
                $validated[
                    'student_enrollment_id'
                ]
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
        | For first version we do not modify dues
        | where payment has already started.
        |--------------------------------------------------------------------------
        */

        $dues = StudentFeeDue::with([
                'feeHead',
                'feeInstallment',
            ])
            ->where(
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
            ->where('status', 1)
            ->where('paid_amount', 0)
            ->orderBy('id')
            ->get();


        $data = $dues->map(
            function ($due) {

                $remainingFee =
                    $this->ledger
                        ->remainingFee($due);

                $remainingFine =
                    $this->ledger
                        ->remainingFine($due);


                return [

                    'id' =>
                        $due->id,

                    'fee_head' =>
                        $due->fee_head_name
                        ??
                        $due->feeHead->name
                        ??
                        'Fee',

                    'installment' =>
                        $due->installment_name
                        ??
                        $due
                            ->feeInstallment
                            ->installment_name
                        ??
                        '-',

                    'base_amount' =>
                        (float)
                        $due->base_amount,

                    'discount_amount' =>
                        (float)
                        ($due->discount_amount ?? 0),

                    'waiver_amount' =>
                        (float)
                        ($due->waiver_amount ?? 0),

                    'fine_amount' =>
                        (float)
                        ($due->fine_amount ?? 0),

                    'fine_waiver_amount' =>
                        (float)
                        ($due->fine_waiver_amount ?? 0),

                    'remaining_fee' =>
                        $remainingFee,

                    'remaining_fine' =>
                        $remainingFine,

                    'allow_concession' =>
                        (bool)
                        (
                            $due->feeHead
                                ->allow_concession
                            ?? false
                        ),

                    'allow_waiver' =>
                        (bool)
                        (
                            $due->feeHead
                                ->allow_waiver
                            ?? false
                        ),
                ];
            }
        );


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

            'academic_year_id' =>
                'required|integer',

            'student_id' =>
                'required|integer',

            'student_enrollment_id' =>
                'required|integer',

            'assigned_date' =>
                'required|date',


            /*
            |--------------------------------------------------------------------------
            | Concession
            |--------------------------------------------------------------------------
            */

            'concession_mode' => [
                'required',
                Rule::in([
                    'none',
                    'fixed',
                    'percentage',
                ]),
            ],

            'concession_value' =>
                'nullable|numeric|min:0',


            /*
            |--------------------------------------------------------------------------
            | Fee waiver
            |--------------------------------------------------------------------------
            */

            'fee_waiver_mode' => [
                'required',
                Rule::in([
                    'none',
                    'full',
                    'fixed',
                    'percentage',
                ]),
            ],

            'fee_waiver_value' =>
                'nullable|numeric|min:0',


            /*
            |--------------------------------------------------------------------------
            | Fine waiver
            |--------------------------------------------------------------------------
            */

            'fine_waiver_mode' => [
                'required',
                Rule::in([
                    'none',
                    'full',
                    'fixed',
                    'percentage',
                ]),
            ],

            'fine_waiver_value' =>
                'nullable|numeric|min:0',


            'due_ids' =>
                'required|array|min:1',

            'due_ids.*' =>
                'required|integer',

            'reason' =>
                'required|string|max:2000',
        ]);


        /*
        |--------------------------------------------------------------------------
        | At least one benefit
        |--------------------------------------------------------------------------
        */

        if (
            $validated['concession_mode']
                === 'none'
            &&
            $validated['fee_waiver_mode']
                === 'none'
            &&
            $validated['fine_waiver_mode']
                === 'none'
        ) {

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Please select at least one concession or waiver.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Validate values
        |--------------------------------------------------------------------------
        */

        $valueError =
            $this->validateBenefitValues(
                $validated
            );

        if ($valueError) {

            return back()
                ->withInput()
                ->with(
                    'error',
                    $valueError
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Verify enrollment
        |--------------------------------------------------------------------------
        */

        $enrollment =
            StudentEnrollment::where(
                'school_id',
                $schoolId
            )
            ->where(
                'id',
                $validated[
                    'student_enrollment_id'
                ]
            )
            ->where(
                'student_id',
                $validated['student_id']
            )
            ->where(
                'academic_year_id',
                $validated[
                    'academic_year_id'
                ]
            )
            ->where('status', 1)
            ->firstOrFail();


        /*
        |--------------------------------------------------------------------------
        | Clean due IDs
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


        /*
        |--------------------------------------------------------------------------
        | Verify all dues
        |--------------------------------------------------------------------------
        */

        $validDues =
            StudentFeeDue::with('feeHead')
            ->where(
                'school_id',
                $schoolId
            )
            ->where(
                'academic_year_id',
                $validated[
                    'academic_year_id'
                ]
            )
            ->where(
                'student_id',
                $validated['student_id']
            )
            ->where(
                'student_enrollment_id',
                $enrollment->id
            )
            ->whereIn(
                'id',
                $dueIds
            )
            ->where('status', 1)
            ->where('paid_amount', 0)
            ->get();


        if (
            $validDues->count()
            !== count($dueIds)
        ) {

            return back()
                ->withInput()
                ->with(
                    'error',
                    'One or more selected fee dues are invalid.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Check selected benefits are allowed
        |--------------------------------------------------------------------------
        */

        foreach ($validDues as $due) {

            if (
                $validated['concession_mode']
                    !== 'none'
                &&
                (
                    !$due->feeHead
                    ||
                    !$due
                        ->feeHead
                        ->allow_concession
                )
            ) {

                return back()
                    ->withInput()
                    ->with(
                        'error',
                        'One or more selected fee components do not allow concession.'
                    );
            }


            if (
                $validated['fee_waiver_mode']
                    !== 'none'
                &&
                (
                    !$due->feeHead
                    ||
                    !$due
                        ->feeHead
                        ->allow_waiver
                )
            ) {

                return back()
                    ->withInput()
                    ->with(
                        'error',
                        'One or more selected fee components do not allow fee waiver.'
                    );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Create pending request
        |--------------------------------------------------------------------------
        */

        StudentCompositeConcession::create([

            'school_id' =>
                $schoolId,

            'academic_year_id' =>
                $validated[
                    'academic_year_id'
                ],

            'student_id' =>
                $validated[
                    'student_id'
                ],

            'student_enrollment_id' =>
                $enrollment->id,

            'assigned_date' =>
                $validated[
                    'assigned_date'
                ],


            'concession_mode' =>
                $validated[
                    'concession_mode'
                ],

            'concession_value' =>
                $validated[
                    'concession_mode'
                ] === 'none'
                    ? null
                    : $validated[
                        'concession_value'
                    ],


            'fee_waiver_mode' =>
                $validated[
                    'fee_waiver_mode'
                ],

            'fee_waiver_value' =>
                in_array(
                    $validated[
                        'fee_waiver_mode'
                    ],
                    ['none', 'full']
                )
                    ? null
                    : $validated[
                        'fee_waiver_value'
                    ],


            'fine_waiver_mode' =>
                $validated[
                    'fine_waiver_mode'
                ],

            'fine_waiver_value' =>
                in_array(
                    $validated[
                        'fine_waiver_mode'
                    ],
                    ['none', 'full']
                )
                    ? null
                    : $validated[
                        'fine_waiver_value'
                    ],


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
                'composite-concessions.index'
            )
            ->with(
                'success',
                'Composite concession request created successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | APPROVE
    |--------------------------------------------------------------------------
    */

    public function approve(
        StudentCompositeConcession $compositeConcession
    ) {

        $this->authorizeSchool(
            $compositeConcession
        );


        if (
            $compositeConcession
                ->approval_status
            !== 'pending'
        ) {

            return back()->with(
                'error',
                'Only pending composite concessions can be approved.'
            );
        }


        if (
            empty(
                $compositeConcession
                    ->selected_due_ids
            )
        ) {

            return back()->with(
                'error',
                'No fee dues are attached to this request.'
            );
        }


        try {

            DB::transaction(
                function () use (
                    $compositeConcession
                ) {

                    $this->applyComposite(
                        $compositeConcession
                    );


                    $compositeConcession
                        ->update([

                            'approval_status' =>
                                'approved',

                            'approved_by' =>
                                Auth::id(),

                            'approved_at' =>
                                now(),
                        ]);
                }
            );

        } catch (\Throwable $e) {

            return back()->with(
                'error',
                $e->getMessage()
            );
        }


        return back()->with(
            'success',
            'Composite concession approved and applied successfully.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | REJECT
    |--------------------------------------------------------------------------
    */

    public function reject(
        StudentCompositeConcession $compositeConcession
    ) {

        $this->authorizeSchool(
            $compositeConcession
        );


        if (
            $compositeConcession
                ->approval_status
            !== 'pending'
        ) {

            return back()->with(
                'error',
                'Only pending composite concessions can be rejected.'
            );
        }


        $compositeConcession->update([

            'approval_status' =>
                'rejected',

            'approved_by' =>
                Auth::id(),

            'approved_at' =>
                now(),
        ]);


        return back()->with(
            'success',
            'Composite concession rejected.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | DELETE / REVERSE
    |--------------------------------------------------------------------------
    */

    public function destroy(
        StudentCompositeConcession $compositeConcession
    ) {

        $this->authorizeSchool(
            $compositeConcession
        );


        /*
        |--------------------------------------------------------------------------
        | Pending / rejected can simply be deleted
        |--------------------------------------------------------------------------
        */

        if (
            $compositeConcession
                ->approval_status
            !== 'approved'
        ) {

            $compositeConcession->delete();


            return back()->with(
                'success',
                'Composite concession deleted successfully.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Check payments
        |--------------------------------------------------------------------------
        */

        $hasPayment =
            StudentCompositeConcessionDue::where(
                'student_composite_concession_id',
                $compositeConcession->id
            )
            ->whereHas(
                'due',
                function ($q) {

                    $q->where(
                        'paid_amount',
                        '>',
                        0
                    );
                }
            )
            ->exists();


        if ($hasPayment) {

            return back()->with(
                'error',
                'This composite concession cannot be reversed because payment exists against an affected due.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Reverse only this transaction's contribution
        |--------------------------------------------------------------------------
        */

        DB::transaction(
            function () use (
                $compositeConcession
            ) {

                $allocations =
                    StudentCompositeConcessionDue::where(
                        'student_composite_concession_id',
                        $compositeConcession->id
                    )
                    ->lockForUpdate()
                    ->get();


                foreach (
                    $allocations as $allocation
                ) {

                    $due =
                        StudentFeeDue::where(
                            'id',
                            $allocation
                                ->student_fee_due_id
                        )
                        ->where(
                            'school_id',
                            $compositeConcession
                                ->school_id
                        )
                        ->lockForUpdate()
                        ->first();


                    if (!$due) {
                        continue;
                    }


                    $due->discount_amount =
                        max(
                            0,

                            (float)
                            $due->discount_amount

                            -

                            (float)
                            $allocation
                                ->concession_amount
                        );


                    $due->waiver_amount =
                        max(
                            0,

                            (float)
                            ($due->waiver_amount ?? 0)

                            -

                            (float)
                            $allocation
                                ->fee_waiver_amount
                        );


                    $due->fine_waiver_amount =
                        max(
                            0,

                            (float)
                            (
                                $due
                                    ->fine_waiver_amount
                                ?? 0
                            )

                            -

                            (float)
                            $allocation
                                ->fine_waiver_amount
                        );


                    $due->save();


                    /*
                    |--------------------------------------------------------------------------
                    | Central ledger calculation
                    |--------------------------------------------------------------------------
                    */

                    $this->ledger
                        ->recalculateDue($due);
                }


                StudentCompositeConcessionDue::where(
                    'student_composite_concession_id',
                    $compositeConcession->id
                )->delete();


                $compositeConcession->delete();
            }
        );


        return back()->with(
            'success',
            'Composite concession reversed successfully.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | APPLY COMPOSITE
    |--------------------------------------------------------------------------
    */

    private function applyComposite(
        StudentCompositeConcession $assignment
    ): void {

        $dueIds = array_values(
            array_unique(
                array_map(
                    'intval',
                    $assignment
                        ->selected_due_ids
                    ?? []
                )
            )
        );


        /*
        |--------------------------------------------------------------------------
        | Lock dues
        |--------------------------------------------------------------------------
        */

        $dues = StudentFeeDue::with(
                'feeHead'
            )
            ->where(
                'school_id',
                $assignment->school_id
            )
            ->where(
                'academic_year_id',
                $assignment
                    ->academic_year_id
            )
            ->where(
                'student_id',
                $assignment->student_id
            )
            ->where(
                'student_enrollment_id',
                $assignment
                    ->student_enrollment_id
            )
            ->whereIn(
                'id',
                $dueIds
            )
            ->where('status', 1)
            ->lockForUpdate()
            ->get();


        if (
            $dues->count()
            !== count($dueIds)
        ) {

            throw new \RuntimeException(
                'One or more selected fee dues are invalid.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Payment check
        |--------------------------------------------------------------------------
        */

        foreach ($dues as $due) {

            if (
                (float)
                $due->paid_amount
                > 0
            ) {

                throw new \RuntimeException(
                    'Composite concession cannot be applied because payment has already started against one or more selected dues.'
                );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Concession allocations
        |--------------------------------------------------------------------------
        */

        $concessionAllocations =
            $this->calculateFeeBenefit(
                $dues,
                $assignment
                    ->concession_mode,
                $assignment
                    ->concession_value,
                'concession'
            );


        /*
        |--------------------------------------------------------------------------
        | We need to calculate fee waiver AFTER considering
        | the concession being added by this same transaction.
        |--------------------------------------------------------------------------
        */

        $feeWaiverAllocations =
            $this->calculateFeeWaiver(
                $dues,
                $assignment
                    ->fee_waiver_mode,
                $assignment
                    ->fee_waiver_value,
                $concessionAllocations
            );


        /*
        |--------------------------------------------------------------------------
        | Fine waiver
        |--------------------------------------------------------------------------
        */

        $fineWaiverAllocations =
            $this->calculateFineWaiver(
                $dues,
                $assignment
                    ->fine_waiver_mode,
                $assignment
                    ->fine_waiver_value
            );


        /*
        |--------------------------------------------------------------------------
        | Apply all allocations
        |--------------------------------------------------------------------------
        */

        foreach ($dues as $due) {

            $concessionAmount =
                round(
                    (float)
                    (
                        $concessionAllocations[
                            $due->id
                        ]
                        ?? 0
                    ),
                    2
                );


            $feeWaiverAmount =
                round(
                    (float)
                    (
                        $feeWaiverAllocations[
                            $due->id
                        ]
                        ?? 0
                    ),
                    2
                );


            $fineWaiverAmount =
                round(
                    (float)
                    (
                        $fineWaiverAllocations[
                            $due->id
                        ]
                        ?? 0
                    ),
                    2
                );


            /*
            |--------------------------------------------------------------------------
            | Nothing to apply
            |--------------------------------------------------------------------------
            */

            if (
                $concessionAmount <= 0
                &&
                $feeWaiverAmount <= 0
                &&
                $fineWaiverAmount <= 0
            ) {
                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | Add contributions
            |--------------------------------------------------------------------------
            */

            $due->discount_amount =
                round(
                    (float)
                    ($due->discount_amount ?? 0)

                    +

                    $concessionAmount,
                    2
                );


            $due->waiver_amount =
                round(
                    (float)
                    ($due->waiver_amount ?? 0)

                    +

                    $feeWaiverAmount,
                    2
                );


            $due->fine_waiver_amount =
                round(
                    (float)
                    (
                        $due
                            ->fine_waiver_amount
                        ?? 0
                    )

                    +

                    $fineWaiverAmount,
                    2
                );


            $due->save();


            /*
            |--------------------------------------------------------------------------
            | THIS is where Section 5 calculation rule is used
            |--------------------------------------------------------------------------
            */

            $this->ledger
                ->recalculateDue($due);


            /*
            |--------------------------------------------------------------------------
            | Audit allocation
            |--------------------------------------------------------------------------
            */

            StudentCompositeConcessionDue::create([

                'student_composite_concession_id' =>
                    $assignment->id,

                'student_fee_due_id' =>
                    $due->id,

                'concession_amount' =>
                    $concessionAmount,

                'fee_waiver_amount' =>
                    $feeWaiverAmount,

                'fine_waiver_amount' =>
                    $fineWaiverAmount,
            ]);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | CALCULATE CONCESSION
    |--------------------------------------------------------------------------
    */

    private function calculateFeeBenefit(
        $dues,
        string $mode,
        $value,
        string $type
    ): array {

        $allocations = [];


        if ($mode === 'none') {
            return $allocations;
        }


        /*
        |--------------------------------------------------------------------------
        | Eligible dues
        |--------------------------------------------------------------------------
        */

        $eligible = [];


        foreach ($dues as $due) {

            if (
                !$due->feeHead
                ||
                !$due
                    ->feeHead
                    ->allow_concession
            ) {

                throw new \RuntimeException(
                    'One or more selected fee components do not allow concession.'
                );
            }


            $available =
                $this->ledger
                    ->remainingFee($due);


            if ($available > 0) {

                $eligible[$due->id] = [
                    'due' =>
                        $due,

                    'available' =>
                        $available,
                ];
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Percentage
        |--------------------------------------------------------------------------
        */

        if ($mode === 'percentage') {

            $percentage =
                (float) $value;


            if (
                $percentage <= 0
                ||
                $percentage > 100
            ) {

                throw new \RuntimeException(
                    'Invalid concession percentage.'
                );
            }


            foreach (
                $eligible as $dueId => $item
            ) {

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


            return $allocations;
        }


        /*
        |--------------------------------------------------------------------------
        | Fixed
        |--------------------------------------------------------------------------
        */

        $remaining =
            (float) $value;


        if ($remaining <= 0) {

            throw new \RuntimeException(
                'Invalid concession amount.'
            );
        }


        foreach (
            $eligible as $dueId => $item
        ) {

            if ($remaining <= 0) {
                break;
            }


            $amount = min(
                $remaining,
                $item['available']
            );


            $allocations[$dueId] =
                round(
                    $amount,
                    2
                );


            $remaining -= $amount;
        }


        return $allocations;
    }


    /*
    |--------------------------------------------------------------------------
    | CALCULATE FEE WAIVER
    |--------------------------------------------------------------------------
    */

    private function calculateFeeWaiver(
        $dues,
        string $mode,
        $value,
        array $concessionAllocations
    ): array {

        $allocations = [];


        if ($mode === 'none') {
            return $allocations;
        }


        $eligible = [];


        foreach ($dues as $due) {

            if (
                !$due->feeHead
                ||
                !$due
                    ->feeHead
                    ->allow_waiver
            ) {

                throw new \RuntimeException(
                    'One or more selected fee components do not allow fee waiver.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Remaining amount AFTER new composite concession
            |--------------------------------------------------------------------------
            */

            $available = max(
                0,

                (float)
                $due->base_amount

                -

                (float)
                ($due->discount_amount ?? 0)

                -

                (float)
                ($due->waiver_amount ?? 0)

                -

                (float)
                (
                    $concessionAllocations[
                        $due->id
                    ]
                    ?? 0
                )
            );


            if ($available > 0) {

                $eligible[$due->id] = [
                    'due' =>
                        $due,

                    'available' =>
                        $available,
                ];
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Full
        |--------------------------------------------------------------------------
        */

        if ($mode === 'full') {

            foreach (
                $eligible as $dueId => $item
            ) {

                $allocations[$dueId] =
                    $item['available'];
            }


            return $allocations;
        }


        /*
        |--------------------------------------------------------------------------
        | Percentage
        |--------------------------------------------------------------------------
        */

        if ($mode === 'percentage') {

            $percentage =
                (float) $value;


            if (
                $percentage <= 0
                ||
                $percentage > 100
            ) {

                throw new \RuntimeException(
                    'Invalid fee waiver percentage.'
                );
            }


            foreach (
                $eligible as $dueId => $item
            ) {

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


            return $allocations;
        }


        /*
        |--------------------------------------------------------------------------
        | Fixed
        |--------------------------------------------------------------------------
        */

        $remaining =
            (float) $value;


        if ($remaining <= 0) {

            throw new \RuntimeException(
                'Invalid fee waiver amount.'
            );
        }


        foreach (
            $eligible as $dueId => $item
        ) {

            if ($remaining <= 0) {
                break;
            }


            $amount = min(
                $remaining,
                $item['available']
            );


            $allocations[$dueId] =
                round(
                    $amount,
                    2
                );


            $remaining -= $amount;
        }


        return $allocations;
    }


    /*
    |--------------------------------------------------------------------------
    | CALCULATE FINE WAIVER
    |--------------------------------------------------------------------------
    */

    private function calculateFineWaiver(
        $dues,
        string $mode,
        $value
    ): array {

        $allocations = [];


        if ($mode === 'none') {
            return $allocations;
        }


        $eligible = [];


        foreach ($dues as $due) {

            $available =
                $this->ledger
                    ->remainingFine($due);


            if ($available > 0) {

                $eligible[$due->id] = [
                    'due' =>
                        $due,

                    'available' =>
                        $available,
                ];
            }
        }


        if (empty($eligible)) {

            throw new \RuntimeException(
                'No outstanding fine is available for fine waiver.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Full
        |--------------------------------------------------------------------------
        */

        if ($mode === 'full') {

            foreach (
                $eligible as $dueId => $item
            ) {

                $allocations[$dueId] =
                    $item['available'];
            }


            return $allocations;
        }


        /*
        |--------------------------------------------------------------------------
        | Percentage
        |--------------------------------------------------------------------------
        */

        if ($mode === 'percentage') {

            $percentage =
                (float) $value;


            if (
                $percentage <= 0
                ||
                $percentage > 100
            ) {

                throw new \RuntimeException(
                    'Invalid fine waiver percentage.'
                );
            }


            foreach (
                $eligible as $dueId => $item
            ) {

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


            return $allocations;
        }


        /*
        |--------------------------------------------------------------------------
        | Fixed
        |--------------------------------------------------------------------------
        */

        $remaining =
            (float) $value;


        if ($remaining <= 0) {

            throw new \RuntimeException(
                'Invalid fine waiver amount.'
            );
        }


        foreach (
            $eligible as $dueId => $item
        ) {

            if ($remaining <= 0) {
                break;
            }


            $amount = min(
                $remaining,
                $item['available']
            );


            $allocations[$dueId] =
                round(
                    $amount,
                    2
                );


            $remaining -= $amount;
        }


        return $allocations;
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDATE BENEFIT VALUES
    |--------------------------------------------------------------------------
    */

    private function validateBenefitValues(
        array $data
    ): ?string {

        /*
        |--------------------------------------------------------------------------
        | Concession
        |--------------------------------------------------------------------------
        */

        if (
            $data['concession_mode']
            !== 'none'
        ) {

            if (
                empty(
                    $data['concession_value']
                )
                ||
                (float)
                $data['concession_value']
                <= 0
            ) {

                return
                    'Concession value is required.';
            }


            if (
                $data['concession_mode']
                === 'percentage'
                &&
                (float)
                $data['concession_value']
                > 100
            ) {

                return
                    'Concession percentage cannot exceed 100%.';
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Fee waiver
        |--------------------------------------------------------------------------
        */

        if (
            in_array(
                $data['fee_waiver_mode'],
                [
                    'fixed',
                    'percentage'
                ]
            )
        ) {

            if (
                empty(
                    $data[
                        'fee_waiver_value'
                    ]
                )
                ||
                (float)
                $data[
                    'fee_waiver_value'
                ]
                <= 0
            ) {

                return
                    'Fee waiver value is required.';
            }


            if (
                $data['fee_waiver_mode']
                === 'percentage'
                &&
                (float)
                $data[
                    'fee_waiver_value'
                ]
                > 100
            ) {

                return
                    'Fee waiver percentage cannot exceed 100%.';
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Fine waiver
        |--------------------------------------------------------------------------
        */

        if (
            in_array(
                $data['fine_waiver_mode'],
                [
                    'fixed',
                    'percentage'
                ]
            )
        ) {

            if (
                empty(
                    $data[
                        'fine_waiver_value'
                    ]
                )
                ||
                (float)
                $data[
                    'fine_waiver_value'
                ]
                <= 0
            ) {

                return
                    'Fine waiver value is required.';
            }


            if (
                $data['fine_waiver_mode']
                === 'percentage'
                &&
                (float)
                $data[
                    'fine_waiver_value'
                ]
                > 100
            ) {

                return
                    'Fine waiver percentage cannot exceed 100%.';
            }
        }


        return null;
    }


    /*
    |--------------------------------------------------------------------------
    | SCHOOL SECURITY
    |--------------------------------------------------------------------------
    */

    private function authorizeSchool(
        StudentCompositeConcession $assignment
    ): void {

        if (
            (int)
            $assignment->school_id
            !==
            (int)
            Auth::user()->school_id
        ) {

            abort(403);
        }
    }
}