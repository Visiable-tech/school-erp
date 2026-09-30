<?php

namespace App\Http\Controllers;

use App\Models\BankMaster;
use App\Models\SchoolAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SchoolAccountController extends Controller
{
    public function index(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        $query = SchoolAccount::with('bank')
            ->where(
                'school_id',
                $schoolId
            );

        if ($request->filled('search')) {

            $search = trim(
                $request->search
            );

            $query->where(
                function ($q) use ($search) {

                    $q->where(
                        'account_name',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'account_number',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'purpose',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'upi_id',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhereHas(
                        'bank',
                        function ($bank) use ($search) {

                            $bank->where(
                                'bank_name',
                                'like',
                                "%{$search}%"
                            );
                        }
                    );
                }
            );
        }

        if ($request->filled('bank_id')) {

            $query->where(
                'bank_master_id',
                $request->bank_id
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

        $accounts = $query
            ->orderByDesc('is_default')
            ->orderBy('sort_order')
            ->orderBy('account_name')
            ->paginate(20)
            ->withQueryString();

        $banks = BankMaster::where(
                'school_id',
                $schoolId
            )
            ->where('status', 1)
            ->orderBy('sort_order')
            ->orderBy('bank_name')
            ->get();

        return view(
            'school-accounts.index',
            compact(
                'accounts',
                'banks'
            )
        );
    }


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

        return view(
            'school-accounts.create',
            compact('banks')
        );
    }


    public function store(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        $validated = $this->validateData(
            $request,
            $schoolId
        );

        BankMaster::where(
                'school_id',
                $schoolId
            )
            ->where(
                'id',
                $validated['bank_master_id']
            )
            ->where('status', 1)
            ->firstOrFail();

        DB::transaction(
            function () use (
                $request,
                $validated,
                $schoolId
            ) {

                /*
                 * Only one default account
                 * per school.
                 */
                if (
                    $request->boolean(
                        'is_default'
                    )
                ) {
                    SchoolAccount::where(
                        'school_id',
                        $schoolId
                    )->update([
                        'is_default' => false
                    ]);
                }

                SchoolAccount::create([
                    'school_id' =>
                        $schoolId,

                    'bank_master_id' =>
                        $validated[
                            'bank_master_id'
                        ],

                    'account_name' =>
                        trim(
                            $validated[
                                'account_name'
                            ]
                        ),

                    'account_number' =>
                        trim(
                            $validated[
                                'account_number'
                            ]
                        ),

                    'account_type' =>
                        $validated[
                            'account_type'
                        ],

                    'purpose' =>
                        $this->nullableTrim(
                            $validated[
                                'purpose'
                            ] ?? null
                        ),

                    'upi_id' =>
                        $this->nullableTrim(
                            $validated[
                                'upi_id'
                            ] ?? null
                        ),

                    'merchant_id' =>
                        $this->nullableTrim(
                            $validated[
                                'merchant_id'
                            ] ?? null
                        ),

                    'description' =>
                        $this->nullableTrim(
                            $validated[
                                'description'
                            ] ?? null
                        ),

                    'is_default' =>
                        $request->boolean(
                            'is_default'
                        ),

                    'sort_order' =>
                        $validated[
                            'sort_order'
                        ] ?? 0,

                    'status' =>
                        $request->boolean(
                            'status'
                        ),
                ]);
            }
        );

        return redirect()
            ->route(
                'school-accounts.index'
            )
            ->with(
                'success',
                'School Account created successfully.'
            );
    }


    public function edit(
        SchoolAccount $schoolAccount
    ) {
        $this->authorizeSchool(
            $schoolAccount
        );

        $schoolId = Auth::user()->school_id;

        $banks = BankMaster::where(
                'school_id',
                $schoolId
            )
            ->where(function ($query) use (
                $schoolAccount
            ) {

                $query->where(
                    'status',
                    1
                )
                ->orWhere(
                    'id',
                    $schoolAccount
                        ->bank_master_id
                );
            })
            ->orderBy('sort_order')
            ->orderBy('bank_name')
            ->get();

        return view(
            'school-accounts.edit',
            compact(
                'schoolAccount',
                'banks'
            )
        );
    }


    public function update(
        Request $request,
        SchoolAccount $schoolAccount
    ) {
        $this->authorizeSchool(
            $schoolAccount
        );

        $schoolId = Auth::user()->school_id;

        $validated = $this->validateData(
            $request,
            $schoolId,
            $schoolAccount->id
        );

        BankMaster::where(
                'school_id',
                $schoolId
            )
            ->where(
                'id',
                $validated['bank_master_id']
            )
            ->firstOrFail();

        DB::transaction(
            function () use (
                $request,
                $validated,
                $schoolId,
                $schoolAccount
            ) {

                if (
                    $request->boolean(
                        'is_default'
                    )
                ) {
                    SchoolAccount::where(
                            'school_id',
                            $schoolId
                        )
                        ->where(
                            'id',
                            '!=',
                            $schoolAccount->id
                        )
                        ->update([
                            'is_default' => false
                        ]);
                }

                $schoolAccount->update([
                    'bank_master_id' =>
                        $validated[
                            'bank_master_id'
                        ],

                    'account_name' =>
                        trim(
                            $validated[
                                'account_name'
                            ]
                        ),

                    'account_number' =>
                        trim(
                            $validated[
                                'account_number'
                            ]
                        ),

                    'account_type' =>
                        $validated[
                            'account_type'
                        ],

                    'purpose' =>
                        $this->nullableTrim(
                            $validated[
                                'purpose'
                            ] ?? null
                        ),

                    'upi_id' =>
                        $this->nullableTrim(
                            $validated[
                                'upi_id'
                            ] ?? null
                        ),

                    'merchant_id' =>
                        $this->nullableTrim(
                            $validated[
                                'merchant_id'
                            ] ?? null
                        ),

                    'description' =>
                        $this->nullableTrim(
                            $validated[
                                'description'
                            ] ?? null
                        ),

                    'is_default' =>
                        $request->boolean(
                            'is_default'
                        ),

                    'sort_order' =>
                        $validated[
                            'sort_order'
                        ] ?? 0,

                    'status' =>
                        $request->boolean(
                            'status'
                        ),
                ]);
            }
        );

        return redirect()
            ->route(
                'school-accounts.index'
            )
            ->with(
                'success',
                'School Account updated successfully.'
            );
    }


    public function destroy(
        SchoolAccount $schoolAccount
    ) {
        $this->authorizeSchool(
            $schoolAccount
        );

        /*
         * Later:
         * block deletion when account is used by
         * fee receipts/refunds/payment records.
         */

        $schoolAccount->delete();

        return redirect()
            ->route(
                'school-accounts.index'
            )
            ->with(
                'success',
                'School Account deleted successfully.'
            );
    }


    private function validateData(
        Request $request,
        int $schoolId,
        ?int $ignoreId = null
    ): array {

        return $request->validate([

            'bank_master_id' => [
                'required',
                'integer',

                Rule::exists(
                    'bank_masters',
                    'id'
                )->where(
                    fn ($query) =>
                        $query->where(
                            'school_id',
                            $schoolId
                        )
                ),
            ],

            'account_name' => [
                'required',
                'string',
                'max:150',
            ],

            'account_number' => [
                'required',
                'string',
                'max:100',

                Rule::unique(
                    'school_accounts',
                    'account_number'
                )
                    ->where(
                        fn ($query) =>
                            $query->where(
                                'school_id',
                                $schoolId
                            )
                    )
                    ->ignore($ignoreId),
            ],

            'account_type' => [
                'required',

                Rule::in([
                    'savings',
                    'current',
                    'cash_credit',
                    'overdraft',
                    'other',
                ]),
            ],

            'purpose' => [
                'nullable',
                'string',
                'max:150',
            ],

            'upi_id' => [
                'nullable',
                'string',
                'max:150',
            ],

            'merchant_id' => [
                'nullable',
                'string',
                'max:150',
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
        SchoolAccount $schoolAccount
    ): void {

        abort_unless(
            (int) $schoolAccount
                ->school_id ===
            (int) Auth::user()
                ->school_id,
            403
        );
    }


    private function nullableTrim(
        ?string $value
    ): ?string {

        if ($value === null) {
            return null;
        }

        $value = trim($value);

        return $value !== ''
            ? $value
            : null;
    }
}