<?php

namespace App\Http\Controllers;

use App\Models\BankMaster;
use App\Models\ChequeBounceReason;
use App\Models\FeeChequeDdDetail;
use App\Models\FeeCollection;
use App\Models\FeeCollectionItem;
use App\Models\PaymentMode;
use App\Models\SchoolAccount;
use App\Models\StudentFeeDue;

use App\Services\FeeLedgerService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FeeChequeDdDetailController extends Controller
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

        $details = FeeChequeDdDetail::with([
                'feeCollection.student',
                'feeCollection.academicYear',
                'bank',
                'schoolAccount',
                'bounceReason',
            ])
            ->where('school_id', $schoolId)

            ->when(
                $request->instrument_type,
                fn ($query, $value) =>
                    $query->where(
                        'instrument_type',
                        $value
                    )
            )

            ->when(
                $request->clearance_status,
                fn ($query, $value) =>
                    $query->where(
                        'clearance_status',
                        $value
                    )
            )

            ->when(
                $request->instrument_no,
                fn ($query, $value) =>
                    $query->where(
                        'instrument_no',
                        'like',
                        '%' . $value . '%'
                    )
            )

            ->when(
                $request->receipt_no,
                function ($query, $value) {
                    $query->whereHas(
                        'feeCollection',
                        fn ($q) =>
                            $q->where(
                                'receipt_no',
                                'like',
                                '%' . $value . '%'
                            )
                    );
                }
            )

            ->when(
                $request->student,
                function ($query, $value) {
                    $query->whereHas(
                        'feeCollection.student',
                        function ($q) use ($value) {
                            $q->where(
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

            ->when(
                $request->from_date,
                fn ($query, $value) =>
                    $query->whereDate(
                        'instrument_date',
                        '>=',
                        $value
                    )
            )

            ->when(
                $request->to_date,
                fn ($query, $value) =>
                    $query->whereDate(
                        'instrument_date',
                        '<=',
                        $value
                    )
            )

            ->orderByDesc('instrument_date')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();


        return view(
            'fees.cheque-dd.index',
            compact('details')
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

        $banks = BankMaster::where(
                'school_id',
                $schoolId
            )
            ->where('status', 1)
            ->orderBy('sort_order')
            ->orderBy('bank_name')
            ->get();


        $schoolAccounts = SchoolAccount::where(
                'school_id',
                $schoolId
            )
            ->where('status', 1)
            ->orderBy('sort_order')
            ->orderBy('account_name')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Find Payment Modes representing Cheque / DD
        |--------------------------------------------------------------------------
        */

        $paymentModeIds = PaymentMode::where(
                'school_id',
                $schoolId
            )
            ->where('status', 1)
            ->whereIn(
                'mode_type',
                [
                    'cheque',
                    'dd',
                ]
            )
            ->pluck('id');


        /*
        |--------------------------------------------------------------------------
        | Only posted Cheque/DD receipts without instrument detail
        |--------------------------------------------------------------------------
        */

        $receipts = FeeCollection::with([
                'student',
                'academicYear',
            ])
            ->where(
                'school_id',
                $schoolId
            )
            ->where(
                'status',
                'posted'
            )
            ->whereDoesntHave(
                'chequeDdDetail'
            )
            ->where(function ($query) use ($paymentModeIds) {

                /*
                | New payment-mode relation
                */

                if ($paymentModeIds->isNotEmpty()) {

                    $query->whereIn(
                        'payment_mode_id',
                        $paymentModeIds
                    );
                }

                /*
                | Backward compatibility with legacy column
                */

                $query->orWhereIn(
                    'payment_mode',
                    [
                        'cheque',
                        'dd',
                    ]
                );
            })
            ->orderByDesc('payment_date')
            ->orderByDesc('id')
            ->get();


        return view(
            'fees.cheque-dd.create',
            compact(
                'banks',
                'schoolAccounts',
                'receipts'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | RECEIPT DETAILS - AJAX
    |--------------------------------------------------------------------------
    */

    public function receiptDetails(
        Request $request
    ) {
        $request->validate([
            'fee_collection_id' =>
                'required|integer',
        ]);

        $schoolId = Auth::user()->school_id;


        $receipt = FeeCollection::with([
                'student',
                'academicYear',
                'paymentMode',
            ])
            ->where(
                'school_id',
                $schoolId
            )
            ->where(
                'id',
                $request->fee_collection_id
            )
            ->where(
                'status',
                'posted'
            )
            ->firstOrFail();


        /*
        |--------------------------------------------------------------------------
        | Determine Cheque / DD
        |--------------------------------------------------------------------------
        */

        $instrumentType = null;


        if (
            $receipt->paymentMode
            &&
            in_array(
                $receipt->paymentMode->mode_type,
                ['cheque', 'dd']
            )
        ) {
            $instrumentType =
                $receipt->paymentMode->mode_type;
        }
        elseif (
            in_array(
                strtolower(
                    (string)
                    $receipt->payment_mode
                ),
                ['cheque', 'dd']
            )
        ) {
            $instrumentType =
                strtolower(
                    $receipt->payment_mode
                );
        }


        return response()->json([

            'receipt_no' =>
                $receipt->receipt_no,

            'payment_date' =>
                optional(
                    $receipt->payment_date
                )->format('d-m-Y'),

            'student_name' =>
                $receipt->student->student_name
                ??
                $receipt->student->name
                ??
                '-',

            'admission_no' =>
                $receipt->student->admission_no
                ??
                '',

            'academic_year' =>
                $receipt->academicYear->name
                ??
                '',

            'amount' =>
                (float)
                $receipt->total_amount,

            'instrument_type' =>
                $instrumentType,

            /*
            | Legacy Fee Receipt fields, if already captured.
            */

            'instrument_no' =>
                $receipt->cheque_no
                ??
                '',

            'instrument_date' =>
                $receipt->cheque_date
                    ? optional(
                        $receipt->cheque_date
                    )->format('Y-m-d')
                    : null,

            'bank_name' =>
                $receipt->bank_name
                ??
                '',
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $validated = $request->validate([

            'fee_collection_id' =>
                'required|integer',

            'instrument_type' =>
                'required|in:cheque,dd',

            'instrument_no' =>
                'required|string|max:100',

            'instrument_date' =>
                'required|date',

            'bank_master_id' =>
                'nullable|integer',

            'bank_name' =>
                'nullable|string|max:150',

            'branch_name' =>
                'nullable|string|max:150',

            'school_account_id' =>
                'nullable|integer',

            'deposit_date' =>
                'nullable|date',

            'remarks' =>
                'nullable|string|max:2000',
        ]);


        $schoolId =
            Auth::user()->school_id;


        DB::transaction(
            function () use (
                $validated,
                $schoolId
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
                    $receipt->status !== 'posted'
                ) {
                    throw ValidationException::withMessages([

                        'fee_collection_id' =>
                            'Only posted receipts can have Cheque/DD details.',
                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | Prevent Duplicate
                |--------------------------------------------------------------------------
                */

                $exists =
                    FeeChequeDdDetail::where(
                        'fee_collection_id',
                        $receipt->id
                    )
                    ->exists();


                if ($exists) {

                    throw ValidationException::withMessages([

                        'fee_collection_id' =>
                            'Cheque/DD detail already exists for this receipt.',
                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | Validate Payment Mode
                |--------------------------------------------------------------------------
                */

                $validInstrumentReceipt =
                    false;


                if ($receipt->payment_mode_id) {

                    $paymentMode =
                        PaymentMode::where(
                            'school_id',
                            $schoolId
                        )
                        ->where(
                            'id',
                            $receipt->payment_mode_id
                        )
                        ->first();


                    if (
                        $paymentMode
                        &&
                        in_array(
                            $paymentMode->mode_type,
                            [
                                'cheque',
                                'dd'
                            ]
                        )
                    ) {
                        $validInstrumentReceipt =
                            true;


                        /*
                        | Prevent user changing cheque to DD
                        | or DD to cheque.
                        */

                        if (
                            $paymentMode->mode_type
                            !==
                            $validated[
                                'instrument_type'
                            ]
                        ) {

                            throw ValidationException::withMessages([

                                'instrument_type' =>
                                    'Instrument type does not match the receipt payment mode.',
                            ]);
                        }
                    }
                }


                if (
                    !$validInstrumentReceipt
                    &&
                    in_array(
                        strtolower(
                            (string)
                            $receipt->payment_mode
                        ),
                        ['cheque', 'dd']
                    )
                ) {

                    $validInstrumentReceipt =
                        true;


                    if (
                        strtolower(
                            $receipt->payment_mode
                        )
                        !==
                        $validated[
                            'instrument_type'
                        ]
                    ) {

                        throw ValidationException::withMessages([

                            'instrument_type' =>
                                'Instrument type does not match the receipt payment mode.',
                        ]);
                    }
                }


                if (
                    !$validInstrumentReceipt
                ) {

                    throw ValidationException::withMessages([

                        'fee_collection_id' =>
                            'The selected receipt is not a Cheque/DD payment.',
                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | Validate Bank
                |--------------------------------------------------------------------------
                */

                $bank = null;


                if (
                    !empty(
                        $validated[
                            'bank_master_id'
                        ]
                    )
                ) {

                    $bank =
                        BankMaster::where(
                            'school_id',
                            $schoolId
                        )
                        ->where(
                            'id',
                            $validated[
                                'bank_master_id'
                            ]
                        )
                        ->where(
                            'status',
                            1
                        )
                        ->firstOrFail();
                }


                /*
                |--------------------------------------------------------------------------
                | Validate School Account
                |--------------------------------------------------------------------------
                */

                if (
                    !empty(
                        $validated[
                            'school_account_id'
                        ]
                    )
                ) {

                    SchoolAccount::where(
                        'school_id',
                        $schoolId
                    )
                    ->where(
                        'id',
                        $validated[
                            'school_account_id'
                        ]
                    )
                    ->where(
                        'status',
                        1
                    )
                    ->firstOrFail();
                }


                /*
                |--------------------------------------------------------------------------
                | Initial Status
                |--------------------------------------------------------------------------
                */

                $status =
                    !empty(
                        $validated[
                            'deposit_date'
                        ]
                    )
                    ?
                    'deposited'
                    :
                    'pending';


                FeeChequeDdDetail::create([

                    'school_id' =>
                        $schoolId,

                    'fee_collection_id' =>
                        $receipt->id,

                    'instrument_type' =>
                        $validated[
                            'instrument_type'
                        ],

                    'instrument_no' =>
                        $validated[
                            'instrument_no'
                        ],

                    'instrument_date' =>
                        $validated[
                            'instrument_date'
                        ],

                    'bank_master_id' =>
                        $bank?->id,

                    'bank_name' =>
                        $bank?->bank_name
                        ??
                        $validated[
                            'bank_name'
                        ]
                        ??
                        null,

                    'branch_name' =>
                        $validated[
                            'branch_name'
                        ]
                        ??
                        $bank?->branch_name
                        ??
                        null,

                    /*
                    | Amount must always come from receipt.
                    */

                    'amount' =>
                        $receipt->total_amount,

                    'school_account_id' =>
                        $validated[
                            'school_account_id'
                        ]
                        ??
                        null,

                    'deposit_date' =>
                        $validated[
                            'deposit_date'
                        ]
                        ??
                        null,

                    'clearance_status' =>
                        $status,

                    'remarks' =>
                        $validated[
                            'remarks'
                        ]
                        ??
                        null,

                    'created_by' =>
                        Auth::id(),

                    'updated_by' =>
                        Auth::id(),
                ]);
            }
        );


        return redirect()
            ->route(
                'cheque-dd-details.index'
            )
            ->with(
                'success',
                'Cheque/DD detail created successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | SHOW
    |--------------------------------------------------------------------------
    */

    public function show(
        FeeChequeDdDetail $chequeDdDetail
    ) {
        $this->authorizeSchool(
            $chequeDdDetail
        );


        $chequeDdDetail->load([

            'feeCollection.student',

            'feeCollection.academicYear',

            'feeCollection.items',

            'bank',

            'schoolAccount',

            'bounceReason',

            'createdBy',

            'updatedBy',
        ]);


        return view(
            'fees.cheque-dd.show',
            compact(
                'chequeDdDetail'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */

    public function edit(
        FeeChequeDdDetail $chequeDdDetail
    ) {
        $this->authorizeSchool(
            $chequeDdDetail
        );


        /*
        | Cleared/bounced/cancelled records are historical.
        */

        if (
            in_array(
                $chequeDdDetail
                    ->clearance_status,
                [
                    'cleared',
                    'bounced',
                    'cancelled'
                ]
            )
        ) {

            return redirect()
                ->route(
                    'cheque-dd-details.show',
                    $chequeDdDetail
                )
                ->with(
                    'error',
                    'Completed Cheque/DD records cannot be edited.'
                );
        }


        $schoolId =
            Auth::user()->school_id;


        $banks =
            BankMaster::where(
                'school_id',
                $schoolId
            )
            ->where('status', 1)
            ->orderBy('sort_order')
            ->orderBy('bank_name')
            ->get();


        $schoolAccounts =
            SchoolAccount::where(
                'school_id',
                $schoolId
            )
            ->where('status', 1)
            ->orderBy('sort_order')
            ->orderBy('account_name')
            ->get();


        $chequeDdDetail->load([
            'feeCollection.student',
            'feeCollection.academicYear',
        ]);


        return view(
            'fees.cheque-dd.edit',
            compact(
                'chequeDdDetail',
                'banks',
                'schoolAccounts'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        FeeChequeDdDetail $chequeDdDetail
    ) {
        $this->authorizeSchool(
            $chequeDdDetail
        );


        if (
            in_array(
                $chequeDdDetail
                    ->clearance_status,
                [
                    'cleared',
                    'bounced',
                    'cancelled'
                ]
            )
        ) {

            return back()->with(
                'error',
                'Completed Cheque/DD records cannot be edited.'
            );
        }


        $validated =
            $request->validate([

                'instrument_no' =>
                    'required|string|max:100',

                'instrument_date' =>
                    'required|date',

                'bank_master_id' =>
                    'nullable|integer',

                'bank_name' =>
                    'nullable|string|max:150',

                'branch_name' =>
                    'nullable|string|max:150',

                'school_account_id' =>
                    'nullable|integer',

                'deposit_date' =>
                    'nullable|date',

                'remarks' =>
                    'nullable|string|max:2000',
            ]);


        $schoolId =
            Auth::user()->school_id;


        $bank = null;


        if (
            !empty(
                $validated[
                    'bank_master_id'
                ]
            )
        ) {

            $bank =
                BankMaster::where(
                    'school_id',
                    $schoolId
                )
                ->where(
                    'id',
                    $validated[
                        'bank_master_id'
                    ]
                )
                ->where('status', 1)
                ->firstOrFail();
        }


        if (
            !empty(
                $validated[
                    'school_account_id'
                ]
            )
        ) {

            SchoolAccount::where(
                'school_id',
                $schoolId
            )
            ->where(
                'id',
                $validated[
                    'school_account_id'
                ]
            )
            ->where('status', 1)
            ->firstOrFail();
        }


        /*
        |--------------------------------------------------------------------------
        | If deposit date entered while pending, make deposited
        |--------------------------------------------------------------------------
        */

        $status =
            $chequeDdDetail
                ->clearance_status;


        if (
            $status === 'pending'
            &&
            !empty(
                $validated[
                    'deposit_date'
                ]
            )
        ) {
            $status = 'deposited';
        }


        $chequeDdDetail->update([

            'instrument_no' =>
                $validated[
                    'instrument_no'
                ],

            'instrument_date' =>
                $validated[
                    'instrument_date'
                ],

            'bank_master_id' =>
                $bank?->id,

            'bank_name' =>
                $bank?->bank_name
                ??
                $validated[
                    'bank_name'
                ]
                ??
                null,

            'branch_name' =>
                $validated[
                    'branch_name'
                ]
                ??
                $bank?->branch_name
                ??
                null,

            'school_account_id' =>
                $validated[
                    'school_account_id'
                ]
                ??
                null,

            'deposit_date' =>
                $validated[
                    'deposit_date'
                ]
                ??
                null,

            'clearance_status' =>
                $status,

            'remarks' =>
                $validated[
                    'remarks'
                ]
                ??
                null,

            'updated_by' =>
                Auth::id(),
        ]);


        return redirect()
            ->route(
                'cheque-dd-details.show',
                $chequeDdDetail
            )
            ->with(
                'success',
                'Cheque/DD detail updated successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | MARK DEPOSITED
    |--------------------------------------------------------------------------
    */

    public function deposit(
        Request $request,
        FeeChequeDdDetail $chequeDdDetail
    ) {
        $this->authorizeSchool(
            $chequeDdDetail
        );


        $validated =
            $request->validate([

                'deposit_date' =>
                    'required|date',

                'school_account_id' =>
                    'required|integer',
            ]);


        $schoolId =
            Auth::user()->school_id;


        if (
            $chequeDdDetail
                ->clearance_status
            !==
            'pending'
        ) {

            return back()->with(
                'error',
                'Only pending instruments can be deposited.'
            );
        }


        $account =
            SchoolAccount::where(
                'school_id',
                $schoolId
            )
            ->where(
                'id',
                $validated[
                    'school_account_id'
                ]
            )
            ->where('status', 1)
            ->firstOrFail();


        $chequeDdDetail->update([

            'school_account_id' =>
                $account->id,

            'deposit_date' =>
                $validated[
                    'deposit_date'
                ],

            'clearance_status' =>
                'deposited',

            'updated_by' =>
                Auth::id(),
        ]);


        return back()->with(
            'success',
            'Cheque/DD marked as deposited.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | MARK CLEARED
    |--------------------------------------------------------------------------
    */

    public function clear(
        Request $request,
        FeeChequeDdDetail $chequeDdDetail
    ) {
        $this->authorizeSchool(
            $chequeDdDetail
        );


        $validated =
            $request->validate([

                'clearance_date' =>
                    'required|date',
            ]);


        if (
            $chequeDdDetail
                ->clearance_status
            !==
            'deposited'
        ) {

            return back()->with(
                'error',
                'Only deposited instruments can be cleared.'
            );
        }


        $chequeDdDetail->update([

            'clearance_status' =>
                'cleared',

            'clearance_date' =>
                $validated[
                    'clearance_date'
                ],

            'updated_by' =>
                Auth::id(),
        ]);


        return back()->with(
            'success',
            'Cheque/DD cleared successfully.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | MARK BOUNCED
    |--------------------------------------------------------------------------
    |
    | IMPORTANT:
    |
    | Fee Receipt already increased paid_amount.
    |
    | Therefore when Cheque/DD bounces:
    |
    | paid_amount -= receipt item amount
    |
    | and FeeLedgerService recalculates balance.
    |--------------------------------------------------------------------------
    */

    public function bounce(
        Request $request,
        FeeChequeDdDetail $chequeDdDetail
    ) {
        $this->authorizeSchool($chequeDdDetail);

        $validated = $request->validate([
            'bounce_date' => 'required|date',
            'cheque_bounce_reason_id' => 'required|integer',
            'bounce_remarks' => 'nullable|string|max:2000',
        ]);

        $schoolId = Auth::user()->school_id;

        DB::transaction(function () use (
            $validated,
            $schoolId,
            $chequeDdDetail
        ) {

            /*
            |--------------------------------------------------------------------------
            | Lock Cheque/DD
            |--------------------------------------------------------------------------
            */

            $instrument = FeeChequeDdDetail::where(
                    'school_id',
                    $schoolId
                )
                ->where('id', $chequeDdDetail->id)
                ->lockForUpdate()
                ->firstOrFail();


            /*
            |--------------------------------------------------------------------------
            | Prevent double bounce/reversal
            |--------------------------------------------------------------------------
            */

            if ($instrument->payment_reversed_at) {

                throw ValidationException::withMessages([
                    'bounce' =>
                        'Payment for this Cheque/DD has already been reversed.'
                ]);
            }


            if (!in_array(
                $instrument->clearance_status,
                ['pending', 'deposited']
            )) {

                throw ValidationException::withMessages([
                    'bounce' =>
                        'Only pending or deposited instruments can be marked as bounced.'
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | Bounce Reason
            |--------------------------------------------------------------------------
            */

            $bounceReason = ChequeBounceReason::where(
                    'school_id',
                    $schoolId
                )
                ->where(
                    'id',
                    $validated['cheque_bounce_reason_id']
                )
                ->where('status', 1)
                ->firstOrFail();


            /*
            |--------------------------------------------------------------------------
            | Receipt
            |--------------------------------------------------------------------------
            */

            $receipt = FeeCollection::where(
                    'school_id',
                    $schoolId
                )
                ->where(
                    'id',
                    $instrument->fee_collection_id
                )
                ->lockForUpdate()
                ->firstOrFail();


            if ($receipt->status !== 'posted') {

                throw ValidationException::withMessages([
                    'bounce' =>
                        'The original fee receipt is no longer posted.'
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | IMPORTANT:
            | Do not bounce a receipt which already has an active refund.
            |--------------------------------------------------------------------------
            |
            | We intentionally block this rather than trying to guess which portion
            | should be reversed.
            |
            */

            $hasRefund = DB::table('fee_refunds')
                ->where(
                    'fee_collection_id',
                    $receipt->id
                )
                ->where(function ($query) {

                    $query->whereNull('status')
                        ->orWhere(
                            'status',
                            '!=',
                            'cancelled'
                        );
                })
                ->exists();


            if ($hasRefund) {

                throw ValidationException::withMessages([
                    'bounce' =>
                        'This receipt already has a refund. Cancel/reconcile the refund before marking the Cheque/DD as bounced.'
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | Collection Items
            |--------------------------------------------------------------------------
            */

            $items = FeeCollectionItem::where(
                    'fee_collection_id',
                    $receipt->id
                )
                ->lockForUpdate()
                ->get();


            if ($items->isEmpty()) {

                throw ValidationException::withMessages([
                    'bounce' =>
                        'No fee collection items were found for this receipt.'
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | Validate all dues BEFORE changing anything
            |--------------------------------------------------------------------------
            */

            $dueRows = [];


            foreach ($items as $item) {

                $due = StudentFeeDue::where(
                        'school_id',
                        $schoolId
                    )
                    ->where(
                        'id',
                        $item->student_fee_due_id
                    )
                    ->lockForUpdate()
                    ->firstOrFail();


                $itemAmount =
                    round((float) $item->amount, 2);

                $paidAmount =
                    round((float) $due->paid_amount, 2);


                if ($itemAmount > $paidAmount) {

                    throw ValidationException::withMessages([
                        'bounce' =>
                            'The payment cannot be reversed because the current paid amount for one or more fee dues is lower than the receipt allocation.'
                    ]);
                }


                $dueRows[] = [
                    'due' => $due,
                    'amount' => $itemAmount,
                ];
            }


            /*
            |--------------------------------------------------------------------------
            | Reverse receipt allocation
            |--------------------------------------------------------------------------
            */

            foreach ($dueRows as $row) {

                /** @var StudentFeeDue $due */
                $due = $row['due'];

                $amount = $row['amount'];


                $due->paid_amount = max(
                    0,
                    round(
                        (float) $due->paid_amount - $amount,
                        2
                    )
                );

                $due->save();


                $this->ledger->recalculateDue($due);
            }


            /*
            |--------------------------------------------------------------------------
            | Snapshot Bounce Charge
            |--------------------------------------------------------------------------
            |
            | We record the configured charge here.
            |
            | We DO NOT add it to an arbitrary fee component because one receipt can
            | contain several fee dues.
            |
            | Later it can be posted through a dedicated Misc Component / Bounce
            | Charge mapping.
            |--------------------------------------------------------------------------
            */

            $bounceCharge = 0;

            if ($bounceReason->apply_charge) {

                $bounceCharge =
                    round(
                        (float) $bounceReason->bounce_charge,
                        2
                    );
            }


            /*
            |--------------------------------------------------------------------------
            | Final Bounce Update
            |--------------------------------------------------------------------------
            */

            $instrument->update([

                'clearance_status' =>
                    'bounced',

                'bounce_date' =>
                    $validated['bounce_date'],

                'cheque_bounce_reason_id' =>
                    $bounceReason->id,

                'bounce_remarks' =>
                    $validated['bounce_remarks']
                    ?? null,

                'payment_reversed_at' =>
                    now(),

                'payment_reversed_by' =>
                    Auth::id(),

                'bounce_charge_amount' =>
                    $bounceCharge,

                /*
                | FALSE means charge is recorded but has not yet been posted
                | into the student's fee ledger.
                */

                'bounce_charge_applied' =>
                    false,

                'updated_by' =>
                    Auth::id(),
            ]);
        });


        return redirect()
            ->route(
                'cheque-dd-details.show',
                $chequeDdDetail
            )
            ->with(
                'success',
                'Cheque/DD marked as bounced and the original fee payment was reversed successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | CANCEL INSTRUMENT DETAIL
    |--------------------------------------------------------------------------
    |
    | This does NOT cancel the fee receipt.
    |
    | It only cancels the instrument tracking record.
    |--------------------------------------------------------------------------
    */

    public function cancel(
        Request $request,
        FeeChequeDdDetail $chequeDdDetail
    ) {
        $this->authorizeSchool(
            $chequeDdDetail
        );


        if (
            in_array(
                $chequeDdDetail
                    ->clearance_status,
                [
                    'cleared',
                    'bounced',
                    'cancelled'
                ]
            )
        ) {

            return back()->with(
                'error',
                'This Cheque/DD record cannot be cancelled.'
            );
        }


        $request->validate([

            'remarks' =>
                'required|string|max:2000',
        ]);


        $chequeDdDetail->update([

            'clearance_status' =>
                'cancelled',

            'remarks' =>
                trim(
                    ($chequeDdDetail->remarks ?? '')
                    .
                    "\nCancellation: "
                    .
                    $request->remarks
                ),

            'updated_by' =>
                Auth::id(),
        ]);


        return back()->with(
            'success',
            'Cheque/DD tracking record cancelled.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | SCHOOL SECURITY
    |--------------------------------------------------------------------------
    */

    private function authorizeSchool(
        FeeChequeDdDetail $detail
    ): void {

        abort_unless(

            (int)
            $detail->school_id
            ===
            (int)
            Auth::user()->school_id,

            403
        );
    }
}