<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\StudentEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Models\StudentType;

class StudentManagementController extends Controller
{
    /**
     * Assign Roll No page
     */
    public function assignRollNo(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        /*
        |--------------------------------------------------------------------------
        | Filter Data
        |--------------------------------------------------------------------------
        */

        $academicYears = AcademicYear::where('school_id', $schoolId)
            ->orderByDesc('id')
            ->get();

        $classes = SchoolClass::where('school_id', $schoolId)
            ->orderBy('name')
            ->get();

        $sections = collect();

        if ($request->filled('academic_year_id')) {
            $sections = Section::where('school_id', $schoolId)
                ->where('academic_year_id', $request->academic_year_id)
                ->when(
                    $request->filled('school_class_id'),
                    function ($query) use ($request) {
                        $query->where(
                            'school_class_id',
                            $request->school_class_id
                        );
                    }
                )
                ->orderBy('name')
                ->get();
        }

        /*
        |--------------------------------------------------------------------------
        | Student Enrollments
        |--------------------------------------------------------------------------
        */

        $enrollments = collect();

        /*
         * Only show students after all three filters are selected.
         */
        if (
            $request->filled('academic_year_id') &&
            $request->filled('school_class_id') &&
            $request->filled('section_id')
        ) {
            $enrollments = StudentEnrollment::with([
                    'student',
                    'academicYear',
                    'schoolClass',
                    'section',
                ])
                ->where('school_id', $schoolId)
                ->where(
                    'academic_year_id',
                    $request->academic_year_id
                )
                ->where(
                    'school_class_id',
                    $request->school_class_id
                )
                ->where(
                    'section_id',
                    $request->section_id
                )
                ->where('status', 1)
                ->whereHas('student', function ($query) use ($request) {

                    $query->where('status', 1);

                    if ($request->filled('search')) {

                        $search = trim($request->search);

                        $query->where(function ($q) use ($search) {

                            $q->where(
                                'admission_no',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'student_name',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'father_name',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'father_mobile',
                                'like',
                                "%{$search}%"
                            );
                        });
                    }
                })
                ->get()
                ->sortBy(function ($enrollment) {

                    /*
                     * Students with existing roll numbers first,
                     * sorted numerically.
                     */

                    if (
                        $enrollment->roll_no !== null &&
                        $enrollment->roll_no !== ''
                    ) {
                        return sprintf(
                            '0-%010d',
                            (int) $enrollment->roll_no
                        );
                    }

                    return '1-' . strtolower(
                        $enrollment->student->student_name ?? ''
                    );
                })
                ->values();
        }

        return view(
            'students.management.assign-roll-no',
            compact(
                'academicYears',
                'classes',
                'sections',
                'enrollments'
            )
        );
    }


