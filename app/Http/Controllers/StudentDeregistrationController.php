<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\StudentDeregistration;
use App\Models\StudentEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StudentDeregistrationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | De-registration Page
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        $academicYears = AcademicYear::where('school_id', $schoolId)
            ->where('status', 1)
            ->orderByDesc('start_date')
            ->get();

        $classes = SchoolClass::where('school_id', $schoolId)
            ->where('status', 1)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $sections = collect();

        $enrollments = collect();


        /*
        |--------------------------------------------------------------------------
        | Sections
        |--------------------------------------------------------------------------
        */

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
                ->where('status', 1)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();
        }


        /*
        |--------------------------------------------------------------------------
        | Current Students
        |--------------------------------------------------------------------------
        */

        if (
            $request->filled('academic_year_id') &&
            $request->filled('school_class_id') &&
            $request->filled('section_id')
        ) {

            $query = StudentEnrollment::with([
                    'student',
                    'academicYear',
                    'schoolClass',
                    'section',
                    'studentType',
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
                ->where('enrollment_status', 'active')
                ->where('status', 1)
                ->whereHas(
                    'student',
                    fn ($q) => $q->where('status', 1)
                );


            /*
            |--------------------------------------------------------------------------
            | Search
            |--------------------------------------------------------------------------
            */

            if ($request->filled('search')) {

                $search = trim($request->search);

                $query->whereHas(
                    'student',
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


            $enrollments = $query
                ->orderByRaw(
                    "CASE
                        WHEN roll_no REGEXP '^[0-9]+$'
                        THEN CAST(roll_no AS UNSIGNED)
                        ELSE 999999
                    END"
                )
                ->orderBy('roll_no')
                ->get();
        }


        /*
        |--------------------------------------------------------------------------
        | Recent De-registrations
        |--------------------------------------------------------------------------
        */

        $deregistrations = StudentDeregistration::with([
                'student',
                'enrollment.academicYear',
                'enrollment.schoolClass',
                'enrollment.section',
                'creator',
            ])
            ->where('school_id', $schoolId)
            ->latest('deregistration_date')
            ->latest('id')
            ->paginate(20)
            ->withQueryString();


        return view(
            'students.management.deregistration',
            compact(
                'academicYears',
                'classes',
                'sections',
                'enrollments',
                'deregistrations'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | De-register Students
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
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

            'enrollment_ids' => [
                'required',
                'array',
                'min:1',
            ],

            'enrollment_ids.*' => [
                'required',
                'integer',
            ],

            'deregistration_date' => [
                'required',
                'date',
            ],

            'reason' => [
                'required',
                'string',
                'max:255',
            ],

            'remarks' => [
                'nullable',
                'string',
                'max:2000',
            ],

        ]);


        /*
        |--------------------------------------------------------------------------
        | Validate Section
        |--------------------------------------------------------------------------
        */

        $section = Section::where('school_id', $schoolId)
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
            ->where('status', 1)
            ->first();


        if (!$section) {

            throw ValidationException::withMessages([
                'section_id' =>
                    'Invalid section selected.',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Lock Selected Enrollments
        |--------------------------------------------------------------------------
        */

        DB::transaction(function () use (
            $validated,
            $schoolId
        ) {

            $enrollments = StudentEnrollment::with('student')
                ->where('school_id', $schoolId)
                ->whereIn(
                    'id',
                    $validated['enrollment_ids']
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
                ->where('is_current', true)
                ->where('enrollment_status', 'active')
                ->where('status', 1)
                ->lockForUpdate()
                ->get();


            if (
                $enrollments->count() !==
                count($validated['enrollment_ids'])
            ) {

                throw ValidationException::withMessages([
                    'enrollment_ids' =>
                        'One or more selected students are no longer eligible for de-registration.',
                ]);
            }


            foreach ($enrollments as $enrollment) {

                /*
                |--------------------------------------------------------------------------
                | Prevent Duplicate
                |--------------------------------------------------------------------------
                */

                $exists = StudentDeregistration::where(
                        'school_id',
                        $schoolId
                    )
                    ->where(
                        'student_enrollment_id',
                        $enrollment->id
                    )
                    ->exists();


                if ($exists) {

                    throw ValidationException::withMessages([
                        'enrollment_ids' =>
                            $enrollment->student->student_name
                            .
                            ' has already been de-registered.',
                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | History
                |--------------------------------------------------------------------------
                */

                StudentDeregistration::create([

                    'school_id' =>
                        $schoolId,

                    'student_id' =>
                        $enrollment->student_id,

                    'student_enrollment_id' =>
                        $enrollment->id,

                    'deregistration_date' =>
                        $validated[
                            'deregistration_date'
                        ],

                    'reason' =>
                        $validated['reason'],

                    'remarks' =>
                        $validated['remarks'] ?? null,

                    'created_by' =>
                        auth()->id(),

                ]);


                /*
                |--------------------------------------------------------------------------
                | Close Current Enrollment
                |--------------------------------------------------------------------------
                |
                | We do NOT delete Student or Enrollment.
                |--------------------------------------------------------------------------
                */

                $enrollment->update([

                    'is_current' =>
                        false,

                    'enrollment_status' =>
                        'inactive',

                ]);
            }

        });


        return redirect()
            ->route(
                'student-management.deregistration'
            )
            ->with(
                'success',
                count($validated['enrollment_ids'])
                .
                ' student(s) de-registered successfully.'
            );
    }
}