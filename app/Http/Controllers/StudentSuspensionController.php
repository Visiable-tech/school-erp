<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\StudentEnrollment;
use App\Models\StudentSuspension;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StudentSuspensionController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Suspension Page
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
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

        $sections = collect();

        $enrollments = collect();

        /*
        |--------------------------------------------------------------------------
        | Sections
        |--------------------------------------------------------------------------
        */

        if (
            $request->filled('academic_year_id')
            &&
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
                ->where('status', 1)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();
        }


        /*
        |--------------------------------------------------------------------------
        | Students
        |--------------------------------------------------------------------------
        */

        if (
            $request->filled('academic_year_id')
            &&
            $request->filled('school_class_id')
            &&
            $request->filled('section_id')
        ) {

            $query = StudentEnrollment::with([
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
                    'enrollment_status',
                    'active'
                )
                ->where(
                    'status',
                    1
                )
                ->whereHas(
                    'student',
                    fn ($q) =>
                        $q->where('status', 1)
                );


            /*
            |--------------------------------------------------------------------------
            | Search
            |--------------------------------------------------------------------------
            */

            if ($request->filled('search')) {

                $search = trim(
                    $request->search
                );

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
        | Active Suspensions
        |--------------------------------------------------------------------------
        */

        $activeSuspensions =
            StudentSuspension::with([
                'student',
                'enrollment.schoolClass',
                'enrollment.section',
            ])
            ->where(
                'school_id',
                $schoolId
            )
            ->where(
                'status',
                'active'
            )
            ->latest('suspension_from')
            ->get();


        return view(
            'students.management.suspension',
            compact(
                'academicYears',
                'classes',
                'sections',
                'enrollments',
                'activeSuspensions'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Suspend Students
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

            'suspension_from' => [
                'required',
                'date',
            ],

            'suspension_to' => [
                'nullable',
                'date',
                'after_or_equal:suspension_from',
            ],

            'reason' => [
                'required',
                'string',
                'max:255',
            ],

            'remarks' => [
                'nullable',
                'string',
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
        | Get Valid Enrollments
        |--------------------------------------------------------------------------
        */

        $enrollments =
            StudentEnrollment::with('student')
            ->where(
                'school_id',
                $schoolId
            )
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
            ->where(
                'is_current',
                true
            )
            ->where(
                'enrollment_status',
                'active'
            )
            ->where(
                'status',
                1
            )
            ->get();


        if (
            $enrollments->count()
            !==
            count($validated['enrollment_ids'])
        ) {

            throw ValidationException::withMessages([
                'enrollment_ids' =>
                    'One or more selected students are invalid.',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Save Suspensions
        |--------------------------------------------------------------------------
        */

        DB::transaction(
            function () use (
                $enrollments,
                $validated,
                $schoolId
            ) {

                foreach (
                    $enrollments as $enrollment
                ) {

                    /*
                    |--------------------------------------------------------------------------
                    | Prevent Duplicate Active Suspension
                    |--------------------------------------------------------------------------
                    */

                    $alreadySuspended =
                        StudentSuspension::where(
                            'school_id',
                            $schoolId
                        )
                        ->where(
                            'student_id',
                            $enrollment->student_id
                        )
                        ->where(
                            'status',
                            'active'
                        )
                        ->exists();


                    if ($alreadySuspended) {

                        throw ValidationException::withMessages([

                            'enrollment_ids' =>
                                $enrollment
                                    ->student
                                    ->student_name
                                .
                                ' already has an active suspension.',

                        ]);
                    }


                    StudentSuspension::create([

                        'school_id' =>
                            $schoolId,

                        'student_id' =>
                            $enrollment->student_id,

                        'student_enrollment_id' =>
                            $enrollment->id,

                        'suspension_from' =>
                            $validated[
                                'suspension_from'
                            ],

                        'suspension_to' =>
                            $validated[
                                'suspension_to'
                            ] ?? null,

                        'reason' =>
                            $validated['reason'],

                        'remarks' =>
                            $validated[
                                'remarks'
                            ] ?? null,

                        'status' =>
                            'active',

                        'created_by' =>
                            auth()->id(),

                    ]);
                }
            }
        );


        return redirect()
            ->route(
                'student-management.suspension',
                [
                    'academic_year_id' =>
                        $validated[
                            'academic_year_id'
                        ],

                    'school_class_id' =>
                        $validated[
                            'school_class_id'
                        ],

                    'section_id' =>
                        $validated[
                            'section_id'
                        ],
                ]
            )
            ->with(
                'success',
                $enrollments->count()
                .
                ' student(s) suspended successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Revoke Suspension
    |--------------------------------------------------------------------------
    */

    public function revoke(
        Request $request,
        StudentSuspension $suspension
    ) {

        $schoolId =
            auth()->user()->school_id;


        if (
            (int) $suspension->school_id
            !==
            (int) $schoolId
        ) {

            abort(403);
        }


        if (
            $suspension->status
            !==
            'active'
        ) {

            return back()->with(
                'error',
                'This suspension is no longer active.'
            );
        }


        $validated =
            $request->validate([

                'revocation_remarks' => [
                    'nullable',
                    'string',
                    'max:1000',
                ],

            ]);


        $suspension->update([

            'status' =>
                'revoked',

            'revoked_by' =>
                auth()->id(),

            'revoked_at' =>
                now(),

            'revocation_remarks' =>
                $validated[
                    'revocation_remarks'
                ] ?? null,

        ]);


        return back()->with(
            'success',
            'Suspension revoked successfully.'
        );
    }
}