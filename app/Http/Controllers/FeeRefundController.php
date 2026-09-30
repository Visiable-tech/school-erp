<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\FeeChequeDdDetail;
use App\Models\FeeCollection;
use App\Models\FeeCollectionItem;
use App\Models\FeeRefund;
use App\Models\FeeRefundItem;
use App\Models\StudentFeeDue;

use App\Services\FeeLedgerService;
use App\Services\FeeRefundNumberService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FeeRefundController extends Controller
{
    public function __construct(
        protected FeeLedgerService $ledger,
        protected FeeRefundNumberService $refundNumber
    ) {
    }


    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        $academicYears = AcademicYear::where(
                'school_id',
                $schoolId
            )
            ->orderByDesc('start_date')
            ->get();


        $refunds = FeeRefund::with([
                'student',
                'academicYear',
                'collection',
                'processedBy',
            ])
            ->where(
                'school_id',
                $schoolId
            )

            ->when(
                $request->academic_year_id,
                fn ($query, $value) =>
                    $query->where(
                        'academic_year_id',
                        $value
                    )
            )

            ->when(
                $request->status,
                fn ($query, $value) =>
                    $query->where(
                        'status',
                        $value
                    )
            )

            ->when(
                $request->refund_no,
                fn ($query, $value) =>
                    $query->where(
                        'refund_no',
                        'like',
                        "%{$value}%"
                    )
            )

            ->when(
                $request->student,
                function ($query, $value) {

                    $query->whereHas(
                        'student',
                        function ($studentQuery) use ($value) {

                            $studentQuery
                                ->where(
                                    'student_name',
                                    'like',
                                    "%{$value}%"
                                )
                                ->orWhere(
                                    'admission_no',
                                    'like',
                                    "%{$value}%"
                                );
                        }
                    );
                }
            )

            ->orderByDesc('refund_date')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();


        return view(
            'admin.fees.refunds.index',
            compact(
                'academicYears',
                'refunds'
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
            'admin.fees.refunds.create',
            compact('academicYears')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | AJAX - STUDENTS
    |--------------------------------------------------------------------------
    */

    public function students(Request $request)
    {
        $validated = $request->validate([

            'academic_year_id' =>
                'required|integer',
        ]);


        $schoolId = Auth::user()->school_id;


        $rows = FeeCollection::with([
                'student',
                'enrollment.schoolClass',
                'enrollment.section',
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
                'status',
                'posted'
            )

            ->get()

            ->unique('student_enrollment_id')

            ->map(function ($collection) {

                return [

                    'student_id' =>
                        $collection->student_id,

                    'enrollment_id' =>
                        $collection->student_enrollment_id,

                    'student_name' =>
                        $collection->student->student_name
                        ??
                        $collection->student->name
                        ??
                        '-',

                    'admission_no' =>
                        $collection->student->admission_no
                        ??
                        '',

                    'class_name' =>
                        $collection
                            ->enrollment
                            ->schoolClass
                            ->name
                        ??
                        '',

                    'section_name' =>
                        $collection
                            ->enrollment
                            ->section
                            ->name
                        ??
                        '',
                ];
            })

            ->values();


        return response()->json($rows);
    }


    /*
    |--------------------------------------------------------------------------
    | AJAX - RECEIPTS
    |--------------------------------------------------------------------------
    |
    | IMPORTANT:
    |
    | Cheque/DD receipt is refundable ONLY after clearance.
    |--------------------------------------------------------------------------
    */

    public function receipts(Request $request)
    {
        $validated = $request->validate([

            'academic_year_id' =>
                'required|integer',

            'student_id' =>
                'required|integer',

            'student_enrollment_id' =>
                'required|integer',
        ]);


        $schoolId = Auth::user()->school_id;

        $output = [];


        $rows = FeeCollection::where(
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
                $validated[
                    'student_enrollment_id'
                ]
            )

            ->where(
                'status',
                'posted'
            )

            ->orderByDesc('payment_date')
            ->get();


        foreach ($rows as $collection) {

            /*
            |--------------------------------------------------------------------------
            | Cheque/DD Protection
            |--------------------------------------------------------------------------
            */

            $chequeDd = FeeChequeDdDetail::where(
                    'school_id',
                    $schoolId
                )
                ->where(
                    'fee_collection_id',
                    $collection->id
                )
                ->first();


            /*
            | If this receipt is linked with a Cheque/DD,
            | only CLEARED instruments are refundable.
            */

            if (
                $chequeDd
                &&
                $chequeDd->clearance_status
                !== 'cleared'
            ) {
                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | Already Refunded Amount
            |--------------------------------------------------------------------------
            */

            $refundedAmount = (float)
                FeeRefund::where(
                    'school_id',
                    $schoolId
                )
                ->where(
                    'fee_collection_id',
                    $collection->id
                )
                ->where(
                    'status',
                    'posted'
                )
                ->sum('total_amount');


            $availableAmount = max(
                0,
                (float) $collection->total_amount
                -
                $refundedAmount
            );


            if ($availableAmount <= 0) {
                continue;
            }


            $output[] = [

                'id' =>
                    $collection->id,

                'receipt_no' =>
                    $collection->receipt_no,

                'payment_date' =>
                    optional(
                        $collection->payment_date
                    )->format('d-m-Y'),

                'total_amount' =>
                    (float)
                    $collection->total_amount,

                'refunded_amount' =>
                    $refundedAmount,

                'available_amount' =>
                    $availableAmount,
            ];
        }


        return response()->json($output);
    }


    /*
    |--------------------------------------------------------------------------
    | AJAX - RECEIPT ITEMS
    |--------------------------------------------------------------------------
    */

    public function items(Request $request)
    {
        $validated = $request->validate([

            'fee_collection_id' =>
                'required|integer',
        ]);


        $schoolId = Auth::user()->school_id;


        $receipt = FeeCollection::where(
                'school_id',
                $schoolId
            )
            ->where(
                'id',
                $validated['fee_collection_id']
            )
            ->where(
                'status',
                'posted'
            )
            ->firstOrFail();


        /*
        |--------------------------------------------------------------------------
        | Cheque/DD Protection
        |--------------------------------------------------------------------------
        */

        $this->validateChequeDdRefund(
            $receipt,
            $schoolId
        );


        $output = [];


        $items = FeeCollectionItem::where(
                'school_id',
                $schoolId
            )
            ->where(
                'fee_collection_id',
                $receipt->id
            )
            ->orderBy('id')
            ->get();


        foreach ($items as $item) {

            /*
            |--------------------------------------------------------------------------
            | Previous Posted Refunds
            |--------------------------------------------------------------------------
            */

            $refundedAmount = (float)
                FeeRefundItem::where(
                    'fee_collection_item_id',
                    $item->id
                )

                ->whereHas(
                    'refund',
                    fn ($query) =>
                        $query->where(
                            'status',
                            'posted'
                        )
                )

                ->sum('refund_amount');


            $availableAmount = max(
                0,
                (float) $item->amount
                -
                $refundedAmount
            );


            if ($availableAmount <= 0) {
                continue;
            }


            $output[] = [

                'id' =>
                    $item->id,

                'student_fee_due_id' =>
                    $item->student_fee_due_id,

                'fee_head_name' =>
                    $item->fee_head_name,

                'installment_name' =>
                    $item->installment_name,

                'due_date' =>
                    optional(
                        $item->due_date
                    )->format('d-m-Y'),

                'paid_amount' =>
                    (float) $item->amount,

                'refunded_amount' =>
                    $refundedAmount,

                'available_amount' =>
                    $availableAmount,
            ];
        }


        return response()->json($output);
    }


    /*
    |--------------------------------------------------------------------------
    | STORE REFUND
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $validated = $request->validate([

            'academic_year_id' =>
                'required|integer',

            'student_id' =>
                'required|integer',

            'student_enrollment_id' =>
                'required|integer',

            'fee_collection_id' =>
                'required|integer',

            'refund_date' =>
                'required|date',

            'reason' =>
                'required|string|max:2000',

            'refunds' =>
                'required|array',

            'refunds.*' =>
                'nullable|numeric|min:0',
        ]);


        $schoolId = Auth::user()->school_id;


        $amounts = collect(
            $validated['refunds']
        )
            ->filter(
                fn ($amount) =>
                    (float) $amount > 0
            );


        if ($amounts->isEmpty()) {

            throw ValidationException::withMessages([

                'refunds' =>
                    'Enter at least one refund amount.',
            ]);
        }


        $refund = DB::transaction(
            function () use (
                $validated,
                $schoolId,
                $amounts
            ) {

                /*
                |--------------------------------------------------------------------------
                | Lock Receipt
                |--------------------------------------------------------------------------
                */

                $receipt = FeeCollection::where(
                        'school_id',
                        $schoolId
                    )
                    ->where(
                        'id',
                        $validated[
                            'fee_collection_id'
                        ]
                    )
                    ->lockForUpdate()
                    ->firstOrFail();


                if (
                    $receipt->status
                    !== 'posted'
                ) {

                    throw ValidationException::withMessages([

                        'fee_collection_id' =>
                            'Only posted receipts can be refunded.',
                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | Verify Student / Enrollment / Academic Year
                |--------------------------------------------------------------------------
                */

                if (
                    (int) $receipt->student_id
                    !==
                    (int) $validated['student_id']
                    ||
                    (int) $receipt->student_enrollment_id
                    !==
                    (int) $validated[
                        'student_enrollment_id'
                    ]
                    ||
                    (int) $receipt->academic_year_id
                    !==
                    (int) $validated[
                        'academic_year_id'
                    ]
                ) {

                    throw ValidationException::withMessages([

                        'fee_collection_id' =>
                            'Selected receipt does not belong to the selected student.',
                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | CHEQUE/DD REFUND PROTECTION
                |--------------------------------------------------------------------------
                |
                | Pending     -> Block
                | Deposited   -> Block
                | Bounced     -> Block
                | Cancelled   -> Block
                | Cleared     -> Allow
                |--------------------------------------------------------------------------
                */

                $this->validateChequeDdRefund(
                    $receipt,
                    $schoolId
                );


                /*
                |--------------------------------------------------------------------------
                | Validate Collection Items
                |--------------------------------------------------------------------------
                */

                $itemIds = $amounts
                    ->keys()
                    ->map(
                        fn ($id) =>
                            (int) $id
                    )
                    ->values();


                $items = FeeCollectionItem::where(
                        'school_id',
                        $schoolId
                    )
                    ->where(
                        'fee_collection_id',
                        $receipt->id
                    )
                    ->whereIn(
                        'id',
                        $itemIds
                    )
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');


                if (
                    $items->count()
                    !==
                    $itemIds->count()
                ) {

                    throw ValidationException::withMessages([

                        'refunds' =>
                            'One or more refund items are invalid.',
                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | Calculate / Validate Total
                |--------------------------------------------------------------------------
                */

                $total = 0;


                foreach (
                    $amounts as $id => $amount
                ) {

                    $item = $items->get(
                        (int) $id
                    );


                    $amount = round(
                        (float) $amount,
                        2
                    );


                    /*
                    | Previous active refunds against this item.
                    */

                    $used = (float)
                        FeeRefundItem::where(
                            'fee_collection_item_id',
                            $item->id
                        )

                        ->whereHas(
                            'refund',
                            fn ($query) =>
                                $query->where(
                                    'status',
                                    'posted'
                                )
                        )

                        ->sum(
                            'refund_amount'
                        );


                    $available = max(
                        0,
                        (float) $item->amount
                        -
                        $used
                    );


                    if (
                        $amount
                        >
                        $available
                    ) {

                        throw ValidationException::withMessages([

                            'refunds' =>
                                'Refund amount cannot exceed refundable amount.',
                        ]);
                    }


                    $total += $amount;
                }


                $total = round(
                    $total,
                    2
                );


                if ($total <= 0) {

                    throw ValidationException::withMessages([

                        'refunds' =>
                            'Refund amount must be greater than zero.',
                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | Create Refund Header
                |--------------------------------------------------------------------------
                */

                $refund = FeeRefund::create([

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
                        $validated[
                            'student_enrollment_id'
                        ],

                    'fee_collection_id' =>
                        $receipt->id,

                    'refund_no' =>
                        $this->refundNumber
                            ->generate(
                                $schoolId
                            ),

                    'refund_date' =>
                        $validated[
                            'refund_date'
                        ],

                    'total_amount' =>
                        $total,

                    'reason' =>
                        $validated[
                            'reason'
                        ],

                    'processed_by' =>
                        Auth::id(),

                    'status' =>
                        'posted',
                ]);


                /*
                |--------------------------------------------------------------------------
                | Refund Individual Items
                |--------------------------------------------------------------------------
                */

                foreach (
                    $amounts as $id => $amount
                ) {

                    $amount = round(
                        (float) $amount,
                        2
                    );


                    if ($amount <= 0) {
                        continue;
                    }


                    $item = $items->get(
                        (int) $id
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | Lock Student Fee Due
                    |--------------------------------------------------------------------------
                    */

                    $due = StudentFeeDue::where(
                            'school_id',
                            $schoolId
                        )
                        ->where(
                            'id',
                            $item
                                ->student_fee_due_id
                        )
                        ->lockForUpdate()
                        ->firstOrFail();


                    /*
                    |--------------------------------------------------------------------------
                    | Current Paid Amount Protection
                    |--------------------------------------------------------------------------
                    */

                    if (
                        $amount
                        >
                        (float) $due->paid_amount
                    ) {

                        throw ValidationException::withMessages([

                            'refunds' =>
                                'Refund exceeds current paid amount of a fee due.',
                        ]);
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Create Refund Item
                    |--------------------------------------------------------------------------
                    */

                    FeeRefundItem::create([

                        'school_id' =>
                            $schoolId,

                        'fee_refund_id' =>
                            $refund->id,

                        'fee_collection_item_id' =>
                            $item->id,

                        'student_fee_due_id' =>
                            $due->id,

                        'fee_head_name' =>
                            $item->fee_head_name,

                        'installment_name' =>
                            $item->installment_name,

                        'due_date' =>
                            $item->due_date,

                        'original_paid_amount' =>
                            $item->amount,

                        'refund_amount' =>
                            $amount,
                    ]);


                    /*
                    |--------------------------------------------------------------------------
                    | Reduce Paid Amount
                    |--------------------------------------------------------------------------
                    */

                    $due->paid_amount = max(
                        0,
                        round(
                            (float) $due->paid_amount
                            -
                            $amount,
                            2
                        )
                    );


                    $due->save();


                    /*
                    |--------------------------------------------------------------------------
                    | Recalculate Ledger
                    |--------------------------------------------------------------------------
                    */

                    $this->ledger
                        ->recalculateDue(
                            $due
                        );
                }


                return $refund;
            }
        );


        return redirect()
            ->route(
                'fee-refunds.show',
                $refund
            )
            ->with(
                'success',
                'Fee refund processed successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | SHOW
    |--------------------------------------------------------------------------
    */

    public function show(
        FeeRefund $feeRefund
    ) {
        $this->authorizeSchool(
            $feeRefund
        );


        $feeRefund->load([

            'student',

            'academicYear',

            'enrollment.schoolClass',

            'enrollment.section',

            'collection',

            'items',

            'processedBy',

            'cancelledBy',
        ]);


        return view(
            'admin.fees.refunds.show',
            compact('feeRefund')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | PRINT
    |--------------------------------------------------------------------------
    */

    public function print(
        FeeRefund $feeRefund
    ) {
        $this->authorizeSchool(
            $feeRefund
        );


        $feeRefund->load([

            'school',

            'student',

            'academicYear',

            'enrollment.schoolClass',

            'enrollment.section',

            'collection',

            'items',

            'processedBy',
        ]);


        return view(
            'admin.fees.refunds.print',
            compact('feeRefund')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | CANCEL REFUND
    |--------------------------------------------------------------------------
    */

    public function cancel(
        Request $request,
        FeeRefund $feeRefund
    ) {
        $this->authorizeSchool(
            $feeRefund
        );


        $validated = $request->validate([

            'cancellation_reason' =>
                'required|string|max:2000',
        ]);


        DB::transaction(
            function () use (
                $feeRefund,
                $validated
            ) {

                /*
                |--------------------------------------------------------------------------
                | Lock Refund
                |--------------------------------------------------------------------------
                */

                $refund = FeeRefund::where(
                        'school_id',
                        $feeRefund->school_id
                    )
                    ->where(
                        'id',
                        $feeRefund->id
                    )
                    ->lockForUpdate()
                    ->firstOrFail();


                if (
                    $refund->status
                    !== 'posted'
                ) {

                    throw ValidationException::withMessages([

                        'refund' =>
                            'This refund has already been cancelled.',
                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | Lock Original Receipt
                |--------------------------------------------------------------------------
                */

                $receipt = FeeCollection::where(
                        'school_id',
                        $refund->school_id
                    )
                    ->where(
                        'id',
                        $refund
                            ->fee_collection_id
                    )
                    ->lockForUpdate()
                    ->firstOrFail();


                if (
                    $receipt->status
                    !== 'posted'
                ) {

                    throw ValidationException::withMessages([

                        'refund' =>
                            'Refund cannot be reversed because the original receipt is not posted.',
                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | Cheque/DD Safety
                |--------------------------------------------------------------------------
                |
                | A refund should only exist against a cleared Cheque/DD.
                |
                | If an older/legacy refund somehow exists against a bounced
                | instrument, do not restore payment automatically.
                |--------------------------------------------------------------------------
                */

                $chequeDd =
                    FeeChequeDdDetail::where(
                        'school_id',
                        $refund->school_id
                    )
                    ->where(
                        'fee_collection_id',
                        $receipt->id
                    )
                    ->lockForUpdate()
                    ->first();


                if (
                    $chequeDd
                    &&
                    $chequeDd
                        ->clearance_status
                    ===
                    'bounced'
                ) {

                    throw ValidationException::withMessages([

                        'refund' =>
                            'This refund cannot be cancelled because the original Cheque/DD has already been marked as bounced.',
                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | Restore Paid Amount
                |--------------------------------------------------------------------------
                */

                $refundItems =
                    FeeRefundItem::where(
                        'fee_refund_id',
                        $refund->id
                    )
                    ->lockForUpdate()
                    ->get();


                foreach (
                    $refundItems as $item
                ) {

                    $due = StudentFeeDue::where(
                            'school_id',
                            $refund->school_id
                        )
                        ->where(
                            'id',
                            $item
                                ->student_fee_due_id
                        )
                        ->lockForUpdate()
                        ->firstOrFail();


                    $due->paid_amount =
                        round(
                            (float)
                            $due->paid_amount
                            +
                            (float)
                            $item->refund_amount,
                            2
                        );


                    $due->save();


                    $this->ledger
                        ->recalculateDue(
                            $due
                        );
                }


                /*
                |--------------------------------------------------------------------------
                | Cancel Refund
                |--------------------------------------------------------------------------
                */

                $refund->update([

                    'status' =>
                        'cancelled',

                    'cancellation_reason' =>
                        $validated[
                            'cancellation_reason'
                        ],

                    'cancelled_by' =>
                        Auth::id(),

                    'cancelled_at' =>
                        now(),
                ]);
            }
        );


        return redirect()
            ->route(
                'fee-refunds.show',
                $feeRefund
            )
            ->with(
                'success',
                'Refund cancelled and payment balances restored.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | CHEQUE/DD REFUND VALIDATION
    |--------------------------------------------------------------------------
    */

    private function validateChequeDdRefund(
        FeeCollection $receipt,
        int $schoolId
    ): void {

        $chequeDd = FeeChequeDdDetail::where(
                'school_id',
                $schoolId
            )
            ->where(
                'fee_collection_id',
                $receipt->id
            )
            ->first();


        /*
        | Normal Cash/Card/UPI/Online receipt.
        */

        if (!$chequeDd) {
            return;
        }


        switch (
            $chequeDd->clearance_status
        ) {

            case 'cleared':

                /*
                | Cleared cheque/DD can be refunded.
                */

                return;


            case 'pending':

                throw ValidationException::withMessages([

                    'fee_collection_id' =>
                        'This Cheque/DD is still pending. Refund can be processed only after clearance.',
                ]);


            case 'deposited':

                throw ValidationException::withMessages([

                    'fee_collection_id' =>
                        'This Cheque/DD has been deposited but has not cleared yet. Refund can be processed only after clearance.',
                ]);


            case 'bounced':

                throw ValidationException::withMessages([

                    'fee_collection_id' =>
                        'Refund cannot be processed because this Cheque/DD has bounced.',
                ]);


            case 'cancelled':

                throw ValidationException::withMessages([

                    'fee_collection_id' =>
                        'Refund cannot be processed because this Cheque/DD record has been cancelled.',
                ]);


            default:

                throw ValidationException::withMessages([

                    'fee_collection_id' =>
                        'This Cheque/DD is not eligible for refund.',
                ]);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | SCHOOL SECURITY
    |--------------------------------------------------------------------------
    */

    private function authorizeSchool(
        FeeRefund $refund
    ): void {

        abort_unless(

            (int) $refund->school_id
            ===
            (int) Auth::user()->school_id,

            403
        );
    }
}