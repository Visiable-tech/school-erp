<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
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
    | Promotion Page
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

        return view(
            'student-promotions.index',
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
    | Load Students
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
         * Verify source section belongs to
         * selected school/year/class.
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


        $enrollments = StudentEnrollment::with(
                'student'
            )
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
                fn ($query) =>
                    $query->where(
                        'status',
                        1
                    )
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
                            ->admission_no,

                    'student_name' =>
                        $enrollment
                            ->student
                            ->student_name,

                    'roll_no' =>
                        $enrollment->roll_no,

                ];

            }
        );


        return response()->json($data);
    }


    /*
    |--------------------------------------------------------------------------
    | Promote Students
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $schoolId = auth()->user()->school_id;


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


            'promotion_date' => [
                'required',
                'date',
            ],

        ]);


        /*
        |--------------------------------------------------------------------------
        | Validate Source Section
        |--------------------------------------------------------------------------
        */

        $sourceSection = Section::where(
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
                    'Invalid source section.'
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
                    'Invalid destination section.'
            ]);

        }


        /*
        |--------------------------------------------------------------------------
        | Validate Academic Years
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
         * Prevent accidental backwards promotion.
         */

        if (
            $toYear->start_date
            <=
            $fromYear->start_date
        ) {

            throw ValidationException::withMessages([
                'to_academic_year_id' =>
                    'Destination academic year must be after the source academic year.'
            ]);

        }


        /*
        |--------------------------------------------------------------------------
        | Validate Classes
        |--------------------------------------------------------------------------
        */

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
        | Promotion Transaction
        |--------------------------------------------------------------------------
        */

        DB::transaction(function () use (
            $validated,
            $schoolId,
            $destinationSection
        ) {

            /*
             * Lock destination section while checking
             * capacity and creating enrollments.
             */

            $lockedDestinationSection =
                Section::whereKey(
                    $destinationSection->id
                )
                ->lockForUpdate()
                ->firstOrFail();


            /*
            |--------------------------------------------------------------------------
            | Get selected source enrollments
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
                        'One or more selected students are no longer eligible for promotion.'
                ]);

            }


            /*
            |--------------------------------------------------------------------------
            | Capacity
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
                            'The destination section does not have enough available seats.'
                    ]);

                }

            }


            /*
            |--------------------------------------------------------------------------
            | Promote
            |--------------------------------------------------------------------------
            */

            foreach (
                $sourceEnrollments
                as
                $sourceEnrollment
            ) {

                /*
                 * Student must not already have
                 * destination-year enrollment.
                 */

                $alreadyEnrolled =
                    StudentEnrollment::where(
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
                            ' already has an enrollment for the destination academic year.'
                    ]);

                }


                /*
                 * Close source enrollment.
                 */

                $sourceEnrollment->update([

                    'is_current' =>
                        false,

                    'enrollment_status' =>
                        'promoted',

                ]);


                /*
                 * Create destination enrollment.
                 *
                 * Roll number intentionally left null.
                 * It can be assigned after promotion.
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

        });


        return redirect()
            ->route(
                'student-promotions.index'
            )
            ->with(
                'success',
                'Selected students promoted successfully.'
            );
    }
}