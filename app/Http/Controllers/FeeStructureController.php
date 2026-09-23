<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\FeeHead;
use App\Models\FeeStructure;
use App\Models\FeeStructureItem;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class FeeStructureController extends Controller
{
    public function index(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        $query = FeeStructure::with([
                'academicYear',
                'schoolClass',
                'items.feeHead',
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

        $feeStructures = $query
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $academicYears = AcademicYear::where(
                'school_id',
                $schoolId
            )
            ->where('status', 1)
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
        $schoolId = auth()->user()->school_id;

        $academicYears = AcademicYear::where(
                'school_id',
                $schoolId
            )
            ->where('status', 1)
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

        $feeHeads = FeeHead::where(
                'school_id',
                $schoolId
            )
            ->where('status', 1)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $currentAcademicYear = AcademicYear::where(
                'school_id',
                $schoolId
            )
            ->where('is_current', 1)
            ->where('status', 1)
            ->first();

        return view(
            'fee-structures.create',
            compact(
                'academicYears',
                'classes',
                'feeHeads',
                'currentAcademicYear'
            )
        );
    }


    public function store(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        $validated = $this->validateData(
            $request,
            $schoolId
        );

        /*
         * Verify Academic Year belongs to this school.
         */
        AcademicYear::where('school_id', $schoolId)
            ->where('id', $validated['academic_year_id'])
            ->where('status', 1)
            ->firstOrFail();

        /*
         * Verify Class belongs to this school.
         */
        SchoolClass::where('school_id', $schoolId)
            ->where('id', $validated['school_class_id'])
            ->where('status', 1)
            ->firstOrFail();


        /*
         * Verify all submitted Fee Heads.
         */
        $submittedHeadIds = collect(
            $validated['items']
        )
            ->pluck('fee_head_id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();


        if (
            $submittedHeadIds->count() !==
            count($validated['items'])
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'items' =>
                        'The same Fee Head cannot be added more than once.'
                ]);
        }


        $validHeadCount = FeeHead::where(
                'school_id',
                $schoolId
            )
            ->where('status', 1)
            ->whereIn('id', $submittedHeadIds)
            ->count();


        if ($validHeadCount !== $submittedHeadIds->count()) {

            return back()
                ->withInput()
                ->withErrors([
                    'items' =>
                        'One or more selected Fee Heads are invalid.'
                ]);
        }


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
                    $validated['name'],

                'description' =>
                    $validated['description'] ?? null,

                'status' =>
                    request()->boolean('status'),

            ]);


            foreach (
                $validated['items']
                as $index => $item
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

                    'status' =>
                        true,

                ]);
            }
        });


        return redirect()
            ->route('fee-structures.index')
            ->with(
                'success',
                'Fee Structure created successfully.'
            );
    }


    public function edit(FeeStructure $feeStructure)
    {
        $this->authorizeSchool($feeStructure);

        $schoolId = auth()->user()->school_id;

        $feeStructure->load('items.feeHead');

        $academicYears = AcademicYear::where(
                'school_id',
                $schoolId
            )
            ->where('status', 1)
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
         * Include inactive heads already attached to
         * this structure so old data can still be edited.
         */
        $usedHeadIds = $feeStructure
            ->items
            ->pluck('fee_head_id');

        $feeHeads = FeeHead::where(
                'school_id',
                $schoolId
            )
            ->where(function ($query) use ($usedHeadIds) {

                $query->where('status', 1);

                if ($usedHeadIds->isNotEmpty()) {
                    $query->orWhereIn(
                        'id',
                        $usedHeadIds
                    );
                }

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
        $this->authorizeSchool($feeStructure);

        $schoolId = auth()->user()->school_id;

        $validated = $this->validateData(
            $request,
            $schoolId,
            $feeStructure
        );


        AcademicYear::where('school_id', $schoolId)
            ->where('id', $validated['academic_year_id'])
            ->firstOrFail();

        SchoolClass::where('school_id', $schoolId)
            ->where('id', $validated['school_class_id'])
            ->firstOrFail();


        $submittedHeadIds = collect(
            $validated['items']
        )
            ->pluck('fee_head_id')
            ->map(fn ($id) => (int) $id);


        if (
            $submittedHeadIds->unique()->count()
            !==
            $submittedHeadIds->count()
        ) {

            return back()
                ->withInput()
                ->withErrors([
                    'items' =>
                        'The same Fee Head cannot be added more than once.'
                ]);
        }


        $validHeadCount = FeeHead::where(
                'school_id',
                $schoolId
            )
            ->whereIn(
                'id',
                $submittedHeadIds
            )
            ->count();


        if (
            $validHeadCount !==
            $submittedHeadIds->count()
        ) {

            return back()
                ->withInput()
                ->withErrors([
                    'items' =>
                        'One or more selected Fee Heads are invalid.'
                ]);
        }


        DB::transaction(function () use (
            $feeStructure,
            $validated
        ) {

            $feeStructure->update([

                'academic_year_id' =>
                    $validated['academic_year_id'],

                'school_class_id' =>
                    $validated['school_class_id'],

                'name' =>
                    $validated['name'],

                'description' =>
                    $validated['description'] ?? null,

                'status' =>
                    request()->boolean('status'),

            ]);


            /*
             * Safe for now because payments/student
             * assignments do not exist yet.
             *
             * Once Fee Collection is live we will stop
             * destructive rebuilding of historical items.
             */

            $feeStructure->items()->delete();


            foreach (
                $validated['items']
                as $index => $item
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

                    'status' =>
                        true,

                ]);
            }
        });


        return redirect()
            ->route('fee-structures.index')
            ->with(
                'success',
                'Fee Structure updated successfully.'
            );
    }


    public function destroy(
        FeeStructure $feeStructure
    ) {
        $this->authorizeSchool($feeStructure);

        /*
         * Later:
         * Block deletion if assigned to students
         * or used by fee transactions.
         */

        $feeStructure->delete();

        return redirect()
            ->route('fee-structures.index')
            ->with(
                'success',
                'Fee Structure deleted successfully.'
            );
    }


    private function validateData(
        Request $request,
        int $schoolId,
        ?FeeStructure $feeStructure = null
    ): array {

        return $request->validate([

            'academic_year_id' => [
                'required',
                'integer',
            ],

            'school_class_id' => [
                'required',
                'integer',
            ],

            'name' => [
                'required',
                'string',
                'max:150',

                Rule::unique(
                    'fee_structures',
                    'name'
                )
                    ->ignore(
                        $feeStructure?->id
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
                                    $request->academic_year_id
                                )
                                ->where(
                                    'school_class_id',
                                    $request->school_class_id
                                )
                    ),
            ],

            'description' =>
                'nullable|string|max:1000',

            'status' =>
                'nullable|boolean',

            'items' =>
                'required|array|min:1',

            'items.*.fee_head_id' =>
                'required|integer',

            'items.*.amount' =>
                'required|numeric|min:0|max:9999999999.99',

        ]);
    }


    private function authorizeSchool(
        FeeStructure $feeStructure
    ): void {

        abort_unless(
            (int) $feeStructure->school_id ===
            (int) auth()->user()->school_id,
            403
        );
    }
}