<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\StudentEnrollment;
use App\Models\TransferCertificate;
use App\Models\TcReason;
use App\Models\TcRemarkOption;
use App\Models\TcLastResultOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TransferCertificateController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | TC Page
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
        | TC Masters
        |--------------------------------------------------------------------------
        */

        $tcReasons = TcReason::where(
                'school_id',
                $schoolId
            )
            ->where('status', 1)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $tcRemarks = TcRemarkOption::where(
                'school_id',
                $schoolId
            )
            ->where('status', 1)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $tcLastResults = TcLastResultOption::where(
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


            if ($request->filled('search')) {

                $search =
                    trim($request->search);

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
        | Existing TCs
        |--------------------------------------------------------------------------
        */

        $certificates =
            TransferCertificate::with([
                'student',
                'enrollment.academicYear',
                'enrollment.schoolClass',
                'enrollment.section',
                'reason',
            ])
            ->where(
                'school_id',
                $schoolId
            )
            ->latest('id')
            ->paginate(20)
            ->withQueryString();


        return view(
            'students.management.transfer-certificate',
            compact(
                'academicYears',
                'classes',
                'sections',
                'enrollments',
                'tcReasons',
                'tcRemarks',
                'tcLastResults',
                'certificates'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Store TC
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $schoolId =
            auth()->user()->school_id;


        $validated =
            $request->validate([

                'student_enrollment_id' => [
                    'required',
                    'integer',
                ],

                'tc_number' => [
                    'required',
                    'string',
                    'max:100',

                    Rule::unique(
                        'transfer_certificates',
                        'tc_number'
                    )->where(
                        fn ($query) =>
                            $query->where(
                                'school_id',
                                $schoolId
                            )
                    ),
                ],

                'application_date' => [
                    'nullable',
                    'date',
                ],

                'issue_date' => [
                    'required',
                    'date',
                ],

                'leaving_date' => [
                    'nullable',
                    'date',
                ],

                'tc_reason_id' => [
                    'required',
                    'integer',
                ],

                'tc_remark_option_id' => [
                    'nullable',
                    'integer',
                ],

                'tc_last_result_option_id' => [
                    'nullable',
                    'integer',
                ],

                'conduct' => [
                    'nullable',
                    'string',
                    'max:150',
                ],

                'next_school' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'next_class' => [
                    'nullable',
                    'string',
                    'max:100',
                ],

                'remarks' => [
                    'nullable',
                    'string',
                    'max:2000',
                ],

            ]);


        /*
        |--------------------------------------------------------------------------
        | Enrollment
        |--------------------------------------------------------------------------
        */

        $enrollment =
            StudentEnrollment::with('student')
            ->where(
                'school_id',
                $schoolId
            )
            ->where(
                'id',
                $validated[
                    'student_enrollment_id'
                ]
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
            ->first();


        if (!$enrollment) {

            throw ValidationException::withMessages([

                'student_enrollment_id' =>
                    'The selected student does not have an active current enrollment.',

            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Validate TC Reason
        |--------------------------------------------------------------------------
        */

        $reasonExists =
            TcReason::where(
                'school_id',
                $schoolId
            )
            ->where(
                'id',
                $validated['tc_reason_id']
            )
            ->where('status', 1)
            ->exists();


        if (!$reasonExists) {

            throw ValidationException::withMessages([

                'tc_reason_id' =>
                    'Invalid T.C. reason selected.',

            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Validate Remark
        |--------------------------------------------------------------------------
        */

        if (
            !empty(
                $validated[
                    'tc_remark_option_id'
                ]
            )
        ) {

            $exists =
                TcRemarkOption::where(
                    'school_id',
                    $schoolId
                )
                ->where(
                    'id',
                    $validated[
                        'tc_remark_option_id'
                    ]
                )
                ->where('status', 1)
                ->exists();


            if (!$exists) {

                throw ValidationException::withMessages([

                    'tc_remark_option_id' =>
                        'Invalid T.C. remark selected.',

                ]);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Validate Last Result
        |--------------------------------------------------------------------------
        */

        if (
            !empty(
                $validated[
                    'tc_last_result_option_id'
                ]
            )
        ) {

            $exists =
                TcLastResultOption::where(
                    'school_id',
                    $schoolId
                )
                ->where(
                    'id',
                    $validated[
                        'tc_last_result_option_id'
                    ]
                )
                ->where('status', 1)
                ->exists();


            if (!$exists) {

                throw ValidationException::withMessages([

                    'tc_last_result_option_id' =>
                        'Invalid T.C. last result selected.',

                ]);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Prevent Multiple Active TCs
        |--------------------------------------------------------------------------
        */

        $existing =
            TransferCertificate::where(
                'school_id',
                $schoolId
            )
            ->where(
                'student_id',
                $enrollment->student_id
            )
            ->whereIn(
                'status',
                [
                    'draft',
                    'issued',
                ]
            )
            ->exists();


        if ($existing) {

            throw ValidationException::withMessages([

                'student_enrollment_id' =>
                    'A Transfer Certificate already exists for this student.',

            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Save Draft
        |--------------------------------------------------------------------------
        */

        $certificate =
            TransferCertificate::create([

                'school_id' =>
                    $schoolId,

                'student_id' =>
                    $enrollment->student_id,

                'student_enrollment_id' =>
                    $enrollment->id,

                'tc_number' =>
                    $validated['tc_number'],

                'application_date' =>
                    $validated[
                        'application_date'
                    ] ?? null,

                'issue_date' =>
                    $validated['issue_date'],

                'leaving_date' =>
                    $validated[
                        'leaving_date'
                    ] ?? null,

                'tc_reason_id' =>
                    $validated['tc_reason_id'],

                'tc_remark_option_id' =>
                    $validated[
                        'tc_remark_option_id'
                    ] ?? null,

                'tc_last_result_option_id' =>
                    $validated[
                        'tc_last_result_option_id'
                    ] ?? null,

                'conduct' =>
                    $validated['conduct'] ?? null,

                'next_school' =>
                    $validated[
                        'next_school'
                    ] ?? null,

                'next_class' =>
                    $validated[
                        'next_class'
                    ] ?? null,

                'remarks' =>
                    $validated['remarks'] ?? null,

                'fees_cleared' =>
                    $request->boolean(
                        'fees_cleared'
                    ),

                'library_cleared' =>
                    $request->boolean(
                        'library_cleared'
                    ),

                'transport_cleared' =>
                    $request->boolean(
                        'transport_cleared'
                    ),

                'status' =>
                    'draft',

                'created_by' =>
                    auth()->id(),

            ]);


        return redirect()
            ->route(
                'student-management.transfer-certificate'
            )
            ->with(
                'success',
                'Transfer Certificate '
                .
                $certificate->tc_number
                .
                ' created successfully as Draft.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Issue TC
    |--------------------------------------------------------------------------
    */

    public function issue(
        TransferCertificate $certificate
    ) {

        $schoolId =
            auth()->user()->school_id;


        if (
            (int) $certificate->school_id
            !==
            (int) $schoolId
        ) {

            abort(403);
        }


        if (
            $certificate->status
            !==
            'draft'
        ) {

            return back()->with(
                'error',
                'Only a Draft Transfer Certificate can be issued.'
            );
        }


        DB::transaction(
            function () use (
                $certificate,
                $schoolId
            ) {

                $certificate =
                    TransferCertificate::where(
                        'school_id',
                        $schoolId
                    )
                    ->whereKey(
                        $certificate->id
                    )
                    ->lockForUpdate()
                    ->firstOrFail();


                if (
                    $certificate->status
                    !==
                    'draft'
                ) {

                    throw ValidationException::withMessages([

                        'tc' =>
                            'Transfer Certificate has already been processed.',

                    ]);
                }


                $enrollment =
                    StudentEnrollment::where(
                        'school_id',
                        $schoolId
                    )
                    ->where(
                        'id',
                        $certificate
                            ->student_enrollment_id
                    )
                    ->lockForUpdate()
                    ->firstOrFail();


                /*
                |--------------------------------------------------------------------------
                | Mark TC Issued
                |--------------------------------------------------------------------------
                */

                $certificate->update([

                    'status' =>
                        'issued',

                    'issued_by' =>
                        auth()->id(),

                    'issued_at' =>
                        now(),

                ]);


                /*
                |--------------------------------------------------------------------------
                | Close Current Enrollment
                |--------------------------------------------------------------------------
                */

                if (
                    $enrollment->is_current
                ) {

                    $enrollment->update([

                        'is_current' =>
                            false,

                        'enrollment_status' =>
                            'inactive',

                    ]);
                }

            }
        );


        return back()->with(
            'success',
            'Transfer Certificate issued successfully.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Cancel Draft TC
    |--------------------------------------------------------------------------
    */

    public function cancel(
        Request $request,
        TransferCertificate $certificate
    ) {

        $schoolId =
            auth()->user()->school_id;


        if (
            (int) $certificate->school_id
            !==
            (int) $schoolId
        ) {

            abort(403);
        }


        if (
            $certificate->status
            !==
            'draft'
        ) {

            return back()->with(
                'error',
                'Only a Draft Transfer Certificate can be cancelled.'
            );
        }


        $validated =
            $request->validate([

                'cancellation_reason' => [
                    'required',
                    'string',
                    'max:1000',
                ],

            ]);


        $certificate->update([

            'status' =>
                'cancelled',

            'cancelled_by' =>
                auth()->id(),

            'cancelled_at' =>
                now(),

            'cancellation_reason' =>
                $validated[
                    'cancellation_reason'
                ],

        ]);


        return back()->with(
            'success',
            'Transfer Certificate cancelled successfully.'
        );
    }
}