    /**
     * Bulk update roll numbers
     */
    public function updateRollNo(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'academic_year_id' => [
                'required',
                'integer',
            ],

            'school_class_id' => [
                'required',
                'integer',
            ],

            'section_id' => [
                'required',
                'integer',
            ],

            'roll_no' => [
                'required',
                'array',
            ],

            'roll_no.*' => [
                'nullable',
                'integer',
                'min:1',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Verify Academic Year
        |--------------------------------------------------------------------------
        */

        $academicYear = AcademicYear::where(
                'school_id',
                $schoolId
            )
            ->where(
                'id',
                $validated['academic_year_id']
            )
            ->firstOrFail();


        /*
        |--------------------------------------------------------------------------
        | Verify Class
        |--------------------------------------------------------------------------
        */

        $schoolClass = SchoolClass::where(
                'school_id',
                $schoolId
            )
            ->where(
                'id',
                $validated['school_class_id']
            )
            ->firstOrFail();


        /*
        |--------------------------------------------------------------------------
        | Verify Section
        |--------------------------------------------------------------------------
        */

        $section = Section::where(
                'school_id',
                $schoolId
            )
            ->where(
                'id',
                $validated['section_id']
            )
            ->where(
                'academic_year_id',
                $validated['academic_year_id']
            )
            ->where(
                'school_class_id',
                $validated['school_class_id']
            )
            ->firstOrFail();


        /*
        |--------------------------------------------------------------------------
        | Remove Empty Values + Detect Duplicate Roll Numbers
        |--------------------------------------------------------------------------
        */

        $enteredRollNumbers = [];

        foreach ($validated['roll_no'] as $enrollmentId => $rollNo) {

            if ($rollNo === null || $rollNo === '') {
                continue;
            }

            $rollNo = (int) $rollNo;

            if (in_array($rollNo, $enteredRollNumbers, true)) {

                throw ValidationException::withMessages([
                    'roll_no' =>
                        'Duplicate roll number '
                        . $rollNo
                        . ' found. Each student must have a unique roll number within the section.',
                ]);
            }

            $enteredRollNumbers[] = $rollNo;
        }


        /*
        |--------------------------------------------------------------------------
        | Update Roll Numbers
        |--------------------------------------------------------------------------
        */

        DB::transaction(function () use (
            $validated,
            $schoolId
        ) {

            foreach (
                $validated['roll_no']
                as $enrollmentId => $rollNo
            ) {

                /*
                 * Very important:
                 * Only update enrollment records belonging to
                 * the selected school/year/class/section.
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
                        'school_class_id',
                        $validated['school_class_id']
                    )
                    ->where(
                        'section_id',
                        $validated['section_id']
                    )
                    ->where(
                        'id',
                        $enrollmentId
                    )
                    ->first();

                if (!$enrollment) {
                    continue;
                }

                $enrollment->update([
                    'roll_no' =>
                        ($rollNo === null || $rollNo === '')
                            ? null
                            : (int) $rollNo,
                ]);
            }
        });


        return redirect()
            ->route(
                'student-management.assign-roll-no',
                [
                    'academic_year_id' =>
                        $academicYear->id,

                    'school_class_id' =>
                        $schoolClass->id,

                    'section_id' =>
                        $section->id,

                    'search' =>
                        $request->search,
                ]
            )
            ->with(
                'success',
                'Roll numbers updated successfully.'
            );
    }

    public function getSections(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        $request->validate([
            'academic_year_id' => 'required|integer',
            'school_class_id'  => 'required|integer',
        ]);

        $sections = Section::where('school_id', $schoolId)
            ->where('academic_year_id', $request->academic_year_id)
            ->where('school_class_id', $request->school_class_id)
            ->orderBy('name')
            ->get([
                'id',
                'name'
            ]);

        return response()->json([
            'success'  => true,
            'sections' => $sections,
        ]);
    }

    public function sectionChange(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        $academicYears = AcademicYear::where(
                'school_id',
                $schoolId
            )
            ->orderByDesc('id')
            ->get();

        $classes = SchoolClass::where(
                'school_id',
                $schoolId
            )
            ->orderBy('name')
            ->get();

        $sections = collect();

        if (
            $request->filled('academic_year_id') &&
            $request->filled('school_class_id')
        ) {
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
                ->orderBy('name')
                ->get();
        }


        /*
        |--------------------------------------------------------------------------
        | Search Students
        |--------------------------------------------------------------------------
        */

        $enrollments = collect();

        if (
            $request->filled('academic_year_id') &&
            $request->filled('school_class_id') &&
            $request->filled('section_id')
        ) {
            $enrollments = StudentEnrollment::with([
                    'student',
                    'academicYear',
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
                ->where(
                    'school_class_id',
                    $request->school_class_id
                )
                ->where(
                    'section_id',
                    $request->section_id
                )
                ->where(
                    'is_current',
                    true
                )
                ->where(
                    'status',
                    1
                )
                ->whereHas(
                    'student',
                    function ($query) use ($request) {

                        $query->where(
                            'status',
                            1
                        );

                        if ($request->filled('search')) {

                            $search =
                                trim($request->search);

                            $query->where(
                                function ($q) use ($search) {

                                    $q->where(
                                        'admission_no',
                                        'like',
                                        "%{$search}%"
                                    )
                                    ->orWhere(
                                        'student_name',
                                        'like',
                                        "%{$search}%"
                                    )
                                    ->orWhere(
                                        'father_name',
                                        'like',
                                        "%{$search}%"
                                    )
                                    ->orWhere(
                                        'father_mobile',
                                        'like',
                                        "%{$search}%"
                                    );
                                }
                            );
                        }
                    }
                )
                ->orderByRaw(
                    'CASE
                        WHEN roll_no IS NULL THEN 1
                        ELSE 0
                    END'
                )
                ->orderBy('roll_no')
                ->get();
        }


        return view(
            'students.management.section-change',
            compact(
                'academicYears',
                'classes',
                'sections',
                'enrollments'
            )
        );
    }

    public function updateSection(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        $validated = $request->validate([
            'enrollment_id' => [
                'required',
                'integer',
            ],

            'new_section_id' => [
                'required',
                'integer',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Find Enrollment
        |--------------------------------------------------------------------------
        */

        $enrollment = StudentEnrollment::with([
                'student',
                'section',
            ])
            ->where(
                'school_id',
                $schoolId
            )
            ->where(
                'id',
                $validated['enrollment_id']
            )
            ->where(
                'is_current',
                true
            )
            ->firstOrFail();


        /*
        |--------------------------------------------------------------------------
        | Validate Destination Section
        |--------------------------------------------------------------------------
        */

        $newSection = Section::where(
                'school_id',
                $schoolId
            )
            ->where(
                'id',
                $validated['new_section_id']
            )
            ->where(
                'academic_year_id',
                $enrollment->academic_year_id
            )
            ->where(
                'school_class_id',
                $enrollment->school_class_id
            )
            ->firstOrFail();


        /*
        |--------------------------------------------------------------------------
        | Same Section Check
        |--------------------------------------------------------------------------
        */

        if (
            (int) $enrollment->section_id ===
            (int) $newSection->id
        ) {
            return redirect()
                ->back()
                ->withErrors([
                    'new_section_id' =>
                        'Student is already assigned to this section.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Change Section
        |--------------------------------------------------------------------------
        */

        DB::transaction(
            function () use (
                $enrollment,
                $newSection
            ) {
                $enrollment->update([
                    'section_id' =>
                        $newSection->id,

                    /*
                    * Roll number is section-specific.
                    * Clear it after moving the student.
                    */
                    'roll_no' =>
                        null,
                ]);
            }
        );


        return redirect()
            ->route(
                'student-management.section-change',
                [
                    'academic_year_id' =>
                        $enrollment->academic_year_id,

                    'school_class_id' =>
                        $enrollment->school_class_id,

                    'section_id' =>
                        $enrollment->section_id,
                ]
            )
            ->with(
                'success',
                'Student section changed successfully. Please assign a new roll number if required.'
            );
    }

    public function sectionChangeMultiple(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        /*
        |--------------------------------------------------------------------------
        | Academic Years
        |--------------------------------------------------------------------------
        */

        $academicYears = AcademicYear::where(
                'school_id',
                $schoolId
            )
            ->orderByDesc('id')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Classes
        |--------------------------------------------------------------------------
        */

        $classes = SchoolClass::where(
                'school_id',
                $schoolId
            )
            ->orderBy('name')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Sections
        |--------------------------------------------------------------------------
        */

        $sections = collect();

        if (
            $request->filled('academic_year_id') &&
            $request->filled('school_class_id')
        ) {
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
                ->orderBy('name')
                ->get();
        }


        /*
        |--------------------------------------------------------------------------
        | Students
        |--------------------------------------------------------------------------
        */

        $enrollments = collect();

        if (
            $request->filled('academic_year_id') &&
            $request->filled('school_class_id') &&
            $request->filled('section_id')
        ) {
            $enrollments = StudentEnrollment::with([
                    'student',
                    'academicYear',
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
                ->where(
                    'school_class_id',
                    $request->school_class_id
                )
                ->where(
                    'section_id',
                    $request->section_id
                )
                ->where(
                    'is_current',
                    true
                )
                ->where(
                    'status',
                    1
                )
                ->whereHas(
                    'student',
                    function ($query) use ($request) {

                        $query->where(
                            'status',
                            1
                        );

                        if ($request->filled('search')) {

                            $search = trim(
                                $request->search
                            );

                            $query->where(
                                function ($q) use ($search) {

                                    $q->where(
                                        'admission_no',
                                        'like',
                                        "%{$search}%"
                                    )
                                    ->orWhere(
                                        'student_name',
                                        'like',
                                        "%{$search}%"
                                    )
                                    ->orWhere(
                                        'father_name',
                                        'like',
                                        "%{$search}%"
                                    )
                                    ->orWhere(
                                        'father_mobile',
                                        'like',
                                        "%{$search}%"
                                    );
                                }
                            );
                        }
                    }
                )
                ->orderByRaw(
                    'CASE
                        WHEN roll_no IS NULL THEN 1
                        ELSE 0
                    END'
                )
                ->orderBy('roll_no')
                ->get();
        }


        return view(
            'students.management.section-change-multiple',
            compact(
                'academicYears',
                'classes',
                'sections',
                'enrollments'
            )
        );
    }

    public function updateSectionMultiple(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        /*
        |--------------------------------------------------------------------------
        | Validate
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'academic_year_id' => [
                'required',
                'integer',
            ],

            'school_class_id' => [
                'required',
                'integer',
            ],

            'current_section_id' => [
                'required',
                'integer',
            ],

            'new_section_id' => [
                'required',
                'integer',
                'different:current_section_id',
            ],

            'enrollment_ids' => [
                'required',
                'array',
                'min:1',
            ],

            'enrollment_ids.*' => [
                'required',
                'integer',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Validate Current Section
        |--------------------------------------------------------------------------
        */

        $currentSection = Section::where(
                'school_id',
                $schoolId
            )
            ->where(
                'id',
                $validated['current_section_id']
            )
            ->where(
                'academic_year_id',
                $validated['academic_year_id']
            )
            ->where(
                'school_class_id',
                $validated['school_class_id']
            )
            ->firstOrFail();


        /*
        |--------------------------------------------------------------------------
        | Validate Destination Section
        |--------------------------------------------------------------------------
        */

        $newSection = Section::where(
                'school_id',
                $schoolId
            )
            ->where(
                'id',
                $validated['new_section_id']
            )
            ->where(
                'academic_year_id',
                $validated['academic_year_id']
            )
            ->where(
                'school_class_id',
                $validated['school_class_id']
            )
            ->firstOrFail();


        /*
        |--------------------------------------------------------------------------
        | Get Valid Enrollment IDs
        |--------------------------------------------------------------------------
        */

        $enrollmentIds = StudentEnrollment::where(
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
            ->where(
                'section_id',
                $validated['current_section_id']
            )
            ->where(
                'is_current',
                true
            )
            ->where(
                'status',
                1
            )
            ->whereIn(
                'id',
                $validated['enrollment_ids']
            )
            ->pluck('id');


        if ($enrollmentIds->isEmpty()) {

            return redirect()
                ->back()
                ->withErrors([
                    'enrollment_ids' =>
                        'No valid students were selected.',
                ])
                ->withInput();
        }


        /*
        |--------------------------------------------------------------------------
        | Move Students
        |--------------------------------------------------------------------------
        */

        DB::transaction(
            function () use (
                $enrollmentIds,
                $newSection
            ) {

                StudentEnrollment::whereIn(
                    'id',
                    $enrollmentIds
                )
                ->update([
                    'section_id' =>
                        $newSection->id,

                    /*
                    * Roll number belongs to the section.
                    * Clear it after moving.
                    */
                    'roll_no' =>
                        null,

                    'updated_at' =>
                        now(),
                ]);
            }
        );


        return redirect()
            ->route(
                'student-management.section-change-multiple',
                [
                    'academic_year_id' =>
                        $validated['academic_year_id'],

                    'school_class_id' =>
                        $validated['school_class_id'],

                    'section_id' =>
                        $currentSection->id,
                ]
            )
            ->with(
                'success',
                $enrollmentIds->count()
                . ' student(s) moved from '
                . $currentSection->name
                . ' to '
                . $newSection->name
                . ' successfully. Roll numbers have been cleared.'
            );
    }

    public function classChange(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        /*
        |--------------------------------------------------------------------------
        | Masters
        |--------------------------------------------------------------------------
        */

        $academicYears = AcademicYear::where('school_id', $schoolId)
            ->orderByDesc('id')
            ->get();

        $classes = SchoolClass::where('school_id', $schoolId)
            ->orderBy('name')
            ->get();

        $sections = collect();

        if (
            $request->filled('academic_year_id') &&
            $request->filled('school_class_id')
        ) {
            $sections = Section::where('school_id', $schoolId)
                ->where(
                    'academic_year_id',
                    $request->academic_year_id
                )
                ->where(
                    'school_class_id',
                    $request->school_class_id
                )
                ->orderBy('name')
                ->get();
        }


        /*
        |--------------------------------------------------------------------------
        | Students
        |--------------------------------------------------------------------------
        */

        $enrollments = collect();

        if (
            $request->filled('academic_year_id') &&
            $request->filled('school_class_id') &&
            $request->filled('section_id')
        ) {
            $enrollments = StudentEnrollment::with([
                    'student',
                    'academicYear',
                    'schoolClass',
                    'section',
                ])
                ->where('school_id', $schoolId)
                ->where(
                    'academic_year_id',
                    $request->academic_year_id
                )
                ->where(
                    'school_class_id',
                    $request->school_class_id
                )
                ->where(
                    'section_id',
                    $request->section_id
                )
                ->where('is_current', true)
                ->where('status', 1)
                ->whereHas(
                    'student',
                    function ($query) use ($request) {

                        $query->where('status', 1);

                        if ($request->filled('search')) {

                            $search = trim(
                                $request->search
                            );

                            $query->where(
                                function ($q) use ($search) {

                                    $q->where(
                                        'admission_no',
                                        'like',
                                        "%{$search}%"
                                    )
                                    ->orWhere(
                                        'student_name',
                                        'like',
                                        "%{$search}%"
                                    )
                                    ->orWhere(
                                        'father_name',
                                        'like',
                                        "%{$search}%"
                                    )
                                    ->orWhere(
                                        'father_mobile',
                                        'like',
                                        "%{$search}%"
                                    );
                                }
                            );
                        }
                    }
                )
                ->orderByRaw(
                    'CASE WHEN roll_no IS NULL THEN 1 ELSE 0 END'
                )
                ->orderBy('roll_no')
                ->get();
        }


        return view(
            'students.management.class-change',
            compact(
                'academicYears',
                'classes',
                'sections',
                'enrollments'
            )
        );
    }

    public function updateClass(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        $validated = $request->validate([

            'enrollment_id' => [
                'required',
                'integer',
            ],

            'new_class_id' => [
                'required',
                'integer',
            ],

            'new_section_id' => [
                'required',
                'integer',
            ],

        ]);


        /*
        |--------------------------------------------------------------------------
        | Current Enrollment
        |--------------------------------------------------------------------------
        */

        $enrollment = StudentEnrollment::with([
                'student',
                'schoolClass',
                'section',
            ])
            ->where('school_id', $schoolId)
            ->where(
                'id',
                $validated['enrollment_id']
            )
            ->where('is_current', true)
            ->firstOrFail();


        /*
        |--------------------------------------------------------------------------
        | Validate Destination Class
        |--------------------------------------------------------------------------
        */

        $newClass = SchoolClass::where(
                'school_id',
                $schoolId
            )
            ->where(
                'id',
                $validated['new_class_id']
            )
            ->firstOrFail();


        /*
        |--------------------------------------------------------------------------
        | Cannot Select Current Class
        |--------------------------------------------------------------------------
        */

        if (
            (int) $enrollment->school_class_id ===
            (int) $newClass->id
        ) {
            return back()
                ->withErrors([
                    'new_class_id' =>
                        'Please select a different class.',
                ])
                ->withInput();
        }


        /*
        |--------------------------------------------------------------------------
        | Validate Destination Section
        |--------------------------------------------------------------------------
        */

        $newSection = Section::where(
                'school_id',
                $schoolId
            )
            ->where(
                'id',
                $validated['new_section_id']
            )
            ->where(
                'academic_year_id',
                $enrollment->academic_year_id
            )
            ->where(
                'school_class_id',
                $newClass->id
            )
            ->firstOrFail();


        /*
        |--------------------------------------------------------------------------
        | Remember Current Filter
        |--------------------------------------------------------------------------
        */

        $oldClassId =
            $enrollment->school_class_id;

        $oldSectionId =
            $enrollment->section_id;

        $academicYearId =
            $enrollment->academic_year_id;


        /*
        |--------------------------------------------------------------------------
        | Change Class + Section
        |--------------------------------------------------------------------------
        */

        DB::transaction(
            function () use (
                $enrollment,
                $newClass,
                $newSection
            ) {

                $enrollment->update([

                    'school_class_id' =>
                        $newClass->id,

                    'section_id' =>
                        $newSection->id,

                    /*
                    * Destination class/section needs
                    * a fresh roll number.
                    */
                    'roll_no' =>
                        null,

                ]);
            }
        );


        return redirect()
            ->route(
                'student-management.class-change',
                [
                    'academic_year_id' =>
                        $academicYearId,

                    'school_class_id' =>
                        $oldClassId,

                    'section_id' =>
                        $oldSectionId,
                ]
            )
            ->with(
                'success',
                'Student moved successfully to '
                . $newClass->name
                . ' - '
                . $newSection->name
                . '. Roll number has been cleared.'
            );
    }

    public function typeChange(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        /*
        |--------------------------------------------------------------------------
        | Masters
        |--------------------------------------------------------------------------
        */

        $academicYears = AcademicYear::where(
                'school_id',
                $schoolId
            )
            ->orderByDesc('id')
            ->get();

        $classes = SchoolClass::where(
                'school_id',
                $schoolId
            )
            ->orderBy('name')
            ->get();

        $studentTypes = StudentType::where(
                'school_id',
                $schoolId
            )
            ->where('status', 1)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Sections
        |--------------------------------------------------------------------------
        */

        $sections = collect();

        if (
            $request->filled('academic_year_id') &&
            $request->filled('school_class_id')
        ) {
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
                ->orderBy('name')
                ->get();
        }


        /*
        |--------------------------------------------------------------------------
        | Students
        |--------------------------------------------------------------------------
        */

        $enrollments = collect();

        if (
            $request->filled('academic_year_id') &&
            $request->filled('school_class_id') &&
            $request->filled('section_id')
        ) {
            $enrollments = StudentEnrollment::with([
                    'student',
                    'schoolClass',
                    'section',
                    'studentType',
                ])
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
                ->where(
                    'section_id',
                    $request->section_id
                )
                ->where(
                    'is_current',
                    true
                )
                ->where(
                    'status',
                    1
                )
                ->whereHas(
                    'student',
                    function ($query) use ($request) {

                        $query->where(
                            'status',
                            1
                        );

                        if (
                            $request->filled('search')
                        ) {
                            $search = trim(
                                $request->search
                            );

                            $query->where(
                                function ($q) use ($search) {

                                    $q->where(
                                        'admission_no',
                                        'like',
                                        "%{$search}%"
                                    )
                                    ->orWhere(
                                        'student_name',
                                        'like',
                                        "%{$search}%"
                                    )
                                    ->orWhere(
                                        'father_mobile',
                                        'like',
                                        "%{$search}%"
                                    );
                                }
                            );
                        }
                    }
                )
                ->orderBy('roll_no')
                ->get();
        }


        return view(
            'students.management.type-change',
            compact(
                'academicYears',
                'classes',
                'sections',
                'studentTypes',
                'enrollments'
            )
        );
    }

    public function updateType(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        $validated = $request->validate([
            'academic_year_id' => [
                'required',
                'integer',
            ],

            'school_class_id' => [
                'required',
                'integer',
            ],

            'section_id' => [
                'required',
                'integer',
            ],

            'student_type_id' => [
                'required',
                'integer',
            ],

            'enrollment_ids' => [
                'required',
                'array',
                'min:1',
            ],

            'enrollment_ids.*' => [
                'required',
                'integer',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Validate Student Type
        |--------------------------------------------------------------------------
        */

        $studentType = StudentType::where(
                'school_id',
                $schoolId
            )
            ->where(
                'id',
                $validated['student_type_id']
            )
            ->where(
                'status',
                1
            )
            ->firstOrFail();


        /*
        |--------------------------------------------------------------------------
        | Validate Section
        |--------------------------------------------------------------------------
        */

        Section::where(
                'school_id',
                $schoolId
            )
            ->where(
                'id',
                $validated['section_id']
            )
            ->where(
                'academic_year_id',
                $validated['academic_year_id']
            )
            ->where(
                'school_class_id',
                $validated['school_class_id']
            )
            ->firstOrFail();


        /*
        |--------------------------------------------------------------------------
        | Get Valid Enrollments
        |--------------------------------------------------------------------------
        */

        $enrollmentIds = StudentEnrollment::where(
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
            ->where(
                'section_id',
                $validated['section_id']
            )
            ->where(
                'is_current',
                true
            )
            ->where(
                'status',
                1
            )
            ->whereIn(
                'id',
                $validated['enrollment_ids']
            )
            ->pluck('id');


        if ($enrollmentIds->isEmpty()) {

            return back()
                ->withErrors([
                    'enrollment_ids' =>
                        'Please select at least one valid student.',
                ])
                ->withInput();
        }


        /*
        |--------------------------------------------------------------------------
        | Bulk Update
        |--------------------------------------------------------------------------
        */

        DB::transaction(function () use (
            $enrollmentIds,
            $studentType
        ) {

            StudentEnrollment::whereIn(
                'id',
                $enrollmentIds
            )
            ->update([
                'student_type_id' =>
                    $studentType->id,

                'updated_at' =>
                    now(),
            ]);

        });


        return redirect()
            ->route(
                'student-management.type-change',
                [
                    'academic_year_id' =>
                        $validated['academic_year_id'],

                    'school_class_id' =>
                        $validated['school_class_id'],

                    'section_id' =>
                        $validated['section_id'],
                ]
            )
            ->with(
                'success',
                $enrollmentIds->count()
                . ' student(s) changed to '
                . $studentType->name
                . ' successfully.'
            );
    }
}