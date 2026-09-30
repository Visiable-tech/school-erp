<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\FeeReceiptScheme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class FeeReceiptSchemeController extends Controller
{
    public function index(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        $query = FeeReceiptScheme::with(
            'academicYear'
        )
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
                        'name',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'code',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'prefix',
                        'like',
                        "%{$search}%"
                    );
                }
            );
        }

        if ($request->filled(
            'academic_year_id'
        )) {
            $query->where(
                'academic_year_id',
                $request->academic_year_id
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

        $schemes = $query
            ->orderByDesc('is_default')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        $academicYears =
            $this->academicYears(
                $schoolId
            );

        return view(
            'fee-receipt-schemes.index',
            compact(
                'schemes',
                'academicYears'
            )
        );
    }


    public function create()
    {
        $schoolId =
            Auth::user()->school_id;

        $academicYears =
            $this->academicYears(
                $schoolId
            );

        return view(
            'fee-receipt-schemes.create',
            compact('academicYears')
        );
    }


    public function store(Request $request)
    {
        $schoolId =
            Auth::user()->school_id;

        $validated =
            $this->validateData(
                $request,
                $schoolId
            );

        DB::transaction(
            function () use (
                $request,
                $validated,
                $schoolId
            ) {

                if (
                    $request->boolean(
                        'is_default'
                    )
                ) {
                    FeeReceiptScheme::where(
                        'school_id',
                        $schoolId
                    )->update([
                        'is_default' => false
                    ]);
                }

                FeeReceiptScheme::create([
                    'school_id' =>
                        $schoolId,

                    'academic_year_id' =>
                        $validated[
                            'academic_year_id'
                        ] ?? null,

                    'name' =>
                        trim(
                            $validated['name']
                        ),

                    'code' =>
                        $this->upper(
                            $validated['code']
                                ?? null
                        ),

                    'prefix' =>
                        $this->upper(
                            $validated['prefix']
                                ?? null
                        ),

                    'suffix' =>
                        $this->upper(
                            $validated['suffix']
                                ?? null
                        ),

                    'separator' =>
                        $validated[
                            'separator'
                        ],

                    'include_academic_year' =>
                        $request->boolean(
                            'include_academic_year'
                        ),

                    'number_length' =>
                        $validated[
                            'number_length'
                        ],

                    'start_number' =>
                        $validated[
                            'start_number'
                        ],

                    /*
                     * Keep zero until first
                     * actual receipt.
                     */
                    'last_number' => 0,

                    'reset_yearly' =>
                        $request->boolean(
                            'reset_yearly'
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
                'fee-receipt-schemes.index'
            )
            ->with(
                'success',
                'Receipt No. Scheme created successfully.'
            );
    }


    public function edit(
        FeeReceiptScheme $feeReceiptScheme
    ) {
        $this->authorizeSchool(
            $feeReceiptScheme
        );

        $schoolId =
            Auth::user()->school_id;

        $academicYears =
            $this->academicYears(
                $schoolId
            );

        return view(
            'fee-receipt-schemes.edit',
            compact(
                'feeReceiptScheme',
                'academicYears'
            )
        );
    }


    public function update(
        Request $request,
        FeeReceiptScheme $feeReceiptScheme
    ) {
        $this->authorizeSchool(
            $feeReceiptScheme
        );

        $schoolId =
            Auth::user()->school_id;

        $validated =
            $this->validateData(
                $request,
                $schoolId,
                $feeReceiptScheme->id
            );

        DB::transaction(
            function () use (
                $request,
                $validated,
                $schoolId,
                $feeReceiptScheme
            ) {

                if (
                    $request->boolean(
                        'is_default'
                    )
                ) {
                    FeeReceiptScheme::where(
                            'school_id',
                            $schoolId
                        )
                        ->where(
                            'id',
                            '!=',
                            $feeReceiptScheme->id
                        )
                        ->update([
                            'is_default' => false
                        ]);
                }

                $feeReceiptScheme->update([

                    'academic_year_id' =>
                        $validated[
                            'academic_year_id'
                        ] ?? null,

                    'name' =>
                        trim(
                            $validated['name']
                        ),

                    'code' =>
                        $this->upper(
                            $validated['code']
                                ?? null
                        ),

                    'prefix' =>
                        $this->upper(
                            $validated['prefix']
                                ?? null
                        ),

                    'suffix' =>
                        $this->upper(
                            $validated['suffix']
                                ?? null
                        ),

                    'separator' =>
                        $validated[
                            'separator'
                        ],

                    'include_academic_year' =>
                        $request->boolean(
                            'include_academic_year'
                        ),

                    'number_length' =>
                        $validated[
                            'number_length'
                        ],

                    'start_number' =>
                        $validated[
                            'start_number'
                        ],

                    /*
                     * Never reset last_number
                     * from this normal edit form.
                     */
                    'reset_yearly' =>
                        $request->boolean(
                            'reset_yearly'
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
                'fee-receipt-schemes.index'
            )
            ->with(
                'success',
                'Receipt No. Scheme updated successfully.'
            );
    }


    public function destroy(
        FeeReceiptScheme $feeReceiptScheme
    ) {
        $this->authorizeSchool(
            $feeReceiptScheme
        );

        /*
         * A last_number > 0 means this scheme
         * has already started issuing numbers.
         */
        if (
            $feeReceiptScheme->last_number > 0
        ) {
            return back()->with(
                'error',
                'This Receipt Scheme has already issued receipt numbers and cannot be deleted. Make it inactive instead.'
            );
        }

        $feeReceiptScheme->delete();

        return redirect()
            ->route(
                'fee-receipt-schemes.index'
            )
            ->with(
                'success',
                'Receipt No. Scheme deleted successfully.'
            );
    }


    private function validateData(
        Request $request,
        int $schoolId,
        ?int $ignoreId = null
    ): array {

        return $request->validate([

            'academic_year_id' => [
                'nullable',
                'integer',

                Rule::exists(
                    'academic_years',
                    'id'
                )->where(
                    fn ($query) =>
                        $query->where(
                            'school_id',
                            $schoolId
                        )
                ),
            ],

            'name' => [
                'required',
                'string',
                'max:150',
            ],

            'code' => [
                'nullable',
                'string',
                'max:50',

                Rule::unique(
                    'fee_receipt_schemes',
                    'code'
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

            'prefix' => [
                'nullable',
                'string',
                'max:50',
            ],

            'suffix' => [
                'nullable',
                'string',
                'max:50',
            ],

            'separator' => [
                'required',
                'string',
                'max:5',
            ],

            'number_length' => [
                'required',
                'integer',
                'min:1',
                'max:12',
            ],

            'start_number' => [
                'required',
                'integer',
                'min:1',
            ],

            'sort_order' => [
                'nullable',
                'integer',
                'min:0',
            ],
        ]);
    }


    private function academicYears(
        int $schoolId
    ) {
        return AcademicYear::where(
                'school_id',
                $schoolId
            )
            ->orderByDesc('start_date')
            ->get();
    }


    private function authorizeSchool(
        FeeReceiptScheme $scheme
    ): void {

        abort_unless(
            (int) $scheme->school_id ===
            (int) Auth::user()->school_id,
            403
        );
    }


    private function upper(
        ?string $value
    ): ?string {

        if ($value === null) {
            return null;
        }

        $value = trim($value);

        return $value !== ''
            ? strtoupper($value)
            : null;
    }
}