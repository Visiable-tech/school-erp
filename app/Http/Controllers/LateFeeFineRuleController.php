<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\LateFeeFineRule;
use App\Models\LateFeeFineSlab;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class LateFeeFineRuleController extends Controller
{
    public function index(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        $query = LateFeeFineRule::with([
            'academicYear',
            'slabs'
        ])
        ->where('school_id', $schoolId);

        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('academic_year_id')) {
            $query->where(
                'academic_year_id',
                $request->academic_year_id
            );
        }

        if (
            $request->filled('status') &&
            in_array($request->status, ['0', '1'], true)
        ) {
            $query->where(
                'status',
                $request->status
            );
        }

        $rules = $query
            ->orderByDesc('is_default')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        $academicYears = $this->academicYears(
            $schoolId
        );

        return view(
            'late-fee-fines.index',
            compact(
                'rules',
                'academicYears'
            )
        );
    }


    public function create()
    {
        $schoolId = Auth::user()->school_id;

        $academicYears = $this->academicYears(
            $schoolId
        );

        return view(
            'late-fee-fines.create',
            compact('academicYears')
        );
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

                LateFeeFineRule::where(
                    'school_id',
                    $schoolId
                )->update([
                    'is_default' => false
                ]);
            }

            $rule = LateFeeFineRule::create([
                'school_id' => $schoolId,

                'academic_year_id' =>
                    $validated['academic_year_id']
                    ?? null,

                'name' =>
                    trim($validated['name']),

                'code' =>
                    $this->upper(
                        $validated['code'] ?? null
                    ),

                'is_default' =>
                    $request->boolean('is_default'),

                'description' =>
                    $validated['description']
                    ?? null,

                'sort_order' =>
                    $validated['sort_order']
                    ?? 0,

                'status' =>
                    $request->boolean('status'),
            ]);

            $this->saveSlabs(
                $rule,
                $validated['slabs']
            );
        });

        return redirect()
            ->route('late-fee-fines.index')
            ->with(
                'success',
                'Late Fee Fine rule created successfully.'
            );
    }


    public function edit(
        LateFeeFineRule $lateFeeFine
    ) {
        $this->authorizeSchool(
            $lateFeeFine
        );

        $lateFeeFine->load('slabs');

        $academicYears = $this->academicYears(
            Auth::user()->school_id
        );

        return view(
            'late-fee-fines.edit',
            compact(
                'lateFeeFine',
                'academicYears'
            )
        );
    }


    public function update(
        Request $request,
        LateFeeFineRule $lateFeeFine
    ) {
        $this->authorizeSchool(
            $lateFeeFine
        );

        $schoolId = Auth::user()->school_id;

        $validated = $this->validateData(
            $request,
            $schoolId,
            $lateFeeFine->id
        );

        DB::transaction(function () use (
            $request,
            $validated,
            $schoolId,
            $lateFeeFine
        ) {

            if ($request->boolean('is_default')) {

                LateFeeFineRule::where(
                    'school_id',
                    $schoolId
                )
                ->where(
                    'id',
                    '!=',
                    $lateFeeFine->id
                )
                ->update([
                    'is_default' => false
                ]);
            }

            $lateFeeFine->update([
                'academic_year_id' =>
                    $validated['academic_year_id']
                    ?? null,

                'name' =>
                    trim($validated['name']),

                'code' =>
                    $this->upper(
                        $validated['code'] ?? null
                    ),

                'is_default' =>
                    $request->boolean('is_default'),

                'description' =>
                    $validated['description']
                    ?? null,

                'sort_order' =>
                    $validated['sort_order']
                    ?? 0,

                'status' =>
                    $request->boolean('status'),
            ]);

            /*
             * Fine rules are setup data.
             * Until we start referencing individual
             * slabs from transactions, rebuild them.
             */
            $lateFeeFine->slabs()->delete();

            $this->saveSlabs(
                $lateFeeFine,
                $validated['slabs']
            );
        });

        return redirect()
            ->route('late-fee-fines.index')
            ->with(
                'success',
                'Late Fee Fine rule updated successfully.'
            );
    }


    public function destroy(
        LateFeeFineRule $lateFeeFine
    ) {
        $this->authorizeSchool(
            $lateFeeFine
        );

        DB::transaction(function () use (
            $lateFeeFine
        ) {
            $lateFeeFine->delete();
        });

        return redirect()
            ->route('late-fee-fines.index')
            ->with(
                'success',
                'Late Fee Fine rule deleted successfully.'
            );
    }


    private function saveSlabs(
        LateFeeFineRule $rule,
        array $slabs
    ): void {

        foreach ($slabs as $index => $slab) {

            LateFeeFineSlab::create([
                'late_fee_fine_rule_id' =>
                    $rule->id,

                'period_type' =>
                    $slab['period_type'],

                'from_day' =>
                    $slab['period_type']
                        === 'day_range'
                            ? ($slab['from_day'] ?? null)
                            : null,

                'to_day' =>
                    $slab['period_type']
                        === 'day_range'
                            ? ($slab['to_day'] ?? null)
                            : null,

                'amount' =>
                    $slab['amount'],

                'per_month' =>
                    $slab['period_type']
                        === 'after_first_month',

                'sort_order' =>
                    $index + 1,

                'status' => true,
            ]);
        }
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
                    'late_fee_fine_rules',
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

            'slabs' => [
                'required',
                'array',
                'min:1',
            ],

            'slabs.*.period_type' => [
                'required',
                Rule::in([
                    'day_range',
                    'after_first_month'
                ]),
            ],

            'slabs.*.from_day' => [
                'nullable',
                'integer',
                'min:1',
                'max:31',
            ],

            'slabs.*.to_day' => [
                'nullable',
                'integer',
                'min:1',
                'max:31',
            ],

            'slabs.*.amount' => [
                'required',
                'numeric',
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
        LateFeeFineRule $rule
    ): void {

        abort_unless(
            (int) $rule->school_id ===
            (int) Auth::user()->school_id,
            403
        );
    }


    private function upper(
        ?string $value
    ): ?string {

        if (!$value) {
            return null;
        }

        return strtoupper(
            trim($value)
        );
    }
}