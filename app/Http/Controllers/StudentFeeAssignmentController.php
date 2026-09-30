<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\FeeStructure;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\StudentFeeAssignment;
use App\Models\StudentFeeDue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class StudentFeeAssignmentController extends Controller
{
    /**
     * Assignment list
     */
    public function index(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        $query = StudentFeeAssignment::with([
                'student',
                'academicYear',
                'feeStructure',
                'enrollment.schoolClass',
                'enrollment.section',
            ])
            ->where('school_id', $schoolId);

        if ($request->filled('academic_year_id')) {
            $query->where(
                'academic_year_id',
                $request->academic_year_id
            );
        }

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->whereHas('student', function ($q) use ($search) {
                $q->where('student_name', 'like', "%{$search}%")
                    ->orWhere('admission_no', 'like', "%{$search}%");
            });
        }

        $assignments = $query
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $academicYears = AcademicYear::where('school_id', $schoolId)
            ->orderByDesc('start_date')
            ->get();

        return view(
            'student-fee-assignments.index',
            compact('assignments', 'academicYears')
        );
    }


    /**
     * Assignment form
     */
    public function create()
    {
        $schoolId = Auth::user()->school_id;

        $academicYears = AcademicYear::where('school_id', $schoolId)
            ->where('status', 1)
            ->orderByDesc('start_date')
            ->get();

        $classes = SchoolClass::where('school_id', $schoolId)
            ->where('status', 1)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $currentAcademicYear = $academicYears
            ->firstWhere('is_current', true);

        return view(
            'student-fee-assignments.create',
            compact(
                'academicYears',
                'classes',
                'currentAcademicYear'
            )
        );
    }


    /**
     * Get sections using academic year + class
     */
    public function getSections(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        $request->validate([
            'academic_year_id' => 'required|integer',
            'school_class_id' => 'required|integer',
        ]);

        $academicYear = AcademicYear::where('school_id', $schoolId)
            ->where('id', $request->academic_year_id)
            ->where('status', 1)
            ->firstOrFail();

        $schoolClass = SchoolClass::where('school_id', $schoolId)
            ->where('id', $request->school_class_id)
            ->where('status', 1)
            ->firstOrFail();

        $sections = Section::where('school_id', $schoolId)
            ->where('academic_year_id', $academicYear->id)
            ->where('school_class_id', $schoolClass->id)
            ->where('status', 1)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'capacity'
            ]);

        return response()->json([
            'sections' => $sections
        ]);
    }


    /**
     * Students for selected section.
     *
     * IMPORTANT:
     * Students are loaded from enrollment records,
     * not directly from students table.
     */
    public function getStudents(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        $request->validate([
            'academic_year_id' => 'required|integer',
            'school_class_id' => 'required|integer',
            'section_id' => 'required|integer',
        ]);

        $section = Section::where('school_id', $schoolId)
            ->where('id', $request->section_id)
            ->where('academic_year_id', $request->academic_year_id)
            ->where('school_class_id', $request->school_class_id)
            ->where('status', 1)
            ->firstOrFail();

        $enrollments = StudentEnrollment::with('student')
            ->where('school_id', $schoolId)
            ->where('academic_year_id', $request->academic_year_id)
            ->where('school_class_id', $request->school_class_id)
            ->where('section_id', $section->id)
            ->where('status', 1)
            ->whereHas('student', function ($q) use ($schoolId) {
                $q->where('school_id', $schoolId)
                    ->where('status', 1);
            })
            ->orderByRaw('CASE WHEN roll_no IS NULL THEN 1 ELSE 0 END')
            ->orderBy('roll_no')
            ->get();

        $students = $enrollments->map(function ($enrollment) {
            return [
                'student_id' => $enrollment->student_id,
                'student_enrollment_id' => $enrollment->id,
                'student_name' => $enrollment->student->student_name,
                'admission_no' => $enrollment->student->admission_no,
                'roll_no' => $enrollment->roll_no,

                'fee_assigned' => StudentFeeAssignment::where(
                        'student_id',
                        $enrollment->student_id
                    )
                    ->where(
                        'academic_year_id',
                        $enrollment->academic_year_id
                    )
                    ->exists(),
            ];
        });

        return response()->json([
            'students' => $students
        ]);
    }


    /**
     * Fee structures available for selected class/year.
     */
    public function getFeeStructures(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        $request->validate([
            'academic_year_id' => 'required|integer',
            'school_class_id' => 'required|integer',
        ]);

        $structures = FeeStructure::where('school_id', $schoolId)
            ->where('academic_year_id', $request->academic_year_id)
            ->where('school_class_id', $request->school_class_id)
            ->where('status', 1)
            ->withCount([
                'installments as installment_count'
            ])
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'academic_year_id',
                'school_class_id'
            ]);

        return response()->json([
            'structures' => $structures
        ]);
    }


    /**
     * Preview installments before assignment.
     */
    public function preview(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        $validated = $request->validate([
            'academic_year_id' => 'required|integer',
            'school_class_id' => 'required|integer',
            'section_id' => 'required|integer',
            'student_id' => 'required|integer',
            'student_enrollment_id' => 'required|integer',
            'fee_structure_id' => 'required|integer',

            'discount_type' => [
                'required',
                Rule::in(['none', 'fixed', 'percentage']),
            ],

            'discount_value' => 'nullable|numeric|min:0',
        ]);

        $context = $this->validateAssignmentContext(
            $schoolId,
            $validated
        );

        $structure = $context['structure'];

        $discountType = $validated['discount_type'];

        $discountValue = (float) (
            $validated['discount_value'] ?? 0
        );

        if (
            $discountType === 'percentage'
            && $discountValue > 100
        ) {
            throw ValidationException::withMessages([
                'discount_value' =>
                    'Percentage discount cannot be greater than 100%.'
            ]);
        }

        $installments = $structure->installments;

        if ($installments->isEmpty()) {
            throw ValidationException::withMessages([
                'fee_structure_id' =>
                    'This fee structure has no installments. Generate the fee schedule first.'
            ]);
        }

        $baseTotal = round(
            (float) $installments->sum('amount'),
            2
        );

        /*
         * Assignment-level discount policy:
         *
         * percentage:
         * applied proportionally to every installment.
         *
         * fixed:
         * distributed across installments in chronological order,
         * never making an installment negative.
         */
        $discountMap = $this->calculateDiscountMap(
            $installments,
            $discountType,
            $discountValue
        );

        $rows = [];

        $discountTotal = 0;
        $payableTotal = 0;

        foreach ($installments as $installment) {

            $baseAmount = round(
                (float) $installment->amount,
                2
            );

            $discountAmount = round(
                $discountMap[$installment->id] ?? 0,
                2
            );

            $payableAmount = max(
                0,
                round($baseAmount - $discountAmount, 2)
            );

            $discountTotal += $discountAmount;
            $payableTotal += $payableAmount;

            $rows[] = [
                'installment_id' => $installment->id,

                'fee_head' =>
                    $installment->feeStructureItem
                        ->feeHead
                        ->name,

                'installment_name' =>
                    $installment->installment_name,

                'due_date' =>
                    $installment->due_date
                        ? $installment->due_date->format('d-m-Y')
                        : '',

                'base_amount' =>
                    number_format($baseAmount, 2, '.', ''),

                'discount_amount' =>
                    number_format($discountAmount, 2, '.', ''),

                'payable_amount' =>
                    number_format($payableAmount, 2, '.', ''),
            ];
        }

        return response()->json([
            'student' => [
                'name' => $context['student']->student_name,
                'admission_no' =>
                    $context['student']->admission_no,
                'roll_no' =>
                    $context['enrollment']->roll_no,
            ],

            'structure' => [
                'id' => $structure->id,
                'name' => $structure->name,
            ],

            'rows' => $rows,

            'summary' => [
                'base_total' =>
                    number_format($baseTotal, 2, '.', ''),

                'discount_total' =>
                    number_format(
                        round($discountTotal, 2),
                        2,
                        '.',
                        ''
                    ),

                'payable_total' =>
                    number_format(
                        round($payableTotal, 2),
                        2,
                        '.',
                        ''
                    ),
            ]
        ]);
    }


    /**
     * Save assignment and generate student dues.
     */
    public function store(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        $validated = $request->validate([
            'academic_year_id' => 'required|integer',
            'school_class_id' => 'required|integer',
            'section_id' => 'required|integer',

            'student_id' => 'required|integer',
            'student_enrollment_id' => 'required|integer',

            'fee_structure_id' => 'required|integer',

            'assigned_date' => 'required|date',

            'discount_type' => [
                'required',
                Rule::in([
                    'none',
                    'fixed',
                    'percentage'
                ]),
            ],

            'discount_value' =>
                'nullable|numeric|min:0',

            'remarks' =>
                'nullable|string|max:2000',
        ]);

        $discountType =
            $validated['discount_type'];

        $discountValue =
            (float) ($validated['discount_value'] ?? 0);

        if (
            $discountType === 'percentage'
            && $discountValue > 100
        ) {
            return back()
                ->withErrors([
                    'discount_value' =>
                        'Percentage discount cannot be greater than 100%.'
                ])
                ->withInput();
        }

        DB::transaction(function () use (
            $schoolId,
            $validated,
            $discountType,
            $discountValue
        ) {

            /*
             * Lock student enrollment so two simultaneous
             * requests cannot safely assign twice.
             */
            $enrollment = StudentEnrollment::where(
                    'school_id',
                    $schoolId
                )
                ->where(
                    'id',
                    $validated['student_enrollment_id']
                )
                ->lockForUpdate()
                ->firstOrFail();

            $context = $this->validateAssignmentContext(
                $schoolId,
                $validated,
                $enrollment
            );

            $student = $context['student'];
            $structure = $context['structure'];

            /*
             * Unique DB constraint is final protection,
             * but give user a friendly validation message.
             */
            $alreadyAssigned =
                StudentFeeAssignment::where(
                    'student_id',
                    $student->id
                )
                ->where(
                    'academic_year_id',
                    $validated['academic_year_id']
                )
                ->exists();

            if ($alreadyAssigned) {
                throw ValidationException::withMessages([
                    'student_id' =>
                        'Fee structure has already been assigned to this student for the selected academic year.'
                ]);
            }

            /*
             * Lock/reload installments and required relationships.
             */
            $structure->load([
                'installments' => function ($q) {
                    $q->where('status', 1)
                        ->orderBy('sort_order')
                        ->orderBy('due_date')
                        ->lockForUpdate();
                },
                'installments.feeStructureItem.feeHead',
            ]);

            if ($structure->installments->isEmpty()) {
                throw ValidationException::withMessages([
                    'fee_structure_id' =>
                        'This fee structure has no active installments.'
                ]);
            }

            $discountMap =
                $this->calculateDiscountMap(
                    $structure->installments,
                    $discountType,
                    $discountValue
                );

            $assignment =
                StudentFeeAssignment::create([
                    'school_id' => $schoolId,

                    'student_id' =>
                        $student->id,

                    'student_enrollment_id' =>
                        $enrollment->id,

                    'academic_year_id' =>
                        $validated['academic_year_id'],

                    'fee_structure_id' =>
                        $structure->id,

                    'assigned_date' =>
                        $validated['assigned_date'],

                    'discount_type' =>
                        $discountType,

                    'discount_value' =>
                        $discountValue,

                    'remarks' =>
                        $validated['remarks'] ?? null,

                    'assigned_by' =>
                        Auth::id(),

                    'status' => 1,
                ]);

            foreach (
                $structure->installments
                as $installment
            ) {

                $item =
                    $installment->feeStructureItem;

                if (
                    !$item
                    || !$item->feeHead
                ) {
                    throw ValidationException::withMessages([
                        'fee_structure_id' =>
                            'One or more fee installments have invalid fee head mapping.'
                    ]);
                }

                $baseAmount =
                    round(
                        (float) $installment->amount,
                        2
                    );

                $discountAmount =
                    round(
                        $discountMap[
                            $installment->id
                        ] ?? 0,
                        2
                    );

                $payableAmount =
                    max(
                        0,
                        round(
                            $baseAmount -
                            $discountAmount,
                            2
                        )
                    );

                StudentFeeDue::create([

                    'school_id' =>
                        $schoolId,

                    'student_fee_assignment_id' =>
                        $assignment->id,

                    'student_id' =>
                        $student->id,

                    'student_enrollment_id' =>
                        $enrollment->id,

                    'academic_year_id' =>
                        $validated['academic_year_id'],

                    'fee_structure_id' =>
                        $structure->id,

                    'fee_structure_item_id' =>
                        $item->id,

                    'fee_installment_id' =>
                        $installment->id,

                    'fee_head_id' =>
                        $item->fee_head_id,

                    /*
                     * Snapshot
                     */
                    'fee_head_name' =>
                        $item->feeHead->name,

                    'installment_name' =>
                        $installment->installment_name,

                    'period_start' =>
                        $installment->period_start,

                    'period_end' =>
                        $installment->period_end,

                    'due_date' =>
                        $installment->due_date,

                    'base_amount' =>
                        $baseAmount,

                    'discount_amount' =>
                        $discountAmount,

                    'fine_amount' =>
                        0,

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
        });

        return redirect()
            ->route('student-fee-assignments.index')
            ->with(
                'success',
                'Student fee structure assigned successfully.'
            );
    }


    /**
     * Assignment details
     */
    public function show(StudentFeeAssignment $studentFeeAssignment)
    {
        $this->authorizeSchool(
            $studentFeeAssignment
        );

        $studentFeeAssignment->load([
            'student',
            'academicYear',
            'feeStructure',
            'enrollment.schoolClass',
            'enrollment.section',
            'assignedBy',
            'dues' => function ($q) {
                $q->orderBy('due_date')
                    ->orderBy('id');
            }
        ]);

        return view(
            'student-fee-assignments.show',
            compact('studentFeeAssignment')
        );
    }


    /**
     * Validate that every selected record belongs
     * to the same school/year/class/section/student.
     */
    private function validateAssignmentContext(
        int $schoolId,
        array $data,
        ?StudentEnrollment $lockedEnrollment = null
    ): array {

        $academicYear =
            AcademicYear::where(
                'school_id',
                $schoolId
            )
            ->where(
                'id',
                $data['academic_year_id']
            )
            ->where('status', 1)
            ->firstOrFail();

        $schoolClass =
            SchoolClass::where(
                'school_id',
                $schoolId
            )
            ->where(
                'id',
                $data['school_class_id']
            )
            ->where('status', 1)
            ->firstOrFail();

        $section =
            Section::where(
                'school_id',
                $schoolId
            )
            ->where(
                'id',
                $data['section_id']
            )
            ->where(
                'academic_year_id',
                $academicYear->id
            )
            ->where(
                'school_class_id',
                $schoolClass->id
            )
            ->where('status', 1)
            ->firstOrFail();

        $student =
            Student::where(
                'school_id',
                $schoolId
            )
            ->where(
                'id',
                $data['student_id']
            )
            ->where('status', 1)
            ->firstOrFail();

        $enrollment =
            $lockedEnrollment
            ?: StudentEnrollment::where(
                'school_id',
                $schoolId
            )
            ->where(
                'id',
                $data['student_enrollment_id']
            )
            ->firstOrFail();

        if (
            (int) $enrollment->student_id !==
                (int) $student->id

            || (int) $enrollment->academic_year_id !==
                (int) $academicYear->id

            || (int) $enrollment->school_class_id !==
                (int) $schoolClass->id

            || (int) $enrollment->section_id !==
                (int) $section->id

            || !$enrollment->status
        ) {
            throw ValidationException::withMessages([
                'student_id' =>
                    'The selected student enrollment does not match the selected academic details.'
            ]);
        }

        $structure =
            FeeStructure::where(
                'school_id',
                $schoolId
            )
            ->where(
                'id',
                $data['fee_structure_id']
            )
            ->where(
                'academic_year_id',
                $academicYear->id
            )
            ->where(
                'school_class_id',
                $schoolClass->id
            )
            ->where('status', 1)
            ->with([
                'installments' => function ($q) {
                    $q->where('status', 1)
                        ->orderBy('sort_order')
                        ->orderBy('due_date');
                },
                'installments.feeStructureItem.feeHead',
            ])
            ->firstOrFail();

        return [
            'academicYear' => $academicYear,
            'schoolClass' => $schoolClass,
            'section' => $section,
            'student' => $student,
            'enrollment' => $enrollment,
            'structure' => $structure,
        ];
    }


    /**
     * Calculate assignment discount.
     */
    private function calculateDiscountMap(
        $installments,
        string $type,
        float $value
    ): array {

        $map = [];

        foreach ($installments as $installment) {
            $map[$installment->id] = 0;
        }

        if (
            $type === 'none'
            || $value <= 0
        ) {
            return $map;
        }

        /*
         * Percentage discount
         */
        if ($type === 'percentage') {

            foreach ($installments as $installment) {

                $amount =
                    (float) $installment->amount;

                $map[$installment->id] =
                    min(
                        $amount,
                        round(
                            $amount *
                            ($value / 100),
                            2
                        )
                    );
            }

            return $map;
        }

        /*
         * Fixed discount:
         * distribute chronologically.
         */
        if ($type === 'fixed') {

            $remaining =
                round($value, 2);

            foreach ($installments as $installment) {

                if ($remaining <= 0) {
                    break;
                }

                $amount =
                    round(
                        (float) $installment->amount,
                        2
                    );

                $discount =
                    min(
                        $amount,
                        $remaining
                    );

                $map[$installment->id] =
                    $discount;

                $remaining =
                    round(
                        $remaining -
                        $discount,
                        2
                    );
            }
        }

        return $map;
    }


    private function authorizeSchool(
        StudentFeeAssignment $assignment
    ): void {

        abort_unless(
            (int) $assignment->school_id ===
                (int) Auth::user()->school_id,
            403
        );
    }

    /**
     * Bulk fee assignment form
     */
    public function bulkCreate()
    {
        $schoolId = Auth::user()->school_id;

        $academicYears = AcademicYear::where('school_id', $schoolId)
            ->where('status', 1)
            ->orderByDesc('start_date')
            ->get();

        $classes = SchoolClass::where('school_id', $schoolId)
            ->where('status', 1)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $currentAcademicYear = $academicYears
            ->firstWhere('is_current', true);

        return view(
            'student-fee-assignments.bulk',
            compact(
                'academicYears',
                'classes',
                'currentAcademicYear'
            )
        );
    }


    /**
     * Generate fees for all students of selected
     * academic year / class / optional section.
     */
    public function bulkStore(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        $validated = $request->validate([
            'academic_year_id' => 'required|integer',
            'school_class_id' => 'required|integer',

            // Optional = entire class
            'section_id' => 'nullable|integer',

            'fee_structure_id' => 'required|integer',

            'assigned_date' => 'required|date',
        ]);

        /*
        * Validate academic year.
        */
        $academicYear = AcademicYear::where('school_id', $schoolId)
            ->where('id', $validated['academic_year_id'])
            ->where('status', 1)
            ->firstOrFail();


        /*
        * Validate class.
        */
        $schoolClass = SchoolClass::where('school_id', $schoolId)
            ->where('id', $validated['school_class_id'])
            ->where('status', 1)
            ->firstOrFail();


        /*
        * Validate optional section.
        */
        if (!empty($validated['section_id'])) {

            Section::where('school_id', $schoolId)
                ->where('id', $validated['section_id'])
                ->where(
                    'academic_year_id',
                    $academicYear->id
                )
                ->where(
                    'school_class_id',
                    $schoolClass->id
                )
                ->where('status', 1)
                ->firstOrFail();
        }


        /*
        * Validate Fee Structure.
        */
        $structure = FeeStructure::where(
                'school_id',
                $schoolId
            )
            ->where(
                'id',
                $validated['fee_structure_id']
            )
            ->where(
                'academic_year_id',
                $academicYear->id
            )
            ->where(
                'school_class_id',
                $schoolClass->id
            )
            ->where('status', 1)
            ->with([
                'installments' => function ($query) {
                    $query->where('status', 1)
                        ->orderBy('sort_order')
                        ->orderBy('due_date');
                },

                'installments.feeStructureItem.feeHead'
            ])
            ->firstOrFail();


        if ($structure->installments->isEmpty()) {

            return back()
                ->withErrors([
                    'fee_structure_id' =>
                        'This Fee Structure has no active installments. Generate the fee schedule first.'
                ])
                ->withInput();
        }


        /*
        * Validate every installment before starting.
        */
        foreach ($structure->installments as $installment) {

            if (
                !$installment->feeStructureItem ||
                !$installment->feeStructureItem->feeHead
            ) {
                return back()
                    ->withErrors([
                        'fee_structure_id' =>
                            'The Fee Structure contains an invalid installment or Fee Head mapping.'
                    ])
                    ->withInput();
            }
        }


        /*
        * Enrollment query.
        *
        * We assign fees against enrollment,
        * not directly against students.
        */
        $enrollmentQuery = StudentEnrollment::where(
                'school_id',
                $schoolId
            )
            ->where(
                'academic_year_id',
                $academicYear->id
            )
            ->where(
                'school_class_id',
                $schoolClass->id
            )
            ->where('status', 1)
            ->whereHas('student', function ($query) use ($schoolId) {

                $query->where(
                    'school_id',
                    $schoolId
                )
                ->where('status', 1);
            });


        /*
        * Optional section.
        */
        if (!empty($validated['section_id'])) {

            $enrollmentQuery->where(
                'section_id',
                $validated['section_id']
            );
        }


        /*
        * IMPORTANT:
        *
        * Skip students who already have an assignment
        * for this academic year.
        */
        $enrollmentQuery->whereDoesntHave(
            'feeAssignments',
            function ($query) use ($academicYear) {

                $query->where(
                    'academic_year_id',
                    $academicYear->id
                );
            }
        );


        $totalEligible = (clone $enrollmentQuery)->count();


        if ($totalEligible === 0) {

            return back()->with(
                'error',
                'No eligible students found. Fees may already be assigned to all selected students.'
            );
        }


        $created = 0;
        $skipped = 0;
        $failed = 0;


        /*
        * Chunk students.
        *
        * Do NOT load 3000+ students into memory at once.
        */
        $enrollmentQuery
            ->orderBy('id')
            ->chunkById(
                100,
                function ($enrollments) use (
                    $schoolId,
                    $academicYear,
                    $structure,
                    $validated,
                    &$created,
                    &$skipped,
                    &$failed
                ) {

                    foreach ($enrollments as $enrollment) {

                        try {

                            DB::transaction(function () use (
                                $schoolId,
                                $academicYear,
                                $structure,
                                $validated,
                                $enrollment,
                                &$created,
                                &$skipped
                            ) {

                                /*
                                * Lock enrollment.
                                */
                                $lockedEnrollment =
                                    StudentEnrollment::where(
                                        'school_id',
                                        $schoolId
                                    )
                                    ->where(
                                        'id',
                                        $enrollment->id
                                    )
                                    ->lockForUpdate()
                                    ->first();


                                if (!$lockedEnrollment) {

                                    $skipped++;
                                    return;
                                }


                                /*
                                * Recheck inside transaction.
                                *
                                * This protects against another user
                                * generating fees at the same time.
                                */
                                $alreadyAssigned =
                                    StudentFeeAssignment::where(
                                        'student_id',
                                        $lockedEnrollment->student_id
                                    )
                                    ->where(
                                        'academic_year_id',
                                        $academicYear->id
                                    )
                                    ->exists();


                                if ($alreadyAssigned) {

                                    $skipped++;
                                    return;
                                }


                                /*
                                * Create one assignment.
                                *
                                * Bulk generation always creates
                                * standard fees without discount.
                                */
                                $assignment =
                                    StudentFeeAssignment::create([

                                        'school_id' =>
                                            $schoolId,

                                        'student_id' =>
                                            $lockedEnrollment->student_id,

                                        'student_enrollment_id' =>
                                            $lockedEnrollment->id,

                                        'academic_year_id' =>
                                            $academicYear->id,

                                        'fee_structure_id' =>
                                            $structure->id,

                                        'assigned_date' =>
                                            $validated['assigned_date'],

                                        'discount_type' =>
                                            'none',

                                        'discount_value' =>
                                            0,

                                        'remarks' =>
                                            'Bulk fee assignment',

                                        'assigned_by' =>
                                            Auth::id(),

                                        'status' => 1,
                                    ]);


                                /*
                                * Snapshot every installment.
                                */
                                foreach (
                                    $structure->installments
                                    as $installment
                                ) {

                                    $item =
                                        $installment->feeStructureItem;

                                    $amount =
                                        round(
                                            (float) $installment->amount,
                                            2
                                        );


                                    StudentFeeDue::create([

                                        'school_id' =>
                                            $schoolId,

                                        'student_fee_assignment_id' =>
                                            $assignment->id,

                                        'student_id' =>
                                            $lockedEnrollment->student_id,

                                        'student_enrollment_id' =>
                                            $lockedEnrollment->id,

                                        'academic_year_id' =>
                                            $academicYear->id,

                                        'fee_structure_id' =>
                                            $structure->id,

                                        'fee_structure_item_id' =>
                                            $item->id,

                                        'fee_installment_id' =>
                                            $installment->id,

                                        'fee_head_id' =>
                                            $item->fee_head_id,

                                        /*
                                        * Snapshot fields
                                        */
                                        'fee_head_name' =>
                                            $item->feeHead->name,

                                        'installment_name' =>
                                            $installment->installment_name,

                                        'period_start' =>
                                            $installment->period_start,

                                        'period_end' =>
                                            $installment->period_end,

                                        'due_date' =>
                                            $installment->due_date,

                                        'base_amount' =>
                                            $amount,

                                        'discount_amount' =>
                                            0,

                                        'fine_amount' =>
                                            0,

                                        'payable_amount' =>
                                            $amount,

                                        'paid_amount' =>
                                            0,

                                        'balance_amount' =>
                                            $amount,

                                        'payment_status' =>
                                            'unpaid',

                                        'status' => 1,
                                    ]);
                                }


                                $created++;
                            });

                        } catch (\Throwable $e) {

                            $failed++;

                            report($e);
                        }
                    }
                }
            );


        return redirect()
            ->route('student-fee-assignments.index')
            ->with(
                'success',
                "Bulk fee generation completed. Created: {$created}, Skipped: {$skipped}, Failed: {$failed}."
            );
    }
}