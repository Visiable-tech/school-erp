<?php

namespace App\Http\Controllers;

use App\Models\PaymentMode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PaymentModeController extends Controller
{
    public function index(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        $query = PaymentMode::where(
            'school_id',
            $schoolId
        );

        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where(
                    'name',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'code',
                    'like',
                    "%{$search}%"
                );
            });
        }

        if ($request->filled('mode_type')) {
            $query->where(
                'mode_type',
                $request->mode_type
            );
        }

        if (
            $request->filled('status') &&
            in_array(
                $request->status,
                ['0', '1'],
                true
            )
        ) {
            $query->where(
                'status',
                $request->status
            );
        }

        $paymentModes = $query
            ->orderByDesc('is_default')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view(
            'payment-modes.index',
            compact('paymentModes')
        );
    }


    public function create()
    {
        return view('payment-modes.create');
    }


    public function store(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        $validated = $this->validateData(
            $request,
            $schoolId
        );

        DB::transaction(function () use (
            $request,
            $validated,
            $schoolId
        ) {

            if ($request->boolean('is_default')) {

                PaymentMode::where(
                    'school_id',
                    $schoolId
                )->update([
                    'is_default' => false
                ]);
            }

            PaymentMode::create([
                'school_id' => $schoolId,

                'name' => trim(
                    $validated['name']
                ),

                'code' => $this->formatCode(
                    $validated['code'] ?? null
                ),

                'mode_type' =>
                    $validated['mode_type'],

                'requires_reference' =>
                    $request->boolean(
                        'requires_reference'
                    ),

                'requires_bank' =>
                    $request->boolean(
                        'requires_bank'
                    ),

                'requires_instrument_date' =>
                    $request->boolean(
                        'requires_instrument_date'
                    ),

                'is_online' =>
                    $request->boolean(
                        'is_online'
                    ),

                'is_default' =>
                    $request->boolean(
                        'is_default'
                    ),

                'description' =>
                    $validated['description']
                    ?? null,

                'sort_order' =>
                    $validated['sort_order']
                    ?? 0,

                'status' =>
                    $request->boolean('status'),
            ]);
        });

        return redirect()
            ->route('payment-modes.index')
            ->with(
                'success',
                'Payment Mode created successfully.'
            );
    }


    public function edit(
        PaymentMode $paymentMode
    ) {
        $this->authorizeSchool(
            $paymentMode
        );

        return view(
            'payment-modes.edit',
            compact('paymentMode')
        );
    }


    public function update(
        Request $request,
        PaymentMode $paymentMode
    ) {
        $this->authorizeSchool(
            $paymentMode
        );

        $schoolId = Auth::user()->school_id;

        $validated = $this->validateData(
            $request,
            $schoolId,
            $paymentMode->id
        );

        DB::transaction(function () use (
            $request,
            $validated,
            $schoolId,
            $paymentMode
        ) {

            if ($request->boolean('is_default')) {

                PaymentMode::where(
                    'school_id',
                    $schoolId
                )
                ->where(
                    'id',
                    '!=',
                    $paymentMode->id
                )
                ->update([
                    'is_default' => false
                ]);
            }

            $paymentMode->update([
                'name' =>
                    trim($validated['name']),

                'code' =>
                    $this->formatCode(
                        $validated['code'] ?? null
                    ),

                'mode_type' =>
                    $validated['mode_type'],

                'requires_reference' =>
                    $request->boolean(
                        'requires_reference'
                    ),

                'requires_bank' =>
                    $request->boolean(
                        'requires_bank'
                    ),

                'requires_instrument_date' =>
                    $request->boolean(
                        'requires_instrument_date'
                    ),

                'is_online' =>
                    $request->boolean(
                        'is_online'
                    ),

                'is_default' =>
                    $request->boolean(
                        'is_default'
                    ),

                'description' =>
                    $validated['description']
                    ?? null,

                'sort_order' =>
                    $validated['sort_order']
                    ?? 0,

                'status' =>
                    $request->boolean('status'),
            ]);
        });

        return redirect()
            ->route('payment-modes.index')
            ->with(
                'success',
                'Payment Mode updated successfully.'
            );
    }


    public function destroy(
        PaymentMode $paymentMode
    ) {
        $this->authorizeSchool(
            $paymentMode
        );

        /*
         * Later, once Fee Receipts use payment_mode_id,
         * deletion will be blocked when transactions exist.
         */

        $paymentMode->delete();

        return redirect()
            ->route('payment-modes.index')
            ->with(
                'success',
                'Payment Mode deleted successfully.'
            );
    }


    private function validateData(
        Request $request,
        int $schoolId,
        ?int $ignoreId = null
    ): array {

        return $request->validate([

            'name' => [
                'required',
                'string',
                'max:100',

                Rule::unique(
                    'payment_modes',
                    'name'
                )
                ->where(
                    fn ($q) =>
                        $q->where(
                            'school_id',
                            $schoolId
                        )
                )
                ->ignore($ignoreId),
            ],

            'code' => [
                'nullable',
                'string',
                'max:30',

                Rule::unique(
                    'payment_modes',
                    'code'
                )
                ->where(
                    fn ($q) =>
                        $q->where(
                            'school_id',
                            $schoolId
                        )
                )
                ->ignore($ignoreId),
            ],

            'mode_type' => [
                'required',
                Rule::in([
                    'cash',
                    'cheque',
                    'dd',
                    'card',
                    'upi',
                    'bank_transfer',
                    'online',
                    'other',
                ]),
            ],

            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'sort_order' => [
                'nullable',
                'integer',
                'min:0',
            ],
        ]);
    }


    private function authorizeSchool(
        PaymentMode $paymentMode
    ): void {

        abort_unless(
            (int) $paymentMode->school_id ===
            (int) Auth::user()->school_id,
            403
        );
    }


    private function formatCode(
        ?string $code
    ): ?string {

        if (!$code) {
            return null;
        }

        return strtoupper(
            trim($code)
        );
    }
}