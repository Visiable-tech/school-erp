<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StudentEnrollmentController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    public function create(Student $student)
    {
        $this->checkStudentAccess($student);

        $schoolId = auth()->user()->school_id;


        /*
        |--------------------------------------------------------------------------
        | Find Academic Years Already Used By Student
        |--------------------------------------------------------------------------
        */

        $usedAcademicYearIds =
            StudentEnrollment::where(
                'student_id',
                $student->id
            )
            ->pluck('academic_year_id')
            ->toArray();


        /*
        |--------------------------------------------------------------------------
        | Only Show Academic Years Not Already Enrolled
        |--------------------------------------------------------------------------
        */

        $academicYears =
            AcademicYear::where(
                'school_id',
                $schoolId
            )
            ->where('status', 1)
            ->whereNotIn(
                'id',
                $usedAcademicYearIds
            )
            ->orderByDesc('start_date')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Classes
        |--------------------------------------------------------------------------
        */

        $classes =
            SchoolClass::where(
                'school_id',
                $schoolId
            )
            ->where('status', 1)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();


        return view(
            'student-enrollments.create',
            compact(
                'student',
                'academicYears',
                'classes'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | GET SECTIONS - AJAX
    |--------------------------------------------------------------------------
    */

    public function getSections(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        $request->validate([
            'academic_year_id' => [
                'required',
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
                'name',
                'capacity'
            ]);


        return response()->json(
            $sections
        );
    }


    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(
        Request $request,
        Student $student
    ) {
        $this->checkStudentAccess($student);

        $schoolId = auth()->user()->school_id;


        $validated = $request->validate([

            'academic_year_id' => [
                'required',

                Rule::exists(
                    'academic_years',
                    'id'
                )->where(
                    fn ($query) =>
                    $query
                        ->where(
                            'school_id',
                            $schoolId
                        )
                        ->where(
                            'status',
                            1
                        )
                ),
            ],


            'school_class_id' => [
                'required',

                Rule::exists(
                    'school_classes',
                    'id'
                )->where(
                    fn ($query) =>
                    $query
                        ->where(
                            'school_id',
                            $schoolId
                        )
                        ->where(
                            'status',
                            1
                        )
                ),
            ],


            'section_id' => [
                'required',
                'integer',
            ],


            'roll_no' => [
                'nullable',
                'string',
                'max:50',
            ],


            'enrollment_date' => [
                'required',
                'date',
            ],

        ]);


        /*
        |--------------------------------------------------------------------------
        | Validate Section
        |--------------------------------------------------------------------------
        */

        $section = Section::where(
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
                'id',
                $validated['section_id']
            )
            ->where(
                'status',
                1
            )
            ->first();


        if (!$section) {

            return back()
                ->withInput()
                ->withErrors([
                    'section_id' =>
                        'The selected section is invalid.'
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Student already enrolled in this year?
        |--------------------------------------------------------------------------
        */

        $alreadyExists =
            StudentEnrollment::where(
                'student_id',
                $student->id
            )
            ->where(
                'academic_year_id',
                $validated['academic_year_id']
            )
            ->exists();


        if ($alreadyExists) {

            return back()
                ->withInput()
                ->withErrors([
                    'academic_year_id' =>
                        'This student already has an enrollment for the selected academic year.'
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Roll Number Check
        |--------------------------------------------------------------------------
        */

        if (!empty($validated['roll_no'])) {

            $rollExists =
                StudentEnrollment::where(
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
                    'roll_no',
                    $validated['roll_no']
                )
                ->exists();


            if ($rollExists) {

                return back()
                    ->withInput()
                    ->withErrors([
                        'roll_no' =>
                            'This roll number is already assigned in the selected section.'
                    ]);
            }

        }


        /*
        |--------------------------------------------------------------------------
        | Create Enrollment
        |--------------------------------------------------------------------------
        */

        DB::transaction(function () use (
            $validated,
            $student,
            $schoolId,
            $section
        ) {

            /*
             * Lock section so two simultaneous admissions
             * cannot bypass capacity.
             */

            $lockedSection =
                Section::whereKey(
                    $section->id
                )
                ->lockForUpdate()
                ->firstOrFail();


            /*
            |--------------------------------------------------------------------------
            | Section Capacity
            |--------------------------------------------------------------------------
            */

            if (
                $lockedSection->capacity
                && $lockedSection->capacity > 0
            ) {

                $studentCount =
                    StudentEnrollment::where(
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
                        'section_id',
                        $lockedSection->id
                    )
                    ->where(
                        'enrollment_status',
                        'active'
                    )
                    ->where(
                        'status',
                        1
                    )
                    ->count();


                if (
                    $studentCount
                    >=
                    $lockedSection->capacity
                ) {

                    throw new \Exception(
                        'Section capacity has been reached.'
                    );

                }

            }


            /*
             * Previous enrollment is no longer current.
             */

            StudentEnrollment::where(
                'student_id',
                $student->id
            )
            ->where(
                'is_current',
                1
            )
            ->update([
                'is_current' => 0,
            ]);


            /*
             * Create new enrollment.
             */

            StudentEnrollment::create([

                'school_id' =>
                    $schoolId,

                'student_id' =>
                    $student->id,

                'academic_year_id' =>
                    $validated[
                        'academic_year_id'
                    ],

                'school_class_id' =>
                    $validated[
                        'school_class_id'
                    ],

                'section_id' =>
                    $lockedSection->id,

                'roll_no' =>
                    $validated[
                        'roll_no'
                    ] ?? null,

                'enrollment_status' =>
                    'active',

                'enrollment_date' =>
                    $validated[
                        'enrollment_date'
                    ],

                'is_current' =>
                    true,

                'status' =>
                    true,

            ]);

        });


        return redirect()
            ->route(
                'students.show',
                $student
            )
            ->with(
                'success',
                'Student enrollment created successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | EDIT ENROLLMENT
    |--------------------------------------------------------------------------
    */

    public function edit(
        StudentEnrollment $studentEnrollment
    ) {
        $this->checkEnrollmentAccess(
            $studentEnrollment
        );


        $schoolId =
            auth()->user()->school_id;


        $studentEnrollment->load([
            'student',
            'academicYear',
            'schoolClass',
            'section',
        ]);


        $sections =
            Section::where(
                'school_id',
                $schoolId
            )
            ->where(
                'academic_year_id',
                $studentEnrollment
                    ->academic_year_id
            )
            ->where(
                'school_class_id',
                $studentEnrollment
                    ->school_class_id
            )
            ->where(
                'status',
                1
            )
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();


        return view(
            'student-enrollments.edit',
            compact(
                'studentEnrollment',
                'sections'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE ENROLLMENT
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        StudentEnrollment $studentEnrollment
    ) {
        $this->checkEnrollmentAccess(
            $studentEnrollment
        );


        $schoolId =
            auth()->user()->school_id;


        $validated =
            $request->validate([

                'section_id' => [
                    'required',
                    'integer',
                ],

                'roll_no' => [
                    'nullable',
                    'string',
                    'max:50',
                ],

                'enrollment_status' => [
                    'required',

                    Rule::in([
                        'active',
                        'promoted',
                        'transferred',
                        'withdrawn',
                        'completed',
                    ]),
                ],

            ]);


        /*
        |--------------------------------------------------------------------------
        | Section Validation
        |--------------------------------------------------------------------------
        */

        $section =
            Section::where(
                'school_id',
                $schoolId
            )
            ->where(
                'academic_year_id',
                $studentEnrollment
                    ->academic_year_id
            )
            ->where(
                'school_class_id',
                $studentEnrollment
                    ->school_class_id
            )
            ->where(
                'id',
                $validated['section_id']
            )
            ->where(
                'status',
                1
            )
            ->first();


        if (!$section) {

            return back()
                ->withInput()
                ->withErrors([
                    'section_id' =>
                        'Invalid section selected.'
                ]);

        }


        /*
        |--------------------------------------------------------------------------
        | Roll Number
        |--------------------------------------------------------------------------
        */

        if (!empty($validated['roll_no'])) {

            $rollExists =
                StudentEnrollment::where(
                    'school_id',
                    $schoolId
                )
                ->where(
                    'academic_year_id',
                    $studentEnrollment
                        ->academic_year_id
                )
                ->where(
                    'school_class_id',
                    $studentEnrollment
                        ->school_class_id
                )
                ->where(
                    'section_id',
                    $validated[
                        'section_id'
                    ]
                )
                ->where(
                    'roll_no',
                    $validated[
                        'roll_no'
                    ]
                )
                ->where(
                    'id',
                    '!=',
                    $studentEnrollment->id
                )
                ->exists();


            if ($rollExists) {

                return back()
                    ->withInput()
                    ->withErrors([
                        'roll_no' =>
                            'This roll number is already assigned in the selected section.'
                    ]);

            }

        }


        $studentEnrollment->update([

            'section_id' =>
                $validated['section_id'],

            'roll_no' =>
                $validated['roll_no']
                ?? null,

            'enrollment_status' =>
                $validated[
                    'enrollment_status'
                ],

        ]);


        return redirect()
            ->route(
                'students.show',
                $studentEnrollment->student_id
            )
            ->with(
                'success',
                'Academic enrollment updated successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | ACCESS CHECK
    |--------------------------------------------------------------------------
    */

    private function checkStudentAccess(
        Student $student
    ): void {

        abort_if(
            $student->school_id
            != auth()->user()->school_id,
            403
        );

    }


    private function checkEnrollmentAccess(
        StudentEnrollment $studentEnrollment
    ): void {

        abort_if(
            $studentEnrollment->school_id
            != auth()->user()->school_id,
            403
        );

    }
}