<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\StudentEnrollment;
use App\Models\StudentAttendance;
use App\Models\StudentAttendanceSession;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StudentAttendanceController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Mark Attendance Page
    |--------------------------------------------------------------------------
    */

    public function create()
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

        $currentAcademicYear = AcademicYear::where('school_id', $schoolId)
            ->where('is_current', 1)
            ->where('status', 1)
            ->first();

        return view(
            'student-attendance.create',
            compact(
                'academicYears',
                'classes',
                'currentAcademicYear'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Sections AJAX
    |--------------------------------------------------------------------------
    */

    public function getSections(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        $request->validate([
            'academic_year_id' => 'required|integer',
            'school_class_id' => 'required|integer',
        ]);

        $sections = Section::where('school_id', $schoolId)
            ->where('academic_year_id', $request->academic_year_id)
            ->where('school_class_id', $request->school_class_id)
            ->where('status', 1)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get([
                'id',
                'name'
            ]);

        return response()->json($sections);
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
            'academic_year_id' => 'required|integer',
            'school_class_id' => 'required|integer',
            'section_id' => 'required|integer',
            'attendance_date' => 'required|date',
        ]);


        /*
        |--------------------------------------------------------------------------
        | Verify Section
        |--------------------------------------------------------------------------
        */

        $section = Section::where('school_id', $schoolId)
            ->where('id', $request->section_id)
            ->where('academic_year_id', $request->academic_year_id)
            ->where('school_class_id', $request->school_class_id)
            ->where('status', 1)
            ->firstOrFail();


        /*
        |--------------------------------------------------------------------------
        | Existing Attendance Session
        |--------------------------------------------------------------------------
        */

        $attendanceSession = StudentAttendanceSession::where(
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
            ->whereDate(
                'attendance_date',
                $request->attendance_date
            )
            ->first();


        /*
        |--------------------------------------------------------------------------
        | Students
        |--------------------------------------------------------------------------
        */

        $enrollments = StudentEnrollment::with('student')
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
            ->whereHas('student', function ($query) {
                $query->where('status', 1);
            })
            ->orderByRaw(
                'CASE WHEN roll_no IS NULL THEN 1 ELSE 0 END'
            )
            ->orderBy('roll_no')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Existing Attendance
        |--------------------------------------------------------------------------
        */

        $existingAttendance = collect();

        if ($attendanceSession) {

            $existingAttendance = StudentAttendance::where(
                    'attendance_session_id',
                    $attendanceSession->id
                )
                ->get()
                ->keyBy('student_id');
        }


        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */

        $students = $enrollments->map(
            function ($enrollment) use ($existingAttendance) {

                $attendance = $existingAttendance->get(
                    $enrollment->student_id
                );

                return [

                    'student_id' =>
                        $enrollment->student_id,

                    'student_enrollment_id' =>
                        $enrollment->id,

                    'admission_no' =>
                        $enrollment->student->admission_no,

                    'student_name' =>
                        $enrollment->student->student_name,

                    'roll_no' =>
                        $enrollment->roll_no,

                    'attendance_status' =>
                        $attendance
                            ? $attendance->attendance_status
                            : 'present',

                    'remarks' =>
                        $attendance
                            ? $attendance->remarks
                            : '',
                ];
            }
        );


        return response()->json([

            'session_id' =>
                $attendanceSession?->id,

            'already_marked' =>
                (bool) $attendanceSession,

            'session_remarks' =>
                $attendanceSession?->remarks ?? '',

            'students' =>
                $students,

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Save Attendance
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $schoolId = auth()->user()->school_id;


        $validated = $request->validate([

            'academic_year_id' =>
                'required|integer',

            'school_class_id' =>
                'required|integer',

            'section_id' =>
                'required|integer',

            'attendance_date' =>
                'required|date',

            'session_remarks' =>
                'nullable|string|max:1000',

            'students' =>
                'required|array|min:1',

            'students.*.student_id' =>
                'required|integer',

            'students.*.student_enrollment_id' =>
                'required|integer',

            'students.*.attendance_status' => [
                'required',
                Rule::in([
                    'present',
                    'absent',
                    'late',
                    'leave',
                    'half_day',
                ]),
            ],

            'students.*.remarks' =>
                'nullable|string|max:500',

        ]);


        /*
        |--------------------------------------------------------------------------
        | Verify Section
        |--------------------------------------------------------------------------
        */

        Section::where('school_id', $schoolId)
            ->where('id', $validated['section_id'])
            ->where(
                'academic_year_id',
                $validated['academic_year_id']
            )
            ->where(
                'school_class_id',
                $validated['school_class_id']
            )
            ->where('status', 1)
            ->firstOrFail();


        DB::transaction(function () use (
            $validated,
            $schoolId
        ) {


            /*
            |--------------------------------------------------------------------------
            | Attendance Session
            |--------------------------------------------------------------------------
            */

            $session = StudentAttendanceSession::updateOrCreate(

                [
                    'school_id' =>
                        $schoolId,

                    'academic_year_id' =>
                        $validated['academic_year_id'],

                    'school_class_id' =>
                        $validated['school_class_id'],

                    'section_id' =>
                        $validated['section_id'],

                    'attendance_date' =>
                        $validated['attendance_date'],
                ],

                [
                    'remarks' =>
                        $validated['session_remarks'] ?? null,

                    'marked_by' =>
                        auth()->id(),

                    'status' =>
                        true,
                ]
            );


            /*
            |--------------------------------------------------------------------------
            | Save Every Student
            |--------------------------------------------------------------------------
            */

            foreach ($validated['students'] as $row) {

                /*
                 * IMPORTANT:
                 * Verify enrollment belongs to selected
                 * year/class/section/student.
                 */

                $enrollment = StudentEnrollment::where(
                        'school_id',
                        $schoolId
                    )
                    ->where(
                        'id',
                        $row['student_enrollment_id']
                    )
                    ->where(
                        'student_id',
                        $row['student_id']
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
                        'status',
                        1
                    )
                    ->firstOrFail();


                StudentAttendance::updateOrCreate(

                    [
                        'attendance_session_id' =>
                            $session->id,

                        'student_id' =>
                            $enrollment->student_id,
                    ],

                    [
                        'school_id' =>
                            $schoolId,

                        'student_enrollment_id' =>
                            $enrollment->id,

                        'attendance_status' =>
                            $row['attendance_status'],

                        'remarks' =>
                            $row['remarks'] ?? null,
                    ]
                );
            }

        });


        return redirect()
            ->route('student-attendance.create')
            ->with(
                'success',
                'Student attendance saved successfully.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Attendance Register
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

        $currentAcademicYear = AcademicYear::where('school_id', $schoolId)
            ->where('is_current', 1)
            ->where('status', 1)
            ->first();

        $attendanceSession = null;
        $attendances = collect();

        $summary = [
            'total' => 0,
            'present' => 0,
            'absent' => 0,
            'late' => 0,
            'leave' => 0,
            'half_day' => 0,
        ];


        /*
        |--------------------------------------------------------------------------
        | Search Attendance
        |--------------------------------------------------------------------------
        */

        if (
            $request->filled('academic_year_id') &&
            $request->filled('school_class_id') &&
            $request->filled('section_id') &&
            $request->filled('attendance_date')
        ) {

            /*
            |--------------------------------------------------------------------------
            | Verify selected section belongs to school/year/class
            |--------------------------------------------------------------------------
            */

            Section::where('school_id', $schoolId)
                ->where('id', $request->section_id)
                ->where(
                    'academic_year_id',
                    $request->academic_year_id
                )
                ->where(
                    'school_class_id',
                    $request->school_class_id
                )
                ->where('status', 1)
                ->firstOrFail();


            /*
            |--------------------------------------------------------------------------
            | Attendance Session
            |--------------------------------------------------------------------------
            */

            $attendanceSession = StudentAttendanceSession::with([
                    'academicYear',
                    'schoolClass',
                    'section',
                    'marker',
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
                ->whereDate(
                    'attendance_date',
                    $request->attendance_date
                )
                ->first();


            if ($attendanceSession) {

                /*
                |--------------------------------------------------------------------------
                | Attendance Rows
                |--------------------------------------------------------------------------
                */

                $attendances = StudentAttendance::with([
                        'student',
                        'enrollment',
                    ])
                    ->where(
                        'attendance_session_id',
                        $attendanceSession->id
                    )
                    ->get()
                    ->sortBy(function ($attendance) {

                        /*
                        * Students with roll numbers first.
                        */

                        return sprintf(
                            '%010d-%s',
                            $attendance->enrollment?->roll_no ?? 999999999,
                            $attendance->student?->student_name ?? ''
                        );
                    })
                    ->values();


                /*
                |--------------------------------------------------------------------------
                | Summary
                |--------------------------------------------------------------------------
                */

                $summary['total'] =
                    $attendances->count();

                $summary['present'] =
                    $attendances
                        ->where('attendance_status', 'present')
                        ->count();

                $summary['absent'] =
                    $attendances
                        ->where('attendance_status', 'absent')
                        ->count();

                $summary['late'] =
                    $attendances
                        ->where('attendance_status', 'late')
                        ->count();

                $summary['leave'] =
                    $attendances
                        ->where('attendance_status', 'leave')
                        ->count();

                $summary['half_day'] =
                    $attendances
                        ->where('attendance_status', 'half_day')
                        ->count();
            }
        }


        return view(
            'student-attendance.index',
            compact(
                'academicYears',
                'classes',
                'currentAcademicYear',
                'attendanceSession',
                'attendances',
                'summary'
            )
        );
    }

}