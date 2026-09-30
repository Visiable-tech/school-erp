<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\FeeCollection;
use App\Models\FeeCollectionItem;
use App\Models\PaymentMode;
use App\Models\SchoolAccount;
use App\Models\StudentEnrollment;
use App\Models\StudentFeeDue;
use App\Services\FeeLedgerService;
use App\Services\FeeReceiptNumberService;
use App\Models\FeeChequeDdDetail;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FeeReceiptController extends Controller
{
    protected FeeLedgerService $ledger;
    protected FeeReceiptNumberService $receiptNumber;


    public function __construct(
        FeeLedgerService $ledger,
        FeeReceiptNumberService $receiptNumber
    ) {
        $this->ledger = $ledger;
        $this->receiptNumber = $receiptNumber;
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


        $receipts = FeeCollection::with([
                'student',
                'academicYear',
                'paymentMode',
                'collector',
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
                $request->receipt_no,
                fn ($query, $value) =>
                    $query->where(
                        'receipt_no',
                        'like',
                        '%' . $value . '%'
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
                                    '%' . $value . '%'
                                )
                                ->orWhere(
                                    'admission_no',
                                    'like',
                                    '%' . $value . '%'
                                );
                        }
                    );
                }
            )

            ->orderByDesc('payment_date')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();


        return view(
            'fees.receipts.index',
            compact(
                'academicYears',
                'receipts'
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


        $paymentModes = PaymentMode::where(
                'school_id',
                $schoolId
            )
            ->where('status', 1)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();


        $schoolAccounts = SchoolAccount::where(
                'school_id',
                $schoolId
            )
            ->where('status', 1)
            ->orderByDesc('is_default')
            ->orderBy('sort_order')
            ->get();


        return view(
            'fees.receipts.create',
            compact(
                'academicYears',
                'paymentModes',
                'schoolAccounts'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | AJAX STUDENTS
    |--------------------------------------------------------------------------
    */

    public function students(Request $request)
    {
        $request->validate([
            'academic_year_id' =>
                'required|integer',
        ]);


        $schoolId = Auth::user()->school_id;


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
                $request->academic_year_id
            )
            ->where('status', 1)
            ->whereHas(
                'student',
                fn ($query) =>
                    $query->where('status', 1)
            )
            ->orderBy('student_id')
            ->get();


        return response()->json(
            $enrollments->map(
                function ($enrollment) {

                    return [

                        'student_id' =>
                            $enrollment->student_id,

                        'enrollment_id' =>
                            $enrollment->id,

                        'student_name' =>
                            $enrollment->student->student_name
                            ??
                            $enrollment->student->name
                            ??
                            '-',

                        'admission_no' =>
                            $enrollment->student->admission_no
                            ?? '',

                        'class_name' =>
                            $enrollment->schoolClass->name
                            ?? '',

                        'section_name' =>
                            $enrollment->section->name
                            ?? '',
                    ];
                }
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | AJAX DUES
    |--------------------------------------------------------------------------
    */

    public function dues(Request $request)
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
                $validated['student_enrollment_id']
            )
            ->where('status', 1)
            ->where(
                'balance_amount',
                '>',
                0
            )
            ->orderBy('due_date')
            ->orderBy('id')
            ->get();


        return response()->json(
            $dues->map(
                function ($due) {

                    return [

                        'id' =>
                            $due->id,

                        'fee_head' =>
                            $due->fee_head_name
                            ??
                            $due->feeHead?->name
                            ??
                            '-',

                        'installment' =>
                            $due->installment_name
                            ??
                            $due->feeInstallment?->installment_name
                            ??
                            '-',

                        'due_date' =>
                            optional(
                                $due->due_date
                            )->format('d-m-Y'),

                        'payable_amount' =>
                            (float) $due->payable_amount,

                        'paid_amount' =>
                            (float) $due->paid_amount,

                        'balance_amount' =>
                            (float) $due->balance_amount,
                    ];
                }
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | STORE RECEIPT
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

            'payment_date' =>
                'required|date',

            'payment_mode_id' =>
                'required|integer',

            'school_account_id' =>
                'nullable|integer',

            'transaction_no' =>
                'nullable|string|max:100',

            'bank_name' =>
                'nullable|string|max:150',

            'cheque_no' =>
                'nullable|string|max:100',

            'cheque_date' =>
                'nullable|date',

            'remarks' =>
                'nullable|string|max:2000',

            'payments' =>
                'required|array',

            'payments.*' =>
                'nullable|numeric|min:0',
        ]);


        $schoolId = Auth::user()->school_id;


        $paymentMode = PaymentMode::where(
                'school_id',
                $schoolId
            )
            ->where(
                'id',
                $validated['payment_mode_id']
            )
            ->where('status', 1)
            ->firstOrFail();


        $payments = collect(
            $validated['payments']
        )
        ->filter(
            fn ($amount) =>
                (float) $amount > 0
        );


        if ($payments->isEmpty()) {

            throw ValidationException::withMessages([
                'payments' =>
                    'Enter payment amount for at least one fee due.',
            ]);
        }


        $receipt = DB::transaction(
            function () use (
                $validated,
                $schoolId,
                $paymentMode,
                $payments
            ) {

                /*
                |--------------------------------------------------------------------------
                | Validate Enrollment
                |--------------------------------------------------------------------------
                */

                $enrollment = StudentEnrollment::where(
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
                        'id',
                        $validated['student_enrollment_id']
                    )
                    ->firstOrFail();


                /*
                |--------------------------------------------------------------------------
                | Lock Dues
                |--------------------------------------------------------------------------
                */

                $dueIds = $payments
                    ->keys()
                    ->map(
                        fn ($id) => (int) $id
                    )
                    ->values();


                $dues = StudentFeeDue::where(
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
                        $validated['student_enrollment_id']
                    )
                    ->whereIn(
                        'id',
                        $dueIds
                    )
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');


                if (
                    $dues->count()
                    !==
                    $dueIds->count()
                ) {

                    throw ValidationException::withMessages([
                        'payments' =>
                            'One or more selected fee dues are invalid.',
                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | Calculate Total
                |--------------------------------------------------------------------------
                */

                $total = 0;


                foreach ($payments as $dueId => $amount) {

                    $amount =
                        round(
                            (float) $amount,
                            2
                        );


                    $due =
                        $dues->get(
                            (int) $dueId
                        );


                    if ($amount <= 0) {
                        continue;
                    }


                    if (
                        $amount
                        >
                        (float) $due->balance_amount
                    ) {

                        throw ValidationException::withMessages([
                            'payments' =>
                                'Payment cannot exceed outstanding balance.',
                        ]);
                    }


                    $total += $amount;
                }


                $total =
                    round(
                        $total,
                        2
                    );


                /*
                |--------------------------------------------------------------------------
                | Generate Receipt Number
                |--------------------------------------------------------------------------
                */

                $receiptNo =
                    $this->receiptNumber
                        ->generate(
                            $schoolId,
                            $validated['academic_year_id']
                        );


                /*
                |--------------------------------------------------------------------------
                | Create Receipt
                |--------------------------------------------------------------------------
                */

                $receipt =
                    FeeCollection::create([

                        'school_id' =>
                            $schoolId,

                        'student_id' =>
                            $validated['student_id'],

                        'student_enrollment_id' =>
                            $validated['student_enrollment_id'],

                        'academic_year_id' =>
                            $validated['academic_year_id'],

                        'receipt_no' =>
                            $receiptNo,

                        'payment_date' =>
                            $validated['payment_date'],

                        'total_amount' =>
                            $total,

                        // Keep legacy snapshot
                        'payment_mode' =>
                            $paymentMode->mode_type
                            ??
                            $paymentMode->name,

                        'payment_mode_id' =>
                            $paymentMode->id,

                        'school_account_id' =>
                            $validated['school_account_id']
                            ?? null,

                        'transaction_no' =>
                            $validated['transaction_no']
                            ?? null,

                        'bank_name' =>
                            $validated['bank_name']
                            ?? null,

                        'cheque_no' =>
                            $validated['cheque_no']
                            ?? null,

                        'cheque_date' =>
                            $validated['cheque_date']
                            ?? null,

                        'remarks' =>
                            $validated['remarks']
                            ?? null,

                        'collected_by' =>
                            Auth::id(),

                        'status' =>
                            'posted',
                    ]);


                /*
                |--------------------------------------------------------------------------
                | Allocate Payments
                |--------------------------------------------------------------------------
                */

                foreach ($payments as $dueId => $amount) {

                    $amount =
                        round(
                            (float) $amount,
                            2
                        );


                    if ($amount <= 0) {
                        continue;
                    }


                    $due =
                        $dues->get(
                            (int) $dueId
                        );


                    FeeCollectionItem::create([

                        'school_id' =>
                            $schoolId,

                        'fee_collection_id' =>
                            $receipt->id,

                        'student_fee_due_id' =>
                            $due->id,

                        'amount' =>
                            $amount,

                        'fee_head_name' =>
                            $due->fee_head_name
                            ??
                            $due->feeHead?->name
                            ??
                            '-',

                        'installment_name' =>
                            $due->installment_name
                            ??
                            $due->feeInstallment?->installment_name
                            ??
                            '-',

                        'due_date' =>
                            $due->due_date,

                        'due_amount' =>
                            $due->payable_amount,
                    ]);


                    $due->paid_amount =
                        round(
                            (float) $due->paid_amount
                            +
                            $amount,
                            2
                        );


                    $due->save();


                    /*
                    |--------------------------------------------------------------------------
                    | Central Ledger Calculation
                    |--------------------------------------------------------------------------
                    */

                    $this->ledger
                        ->recalculateDue(
                            $due
                        );
                }


                return $receipt;
            }
        );


        return redirect()
            ->route(
                'fee-receipts.show',
                $receipt
            )
            ->with(
                'success',
                'Fee receipt generated successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | SHOW
    |--------------------------------------------------------------------------
    */

    public function show(
        FeeCollection $feeReceipt
    ) {
        $this->authorizeSchool(
            $feeReceipt
        );


        $feeReceipt->load([
            'student',
            'enrollment.schoolClass',
            'enrollment.section',
            'academicYear',
            'paymentMode',
            'schoolAccount',
            'items.due',
            'collector',
            'cancelledBy',
        ]);


        return view(
            'fees.receipts.show',
            compact('feeReceipt')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | PRINT
    |--------------------------------------------------------------------------
    */

    public function print(
        FeeCollection $feeReceipt
    ) {
        $this->authorizeSchool(
            $feeReceipt
        );


        $feeReceipt->load([
            'student',
            'enrollment.schoolClass',
            'enrollment.section',
            'academicYear',
            'paymentMode',
            'schoolAccount',
            'items',
            'collector',
            'school',
        ]);


        return view(
            'fees.receipts.print',
            compact('feeReceipt')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | CANCEL / REVERSE RECEIPT
    |--------------------------------------------------------------------------
    */

    public function cancel(
        Request $request,
        FeeCollection $feeReceipt
    ) {
        $this->authorizeSchool($feeReceipt);

        $validated = $request->validate([
            'cancellation_reason' => 'required|string|max:2000',
        ]);

        DB::transaction(function () use ($feeReceipt, $validated) {

            $receipt = FeeCollection::where('school_id', $feeReceipt->school_id)
                ->where('id', $feeReceipt->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($receipt->status !== 'posted') {
                throw ValidationException::withMessages([
                    'receipt' => 'This receipt has already been cancelled.',
                ]);
            }

            // Never cancel a receipt while an active refund exists.
            $hasActiveRefund = DB::table('fee_refunds')
                ->where('school_id', $receipt->school_id)
                ->where('fee_collection_id', $receipt->id)
                ->where('status', 'posted')
                ->exists();

            if ($hasActiveRefund) {
                throw ValidationException::withMessages([
                    'receipt' => 'This receipt has an active refund. Cancel the refund first before cancelling the receipt.',
                ]);
            }

            // Lock linked Cheque/DD record, if any.
            $chequeDd = FeeChequeDdDetail::where('school_id', $receipt->school_id)
                ->where('fee_collection_id', $receipt->id)
                ->lockForUpdate()
                ->first();

            if ($chequeDd) {

                // Bounce() already reversed the payment, so never reverse it again.
                if (
                    $chequeDd->clearance_status === 'bounced'
                    || $chequeDd->payment_reversed_at
                ) {
                    throw ValidationException::withMessages([
                        'receipt' => 'This Cheque/DD payment has already been reversed after bounce. The receipt cannot be cancelled again.',
                    ]);
                }

                // Cleared instruments should be corrected through Refund.
                if ($chequeDd->clearance_status === 'cleared') {
                    throw ValidationException::withMessages([
                        'receipt' => 'A cleared Cheque/DD receipt cannot be cancelled. Please use Fee Refund instead.',
                    ]);
                }

                if ($chequeDd->clearance_status === 'cancelled') {
                    throw ValidationException::withMessages([
                        'receipt' => 'The Cheque/DD tracking record is already cancelled. Please reconcile the instrument before cancelling this receipt.',
                    ]);
                }
            }

            $items = FeeCollectionItem::where('school_id', $receipt->school_id)
                ->where('fee_collection_id', $receipt->id)
                ->lockForUpdate()
                ->get();

            if ($items->isEmpty()) {
                throw ValidationException::withMessages([
                    'receipt' => 'No fee collection items were found for this receipt.',
                ]);
            }

            // Validate all rows first so the transaction fails before any partial reversal.
            $dueRows = [];

            foreach ($items as $item) {

                $due = StudentFeeDue::where('school_id', $receipt->school_id)
                    ->where('id', $item->student_fee_due_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $itemAmount = round((float) $item->amount, 2);
                $paidAmount = round((float) $due->paid_amount, 2);

                if ($itemAmount > $paidAmount) {
                    throw ValidationException::withMessages([
                        'receipt' => 'This receipt cannot be cancelled because one or more fee dues no longer contain the full receipt allocation.',
                    ]);
                }

                $dueRows[] = [
                    'due' => $due,
                    'amount' => $itemAmount,
                ];
            }

            // Reverse the receipt allocation exactly once.
            foreach ($dueRows as $row) {

                /** @var StudentFeeDue $due */
                $due = $row['due'];

                $due->paid_amount = max(
                    0,
                    round((float) $due->paid_amount - $row['amount'], 2)
                );

                $due->save();
                $this->ledger->recalculateDue($due);
            }

            // A pending/deposited Cheque/DD closes together with its receipt.
            if (
                $chequeDd
                && in_array(
                    $chequeDd->clearance_status,
                    ['pending', 'deposited'],
                    true
                )
            ) {
                $chequeDd->update([
                    'clearance_status' => 'cancelled',
                    'remarks' => trim(
                        ($chequeDd->remarks ?? '')
                        . "\nReceipt cancellation: "
                        . $validated['cancellation_reason']
                    ),
                    'updated_by' => Auth::id(),
                ]);
            }

            $receipt->update([
                'status' => 'cancelled',
                'cancellation_reason' => $validated['cancellation_reason'],
                'cancelled_by' => Auth::id(),
                'cancelled_at' => now(),
            ]);
        });

        return redirect()
            ->route('fee-receipts.show', $feeReceipt)
            ->with(
                'success',
                'Receipt cancelled and fee balances restored successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | School Security
    |--------------------------------------------------------------------------
    */

    private function authorizeSchool(
        FeeCollection $receipt
    ): void {

        abort_unless(
            (int) $receipt->school_id
            ===
            (int) Auth::user()->school_id,
            403
        );
    }
}