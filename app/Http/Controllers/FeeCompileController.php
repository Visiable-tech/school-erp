<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\FeeStructure;
use App\Models\Section;
use App\Models\SchoolClass;
use App\Models\StudentEnrollment;
use App\Models\StudentFeeAssignment;
use App\Models\StudentFeeDue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class FeeCompileController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Compile Fee Screen
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $schoolId = Auth::user()->school_id;

        $academicYears = AcademicYear::where(
            'school_id',
            $schoolId
        )
        ->orderByDesc('is_current')
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
            'fee-compile.index',
            compact(
                'academicYears',
                'classes'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Get Sections
    |--------------------------------------------------------------------------
    */

    public function sections(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        $request->validate([
            'academic_year_id' => [
                'required',
                'integer',
            ],

            'school_class_id' => [
                'required',
                'integer',
            ],
        ]);

        $sections = Section::where(
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
        ->where('status', 1)
        ->orderBy('sort_order')
        ->orderBy('name')
        ->get([
            'id',
            'name'
        ]);

        return response()->json(
            $sections
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Get Fee Templates
    |--------------------------------------------------------------------------
    */

    public function templates(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        $request->validate([
            'academic_year_id' => [
                'required',
                'integer',
            ],

            'school_class_id' => [
                'required',
                'integer',
            ],
        ]);

        $templates = FeeStructure::where(
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
        ->where('status', 1)
        ->orderBy('name')
        ->get([
            'id',
            'name'
        ]);

        return response()->json(
            $templates
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Preview
    |--------------------------------------------------------------------------
    */

    public function preview(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        $validated = $this->validateCompileRequest(
            $request
        );

        $feeStructure =
            $this->getFeeStructure(
                $validated,
                $schoolId
            );

        /*
         * All Fee Template items must have
         * generated installments.
         */
        $feeStructure->load([
            'items.feeHead',
            'items.installments',
        ]);

        if ($feeStructure->items->isEmpty()) {

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Selected Fee Template has no Fee Components.'
                );
        }

        foreach ($feeStructure->items as $item) {

            if (
                $item->installments
                    ->where('status', true)
                    ->isEmpty()
            ) {

                return back()
                    ->withInput()
                    ->with(
                        'error',
                        'Fee schedule has not been generated for "' .
                        ($item->feeHead->name ?? 'Fee Component') .
                        '". Generate its installments first.'
                    );
            }
        }


        $query = StudentEnrollment::with([
            'student',
            'section',
            'schoolClass',
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
            'school_class_id',
            $validated['school_class_id']
        );

        if (
            !empty($validated['section_id'])
        ) {
            $query->where(
                'section_id',
                $validated['section_id']
            );
        }

        /*
         * Adjust this condition if your enrollment
         * table uses a different active-status field.
         */
        $query->where('status', 1);

        $enrollments = $query
            ->orderBy('section_id')
            ->orderBy('roll_no')
            ->get();


        /*
         * Find students already compiled.
         */
        $alreadyCompiled =
            StudentFeeAssignment::where(
                'school_id',
                $schoolId
            )
            ->where(
                'academic_year_id',
                $validated['academic_year_id']
            )
            ->where(
                'fee_structure_id',
                $validated['fee_structure_id']
            )
            ->whereIn(
                'student_id',
                $enrollments->pluck(
                    'student_id'
                )
            )
            ->pluck('student_id')
            ->map(fn ($id) => (int) $id)
            ->all();


        /*
         * Calculate template amount.
         */
        $templateTotal = 0;

        foreach ($feeStructure->items as $item) {

            foreach (
                $item->installments
                    ->where('status', true)
                as $installment
            ) {
                $templateTotal +=
                    (float) $installment->amount;
            }
        }


        $academicYear =
            AcademicYear::where(
                'school_id',
                $schoolId
            )
            ->findOrFail(
                $validated['academic_year_id']
            );

        $schoolClass =
            SchoolClass::where(
                'school_id',
                $schoolId
            )
            ->findOrFail(
                $validated['school_class_id']
            );

        $section = null;

        if (
            !empty($validated['section_id'])
        ) {
            $section =
                Section::where(
                    'school_id',
                    $schoolId
                )
                ->findOrFail(
                    $validated['section_id']
                );
        }


        return view(
            'fee-compile.preview',
            compact(
                'enrollments',
                'alreadyCompiled',
                'feeStructure',
                'academicYear',
                'schoolClass',
                'section',
                'templateTotal',
                'validated'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Compile Selected Students
    |--------------------------------------------------------------------------
    */

    public function compile(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        $validated =
            $this->validateCompileRequest(
                $request,
                true
            );

        $feeStructure =
            $this->getFeeStructure(
                $validated,
                $schoolId
            );

        $feeStructure->load([
            'items.feeHead',
            'items.installments',
        ]);

        /*
         * Never compile a template with an
         * incomplete schedule.
         */
        foreach ($feeStructure->items as $item) {

            if (
                $item->installments
                    ->where('status', true)
                    ->isEmpty()
            ) {
                return back()->with(
                    'error',
                    'Installment schedule is missing for "' .
                    ($item->feeHead->name ?? 'Fee Component') .
                    '".'
                );
            }
        }


        $enrollments =
            StudentEnrollment::with('student')
            ->where(
                'school_id',
                $schoolId
            )
            ->where(
                'academic_year_id',
                $validated['academic_year_id']
            )
            ->where(
                'school_class_id',
                $validated['school_class_id']
            )
            ->whereIn(
                'id',
                $validated['enrollment_ids']
            )
            ->where('status', 1);

        if (
            !empty($validated['section_id'])
        ) {
            $enrollments->where(
                'section_id',
                $validated['section_id']
            );
        }

        $enrollments =
            $enrollments->get();


        if ($enrollments->isEmpty()) {

            return back()->with(
                'error',
                'No valid students selected.'
            );
        }


        $compiled = 0;
        $skipped = 0;

        DB::transaction(function () use (
            $enrollments,
            $feeStructure,
            $validated,
            $schoolId,
            &$compiled,
            &$skipped
        ) {

            foreach (
                $enrollments as $enrollment
            ) {

                /*
                 * Prevent duplicate compilation.
                 */
                $existing =
                    StudentFeeAssignment::where(
                        'school_id',
                        $schoolId
                    )
                    ->where(
                        'student_id',
                        $enrollment->student_id
                    )
                    ->where(
                        'academic_year_id',
                        $validated[
                            'academic_year_id'
                        ]
                    )
                    ->where(
                        'fee_structure_id',
                        $feeStructure->id
                    )
                    ->exists();

                if ($existing) {
                    $skipped++;
                    continue;
                }


                /*
                 * Create Assignment Header
                 */
                $assignment =
                    StudentFeeAssignment::create([

                        'school_id' =>
                            $schoolId,

                        'student_id' =>
                            $enrollment->student_id,

                        'student_enrollment_id' =>
                            $enrollment->id,

                        'academic_year_id' =>
                            $validated[
                                'academic_year_id'
                            ],

                        'fee_structure_id' =>
                            $feeStructure->id,

                        'assigned_date' =>
                            $validated[
                                'compile_date'
                            ],

                        /*
                         * Concessions will later
                         * come from Concession
                         * Assignment.
                         */
                        'discount_type' =>
                            'none',

                        'discount_value' =>
                            0,

                        'remarks' =>
                            'Compiled through Compile Fee',

                        'assigned_by' =>
                            Auth::id(),

                        'status' => 1,
                    ]);


                /*
                 * Snapshot every installment
                 * into Student Fee Dues.
                 */
                foreach (
                    $feeStructure->items
                    as $item
                ) {

                    foreach (
                        $item->installments
                            ->where(
                                'status',
                                true
                            )
                        as $installment
                    ) {

                        $baseAmount =
                            round(
                                (float)
                                $installment->amount,
                                2
                            );

                        $discountAmount = 0;
                        $fineAmount = 0;

                        $payableAmount =
                            $baseAmount;

                        StudentFeeDue::create([

                            'school_id' =>
                                $schoolId,

                            'student_fee_assignment_id' =>
                                $assignment->id,

                            'student_id' =>
                                $enrollment->student_id,

                            'student_enrollment_id' =>
                                $enrollment->id,

                            'academic_year_id' =>
                                $validated[
                                    'academic_year_id'
                                ],

                            'fee_structure_id' =>
                                $feeStructure->id,

                            'fee_structure_item_id' =>
                                $item->id,

                            'fee_installment_id' =>
                                $installment->id,

                            'fee_head_id' =>
                                $item->fee_head_id,

                            /*
                             * Snapshot name.
                             */
                            'fee_head_name' =>
                                $item->feeHead->name,

                            'installment_name' =>
                                $installment
                                    ->installment_name,

                            'due_date' =>
                                $installment
                                    ->due_date,

                            'base_amount' =>
                                $baseAmount,

                            'discount_amount' =>
                                $discountAmount,

                            'fine_amount' =>
                                $fineAmount,

                            'payable_amount' =>
                                $payableAmount,

                            'paid_amount' =>
                                0,

                            'balance_amount' =>
                                $payableAmount,

                            'payment_status' =>
                                'unpaid',

                            'status' => 1,
                        ]);
                    }
                }

                $compiled++;
            }
        });


        return redirect()
            ->route('fee-compile.index')
            ->with(
                'success',
                "{$compiled} student(s) compiled successfully. {$skipped} skipped because fee was already compiled."
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    private function validateCompileRequest(
        Request $request,
        bool $requireStudents = false
    ): array {

        $schoolId =
            Auth::user()->school_id;

        $rules = [

            'academic_year_id' => [
                'required',

                Rule::exists(
                    'academic_years',
                    'id'
                )->where(
                    fn ($q) =>
                        $q->where(
                            'school_id',
                            $schoolId
                        )
                ),
            ],

            'school_class_id' => [
                'required',

                Rule::exists(
                    'school_classes',
                    'id'
                )->where(
                    fn ($q) =>
                        $q->where(
                            'school_id',
                            $schoolId
                        )
                ),
            ],

            'section_id' => [
                'nullable',

                Rule::exists(
                    'sections',
                    'id'
                )->where(
                    fn ($q) =>
                        $q->where(
                            'school_id',
                            $schoolId
                        )
                ),
            ],

            'fee_structure_id' => [
                'required',

                Rule::exists(
                    'fee_structures',
                    'id'
                )->where(
                    fn ($q) =>
                        $q->where(
                            'school_id',
                            $schoolId
                        )
                ),
            ],

            'compile_date' => [
                'required',
                'date',
            ],
        ];


        if ($requireStudents) {

            $rules[
                'enrollment_ids'
            ] = [
                'required',
                'array',
                'min:1',
            ];

            $rules[
                'enrollment_ids.*'
            ] = [
                'required',
                'integer',
            ];
        }


        return $request->validate(
            $rules
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Validate Template Against Year/Class
    |--------------------------------------------------------------------------
    */

    private function getFeeStructure(
        array $validated,
        int $schoolId
    ): FeeStructure {

        return FeeStructure::where(
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
            'school_class_id',
            $validated[
                'school_class_id'
            ]
        )
        ->where(
            'id',
            $validated[
                'fee_structure_id'
            ]
        )
        ->where('status', 1)
        ->firstOrFail();
    }
}