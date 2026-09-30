<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\FeeHead;
use App\Models\FeeStructure;
use App\Models\FeeStructureItem;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class FeeStructureController extends Controller
{
    public function index(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        $query = FeeStructure::with([
                'academicYear',
                'schoolClass',
                'items.feeHead.componentGroup',
                'items.feeHead.feeCycle',
            ])
            ->where('school_id', $schoolId);

        if ($request->filled('academic_year_id')) {
            $query->where(
                'academic_year_id',
                $request->academic_year_id
            );
        }

        if ($request->filled('school_class_id')) {
            $query->where(
                'school_class_id',
                $request->school_class_id
            );
        }

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(
                'name',
                'like',
                "%{$search}%"
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

        $feeStructures = $query
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $academicYears = AcademicYear::where(
                'school_id',
                $schoolId
            )
            ->orderByDesc('start_date')
            ->get();

        $classes = SchoolClass::where(
                'school_id',
                $schoolId
            )
            ->where('status', 1)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view(
            'fee-structures.index',
            compact(
                'feeStructures',
                'academicYears',
                'classes'
            )
        );
    }


    public function create()
    {
        $schoolId = Auth::user()->school_id;

        $academicYears = AcademicYear::where(
                'school_id',
                $schoolId
            )
            ->orderByDesc('start_date')
            ->get();

        $classes = SchoolClass::where(
                'school_id',
                $schoolId
            )
            ->where('status', 1)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $feeHeads = $this->getFeeComponents(
            $schoolId
        );

        return view(
            'fee-structures.create',
            compact(
                'academicYears',
                'classes',
                'feeHeads'
            )
        );
    }


    public function store(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        $validated = $this->validateData(
            $request,
            $schoolId
        );

        $this->validateComponents(
            $validated['items'],
            $schoolId
        );

        DB::transaction(function () use (
            $validated,
            $schoolId
        ) {

            $structure = FeeStructure::create([
                'school_id' =>
                    $schoolId,

                'academic_year_id' =>
                    $validated['academic_year_id'],

                'school_class_id' =>
                    $validated['school_class_id'],

                'name' =>
                    trim($validated['name']),

                'description' =>
                    $validated['description'] ?? null,

                'status' =>
                    $validated['status'] ?? true,
            ]);

            foreach (
                $validated['items'] as $index => $item
            ) {
                FeeStructureItem::create([
                    'fee_structure_id' =>
                        $structure->id,

                    'fee_head_id' =>
                        $item['fee_head_id'],

                    'amount' =>
                        $item['amount'],

                    'sort_order' =>
                        $index + 1,

                    'status' => true,
                ]);
            }
        });

        return redirect()
            ->route('fee-structures.index')
            ->with(
                'success',
                'Fee Template created successfully.'
            );
    }


    public function edit(
        FeeStructure $feeStructure
    ) {
        $this->authorizeSchool(
            $feeStructure
        );

        $schoolId = Auth::user()->school_id;

        $feeStructure->load([
            'items.feeHead.componentGroup',
            'items.feeHead.feeCycle',
        ]);

        $academicYears = AcademicYear::where(
                'school_id',
                $schoolId
            )
            ->orderByDesc('start_date')
            ->get();

        $classes = SchoolClass::where(
                'school_id',
                $schoolId
            )
            ->where('status', 1)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        /*
         * Include active components plus
         * components already used in template.
         */
        $usedIds = $feeStructure
            ->items
            ->pluck('fee_head_id');

        $feeHeads = FeeHead::with([
                'componentGroup',
                'feeCycle',
            ])
            ->where('school_id', $schoolId)
            ->where(function ($query) use ($usedIds) {
                $query->where('status', 1)
                    ->orWhereIn('id', $usedIds);
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view(
            'fee-structures.edit',
            compact(
                'feeStructure',
                'academicYears',
                'classes',
                'feeHeads'
            )
        );
    }


    public function update(
        Request $request,
        FeeStructure $feeStructure
    ) {
        $this->authorizeSchool(
            $feeStructure
        );

        $schoolId = Auth::user()->school_id;

        $validated = $this->validateData(
            $request,
            $schoolId,
            $feeStructure->id
        );

        $this->validateComponents(
            $validated['items'],
            $schoolId
        );

        /*
         * Once students have compiled dues,
         * we should not destructively rebuild
         * the template.
         */
        $hasGeneratedInstallments =
            $feeStructure
                ->items()
                ->whereHas('installments')
                ->exists();

        if ($hasGeneratedInstallments) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'This Fee Template already has generated installments. Its components cannot be rebuilt. Create a new Fee Template instead.'
                );
        }

        DB::transaction(function () use (
            $validated,
            $feeStructure
        ) {

            $feeStructure->update([
                'academic_year_id' =>
                    $validated['academic_year_id'],

                'school_class_id' =>
                    $validated['school_class_id'],

                'name' =>
                    trim($validated['name']),

                'description' =>
                    $validated['description'] ?? null,

                'status' =>
                    $validated['status'] ?? true,
            ]);

            /*
             * Safe only because we blocked
             * templates having installments.
             */
            $feeStructure->items()->delete();

            foreach (
                $validated['items'] as $index => $item
            ) {
                FeeStructureItem::create([
                    'fee_structure_id' =>
                        $feeStructure->id,

                    'fee_head_id' =>
                        $item['fee_head_id'],

                    'amount' =>
                        $item['amount'],

                    'sort_order' =>
                        $index + 1,

                    'status' => true,
                ]);
            }
        });

        return redirect()
            ->route('fee-structures.index')
            ->with(
                'success',
                'Fee Template updated successfully.'
            );
    }


    public function destroy(
        FeeStructure $feeStructure
    ) {
        $this->authorizeSchool(
            $feeStructure
        );

        /*
         * Existing student assignments
         * must protect financial history.
         */
        if (
            method_exists(
                $feeStructure,
                'studentAssignments'
            ) &&
            $feeStructure
                ->studentAssignments()
                ->exists()
        ) {
            return back()->with(
                'error',
                'This Fee Template is already assigned to students and cannot be deleted.'
            );
        }

        if (
            $feeStructure
                ->items()
                ->whereHas('installments')
                ->exists()
        ) {
            return back()->with(
                'error',
                'This Fee Template has generated installments and cannot be deleted.'
            );
        }

        DB::transaction(
            function () use ($feeStructure) {

                $feeStructure
                    ->items()
                    ->delete();

                $feeStructure->delete();
            }
        );

        return redirect()
            ->route('fee-structures.index')
            ->with(
                'success',
                'Fee Template deleted successfully.'
            );
    }


    private function validateData(
        Request $request,
        int $schoolId,
        ?int $ignoreId = null
    ): array {

        return $request->validate([

            'academic_year_id' => [
                'required',
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

            'school_class_id' => [
                'required',
                'integer',

                Rule::exists(
                    'school_classes',
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

                Rule::unique(
                    'fee_structures',
                    'name'
                )
                    ->where(
                        fn ($query) =>
                            $query
                                ->where(
                                    'school_id',
                                    $schoolId
                                )
                                ->where(
                                    'academic_year_id',
                                    $request
                                        ->academic_year_id
                                )
                                ->where(
                                    'school_class_id',
                                    $request
                                        ->school_class_id
                                )
                    )
                    ->ignore($ignoreId),
            ],

            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'status' => [
                'nullable',
                'boolean',
            ],

            'items' => [
                'required',
                'array',
                'min:1',
            ],

            'items.*.fee_head_id' => [
                'required',
                'integer',
                'distinct',
            ],

            'items.*.amount' => [
                'required',
                'numeric',
                'min:0',
                'max:9999999999.99',
            ],
        ]);
    }


    private function validateComponents(
        array $items,
        int $schoolId
    ): void {

        $ids = collect($items)
            ->pluck('fee_head_id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $validCount = FeeHead::where(
                'school_id',
                $schoolId
            )
            ->whereIn('id', $ids)
            ->count();

        abort_if(
            $validCount !== $ids->count(),
            422,
            'Invalid Fee Component selected.'
        );
    }


    private function getFeeComponents(
        int $schoolId
    ) {
        return FeeHead::with([
                'componentGroup',
                'feeCycle',
            ])
            ->where(
                'school_id',
                $schoolId
            )
            ->where('status', 1)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }


    private function authorizeSchool(
        FeeStructure $feeStructure
    ): void {

        abort_unless(
            (int) $feeStructure->school_id ===
            (int) Auth::user()->school_id,
            403
        );
    }
}