<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\PromotionStatus;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\StudentEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class StudentPromotionController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Promotions / Repetitions Page
    |--------------------------------------------------------------------------
    */

    public function index()
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


        $promotionStatuses = PromotionStatus::where(
                'school_id',
                $schoolId
            )
            ->where('status', 1)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();


        return view(
            'students.management.promotions-repetitions',
            compact(
                'academicYears',
                'classes',
                'promotionStatuses'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Get Sections
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
                'capacity',
            ]);


        return response()->json(
            $sections
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Get Students
    |--------------------------------------------------------------------------
    */

    public function getStudents(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        $request->validate([

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

        ]);


        /*
        |--------------------------------------------------------------------------
        | Validate Source Section
        |--------------------------------------------------------------------------
        */

        $section = Section::where(
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
                'id',
                $request->section_id
            )
            ->where('status', 1)
            ->firstOrFail();


        /*
        |--------------------------------------------------------------------------
        | Load Current Students
        |--------------------------------------------------------------------------
        */

        $enrollments = StudentEnrollment::with([
                'student',
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
                $section->id
            )
            ->where(
                'is_current',
                1
            )
            ->where(
                'enrollment_status',
                'active'
            )
            ->where(
                'status',
                1
            )
            ->whereHas(
                'student',
                function ($query) {

                    $query->where(
                        'status',
                        1
                    );

                }
            )
            ->orderByRaw(
                "CASE
                    WHEN roll_no REGEXP '^[0-9]+$'
                    THEN CAST(roll_no AS UNSIGNED)
                    ELSE 999999
                END"
            )
            ->orderBy('roll_no')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | JSON
        |--------------------------------------------------------------------------
        */

        $data = $enrollments->map(
            function ($enrollment) {

                return [

                    'enrollment_id' =>
                        $enrollment->id,

                    'student_id' =>
                        $enrollment->student_id,

                    'admission_no' =>
                        $enrollment
                            ->student
                            ?->admission_no,

                    'student_name' =>
                        $enrollment
                            ->student
                            ?->student_name,

                    'father_name' =>
                        $enrollment
                            ->student
                            ?->father_name,

                    'gender' =>
                        $enrollment
                            ->student
                            ?->gender,

                    'roll_no' =>
                        $enrollment->roll_no,

                    'student_type' =>
                        $enrollment
                            ->studentType
                            ?->name,

                ];

            }
        );


        return response()->json(
            $data
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Process Promotion / Repetition
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $schoolId = auth()->user()->school_id;


        /*
        |--------------------------------------------------------------------------
        | Validate Request
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([

            /*
            |--------------------------------------------------------------------------
            | Source
            |--------------------------------------------------------------------------
            */

            'from_academic_year_id' => [
                'required',
                'integer',
            ],

            'from_school_class_id' => [
                'required',
                'integer',
            ],

            'from_section_id' => [
                'required',
                'integer',
            ],


            /*
            |--------------------------------------------------------------------------
            | Promotion Status
            |--------------------------------------------------------------------------
            */

            'promotion_status_id' => [
                'required',
                'integer',
            ],


            /*
            |--------------------------------------------------------------------------
            | Destination
            |--------------------------------------------------------------------------
            */

            'to_academic_year_id' => [
                'required',
                'integer',
                'different:from_academic_year_id',
            ],

            'to_school_class_id' => [
                'required',
                'integer',
            ],

            'to_section_id' => [
                'required',
                'integer',
            ],


            /*
            |--------------------------------------------------------------------------
            | Students
            |--------------------------------------------------------------------------
            */

            'enrollment_ids' => [
                'required',
                'array',
                'min:1',
            ],

            'enrollment_ids.*' => [
                'required',
                'integer',
            ],


            /*
            |--------------------------------------------------------------------------
            | Date
            |--------------------------------------------------------------------------
            */

            'promotion_date' => [
                'required',
                'date',
            ],

        ]);


        /*
        |--------------------------------------------------------------------------
        | Promotion Status
        |--------------------------------------------------------------------------
        */

        $promotionStatus =
            PromotionStatus::where(
                'school_id',
                $schoolId
            )
            ->where(
                'id',
                $validated[
                    'promotion_status_id'
                ]
            )
            ->where(
                'status',
                1
            )
            ->first();


        if (!$promotionStatus) {

            throw ValidationException::withMessages([

                'promotion_status_id' =>
                    'Invalid promotion status.',

            ]);

        }


        /*
        |--------------------------------------------------------------------------
        | Validate Source Section
        |--------------------------------------------------------------------------
        */

        $sourceSection =
            Section::where(
                'school_id',
                $schoolId
            )
            ->where(
                'academic_year_id',
                $validated[
                    'from_academic_year_id'
                ]
            )
            ->where(
                'school_class_id',
                $validated[
                    'from_school_class_id'
                ]
            )
            ->where(
                'id',
                $validated[
                    'from_section_id'
                ]
            )
            ->where('status', 1)
            ->first();


        if (!$sourceSection) {

            throw ValidationException::withMessages([

                'from_section_id' =>
                    'Invalid source section.',

            ]);

        }


        /*
        |--------------------------------------------------------------------------
        | Validate Destination Section
        |--------------------------------------------------------------------------
        */

        $destinationSection =
            Section::where(
                'school_id',
                $schoolId
            )
            ->where(
                'academic_year_id',
                $validated[
                    'to_academic_year_id'
                ]
            )
            ->where(
                'school_class_id',
                $validated[
                    'to_school_class_id'
                ]
            )
            ->where(
                'id',
                $validated[
                    'to_section_id'
                ]
            )
            ->where('status', 1)
            ->first();


        if (!$destinationSection) {

            throw ValidationException::withMessages([

                'to_section_id' =>
                    'Invalid destination section.',

            ]);

        }


        /*
        |--------------------------------------------------------------------------
        | Academic Years
        |--------------------------------------------------------------------------
        */

        $fromYear =
            AcademicYear::where(
                'school_id',
                $schoolId
            )
            ->where(
                'id',
                $validated[
                    'from_academic_year_id'
                ]
            )
            ->where('status', 1)
            ->firstOrFail();


        $toYear =
            AcademicYear::where(
                'school_id',
                $schoolId
            )
            ->where(
                'id',
                $validated[
                    'to_academic_year_id'
                ]
            )
            ->where('status', 1)
            ->firstOrFail();


        /*
        |--------------------------------------------------------------------------
        | Destination Must Be Later Year
        |--------------------------------------------------------------------------
        */

        if (
            $toYear->start_date
            <=
            $fromYear->start_date
        ) {

            throw ValidationException::withMessages([

                'to_academic_year_id' =>
                    'Destination academic year must be after the source academic year.',

            ]);

        }


        /*
        |--------------------------------------------------------------------------
        | Validate Classes
        |--------------------------------------------------------------------------
        */

        $sourceClass =
            SchoolClass::where(
                'school_id',
                $schoolId
            )
            ->where(
                'id',
                $validated[
                    'from_school_class_id'
                ]
            )
            ->where('status', 1)
            ->firstOrFail();


        $destinationClass =
            SchoolClass::where(
                'school_id',
                $schoolId
            )
            ->where(
                'id',
                $validated[
                    'to_school_class_id'
                ]
            )
            ->where('status', 1)
            ->firstOrFail();


        /*
        |--------------------------------------------------------------------------
        | Repeated Student
        |--------------------------------------------------------------------------
        |
        | Repeated means:
        |
        | Next academic year
        | Same class
        |
        */

        if (
            $promotionStatus->type
            ===
            'repeated'
            &&
            (int) $sourceClass->id
            !==
            (int) $destinationClass->id
        ) {

            throw ValidationException::withMessages([

                'to_school_class_id' =>
                    'For Repeated status, destination class must be the same as the current class.',

            ]);

        }


        /*
        |--------------------------------------------------------------------------
        | Promoted Student
        |--------------------------------------------------------------------------
        |
        | Prevent accidental selection of same class.
        |--------------------------------------------------------------------------
        */

        if (
            $promotionStatus->type
            ===
            'promoted'
            &&
            (int) $sourceClass->id
            ===
            (int) $destinationClass->id
        ) {

            throw ValidationException::withMessages([

                'to_school_class_id' =>
                    'For Promoted status, please select a different destination class.',

            ]);

        }


        /*
        |--------------------------------------------------------------------------
        | Transaction
        |--------------------------------------------------------------------------
        */

        DB::transaction(
            function () use (
                $validated,
                $schoolId,
                $destinationSection,
                $promotionStatus
            ) {

                /*
                |--------------------------------------------------------------------------
                | Lock Destination Section
                |--------------------------------------------------------------------------
                */

                $lockedDestinationSection =
                    Section::whereKey(
                        $destinationSection->id
                    )
                    ->lockForUpdate()
                    ->firstOrFail();


                /*
                |--------------------------------------------------------------------------
                | Get Source Enrollments
                |--------------------------------------------------------------------------
                */

                $sourceEnrollments =
                    StudentEnrollment::with(
                        'student'
                    )
                    ->where(
                        'school_id',
                        $schoolId
                    )
                    ->whereIn(
                        'id',
                        $validated[
                            'enrollment_ids'
                        ]
                    )
                    ->where(
                        'academic_year_id',
                        $validated[
                            'from_academic_year_id'
                        ]
                    )
                    ->where(
                        'school_class_id',
                        $validated[
                            'from_school_class_id'
                        ]
                    )
                    ->where(
                        'section_id',
                        $validated[
                            'from_section_id'
                        ]
                    )
                    ->where(
                        'is_current',
                        1
                    )
                    ->where(
                        'enrollment_status',
                        'active'
                    )
                    ->where(
                        'status',
                        1
                    )
                    ->lockForUpdate()
                    ->get();


                /*
                |--------------------------------------------------------------------------
                | Validate Selected Students
                |--------------------------------------------------------------------------
                */

                if (
                    $sourceEnrollments->count()
                    !==
                    count(
                        $validated[
                            'enrollment_ids'
                        ]
                    )
                ) {

                    throw ValidationException::withMessages([

                        'enrollment_ids' =>
                            'One or more selected students are no longer eligible.',

                    ]);

                }


                /*
                |--------------------------------------------------------------------------
                | Destination Capacity
                |--------------------------------------------------------------------------
                */

                if (
                    $lockedDestinationSection
                        ->capacity
                    &&
                    $lockedDestinationSection
                        ->capacity > 0
                ) {

                    $existingCount =
                        StudentEnrollment::where(
                            'school_id',
                            $schoolId
                        )
                        ->where(
                            'academic_year_id',
                            $validated[
                                'to_academic_year_id'
                            ]
                        )
                        ->where(
                            'school_class_id',
                            $validated[
                                'to_school_class_id'
                            ]
                        )
                        ->where(
                            'section_id',
                            $lockedDestinationSection
                                ->id
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


                    $requiredSeats =
                        $sourceEnrollments->count();


                    if (
                        $existingCount
                        +
                        $requiredSeats
                        >
                        $lockedDestinationSection
                            ->capacity
                    ) {

                        throw ValidationException::withMessages([

                            'to_section_id' =>
                                'Destination section does not have enough available seats.',

                        ]);

                    }

                }


                /*
                |--------------------------------------------------------------------------
                | Process Selected Students
                |--------------------------------------------------------------------------
                */

                foreach (
                    $sourceEnrollments
                    as
                    $sourceEnrollment
                ) {

                    /*
                    |--------------------------------------------------------------------------
                    | Prevent Duplicate Enrollment
                    |--------------------------------------------------------------------------
                    */

                    $alreadyEnrolled =
                        StudentEnrollment::where(
                            'school_id',
                            $schoolId
                        )
                        ->where(
                            'student_id',
                            $sourceEnrollment
                                ->student_id
                        )
                        ->where(
                            'academic_year_id',
                            $validated[
                                'to_academic_year_id'
                            ]
                        )
                        ->exists();


                    if ($alreadyEnrolled) {

                        throw ValidationException::withMessages([

                            'enrollment_ids' =>
                                $sourceEnrollment
                                    ->student
                                    ->student_name
                                .
                                ' already has an enrollment for the destination academic year.',

                        ]);

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Close Current Enrollment
                    |--------------------------------------------------------------------------
                    |
                    | Keep existing supported enrollment_status value.
                    | Exact result is recorded through promotion_status_id.
                    |--------------------------------------------------------------------------
                    */

                    $sourceEnrollment->update([

                        'is_current' =>
                            false,

                        'enrollment_status' =>
                            'promoted',

                    ]);


                    /*
                    |--------------------------------------------------------------------------
                    | Create New Enrollment
                    |--------------------------------------------------------------------------
                    */

                    StudentEnrollment::create([

                        'school_id' =>
                            $schoolId,

                        'student_id' =>
                            $sourceEnrollment
                                ->student_id,

                        'academic_year_id' =>
                            $validated[
                                'to_academic_year_id'
                            ],

                        'school_class_id' =>
                            $validated[
                                'to_school_class_id'
                            ],

                        'section_id' =>
                            $lockedDestinationSection
                                ->id,


                        /*
                        |--------------------------------------------------------------------------
                        | Preserve Student Type
                        |--------------------------------------------------------------------------
                        */

                        'student_type_id' =>
                            $sourceEnrollment
                                ->student_type_id,


                        /*
                        |--------------------------------------------------------------------------
                        | Promotion / Repetition Status
                        |--------------------------------------------------------------------------
                        */

                        'promotion_status_id' =>
                            $promotionStatus->id,


                        /*
                        |--------------------------------------------------------------------------
                        | New Roll Will Be Assigned Later
                        |--------------------------------------------------------------------------
                        */

                        'roll_no' =>
                            null,


                        'enrollment_status' =>
                            'active',


                        'enrollment_date' =>
                            $validated[
                                'promotion_date'
                            ],


                        'is_current' =>
                            true,


                        'status' =>
                            true,

                    ]);

                }

            }
        );


        /*
        |--------------------------------------------------------------------------
        | Success
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route(
                'student-promotions.index'
            )
            ->with(
                'success',
                count(
                    $validated[
                        'enrollment_ids'
                    ]
                )
                .
                ' student(s) processed successfully as '
                .
                $promotionStatus->name
                .
                '.'
            );
    }
}