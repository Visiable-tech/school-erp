<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\StudentEnrollment;
use App\Models\TcReason;
use App\Models\TcRequest;
use App\Models\TransferCertificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TcRequestController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | List + Student Search
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

        $tcReasons = TcReason::where('school_id', $schoolId)
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
        | Students
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
        | Request Register
        |--------------------------------------------------------------------------
        */

        $requestQuery = TcRequest::with([
                'student',
                'enrollment.academicYear',
                'enrollment.schoolClass',
                'enrollment.section',
                'reason',
                'requestedBy',
                'reviewedBy',
                'transferCertificate',
            ])
            ->where('school_id', $schoolId);


        if ($request->filled('request_status')) {

            $requestQuery->where(
                'status',
                $request->request_status
            );
        }


        $tcRequests = $requestQuery
            ->latest('id')
            ->paginate(20)
            ->withQueryString();


        return view(
            'students.management.tc-requests',
            compact(
                'academicYears',
                'classes',
                'sections',
                'enrollments',
                'tcReasons',
                'tcRequests'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Create TC Request
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $schoolId = auth()->user()->school_id;


        $validated = $request->validate([

            'student_enrollment_id' => [
                'required',
                'integer',
            ],

            'request_date' => [
                'required',
                'date',
            ],

            'tc_reason_id' => [
                'required',

                Rule::exists(
                    'tc_reasons',
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

            'request_remarks' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);


        $enrollment = StudentEnrollment::with('student')
            ->where('school_id', $schoolId)
            ->where(
                'id',
                $validated['student_enrollment_id']
            )
            ->where('is_current', true)
            ->where('enrollment_status', 'active')
            ->where('status', 1)
            ->first();


        if (!$enrollment) {

            throw ValidationException::withMessages([

                'student_enrollment_id' =>
                    'Selected student does not have an active current enrollment.',

            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Existing Pending Request
        |--------------------------------------------------------------------------
        */

        $alreadyPending = TcRequest::where(
                'school_id',
                $schoolId
            )
            ->where(
                'student_id',
                $enrollment->student_id
            )
            ->where('status', 'pending')
            ->exists();


        if ($alreadyPending) {

            throw ValidationException::withMessages([

                'student_enrollment_id' =>
                    'This student already has a pending T.C. request.',

            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Existing TC
        |--------------------------------------------------------------------------
        */

        $existingTc = TransferCertificate::where(
                'school_id',
                $schoolId
            )
            ->where(
                'student_id',
                $enrollment->student_id
            )
            ->whereIn(
                'status',
                ['draft', 'issued']
            )
            ->exists();


        if ($existingTc) {

            throw ValidationException::withMessages([

                'student_enrollment_id' =>
                    'A Transfer Certificate already exists for this student.',

            ]);
        }


        TcRequest::create([

            'school_id' =>
                $schoolId,

            'student_id' =>
                $enrollment->student_id,

            'student_enrollment_id' =>
                $enrollment->id,

            'request_date' =>
                $validated['request_date'],

            'tc_reason_id' =>
                $validated['tc_reason_id'],

            'request_remarks' =>
                $validated['request_remarks']
                ?? null,

            'status' =>
                'pending',

            'requested_by' =>
                auth()->id(),

        ]);


        return redirect()
            ->route(
                'student-management.tc-requests'
            )
            ->with(
                'success',
                'T.C. request submitted successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Approve Request
    |--------------------------------------------------------------------------
    */

    public function approve(
        Request $request,
        TcRequest $tcRequest
    ) {

        $schoolId = auth()->user()->school_id;


        if (
            (int) $tcRequest->school_id
            !==
            (int) $schoolId
        ) {
            abort(403);
        }


        $validated = $request->validate([

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

            'issue_date' => [
                'required',
                'date',
            ],

            'review_remarks' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);


        DB::transaction(
            function () use (
                $tcRequest,
                $validated,
                $schoolId
            ) {

                $tcRequest = TcRequest::where(
                        'school_id',
                        $schoolId
                    )
                    ->whereKey($tcRequest->id)
                    ->lockForUpdate()
                    ->firstOrFail();


                if (
                    $tcRequest->status
                    !==
                    'pending'
                ) {

                    throw ValidationException::withMessages([

                        'request' =>
                            'This T.C. request has already been processed.',

                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | Enrollment Must Still Be Current
                |--------------------------------------------------------------------------
                */

                $enrollment = StudentEnrollment::where(
                        'school_id',
                        $schoolId
                    )
                    ->where(
                        'id',
                        $tcRequest->student_enrollment_id
                    )
                    ->where('is_current', true)
                    ->where(
                        'enrollment_status',
                        'active'
                    )
                    ->where('status', 1)
                    ->lockForUpdate()
                    ->first();


                if (!$enrollment) {

                    throw ValidationException::withMessages([

                        'request' =>
                            'The student no longer has the active enrollment used for this request.',

                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | Ensure No Existing TC
                |--------------------------------------------------------------------------
                */

                $existingTc =
                    TransferCertificate::where(
                        'school_id',
                        $schoolId
                    )
                    ->where(
                        'student_id',
                        $tcRequest->student_id
                    )
                    ->whereIn(
                        'status',
                        [
                            'draft',
                            'issued',
                        ]
                    )
                    ->exists();


                if ($existingTc) {

                    throw ValidationException::withMessages([

                        'request' =>
                            'A Transfer Certificate already exists for this student.',

                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | Create Draft TC
                |--------------------------------------------------------------------------
                */

                $certificate =
                    TransferCertificate::create([

                        'school_id' =>
                            $schoolId,

                        'is_manual' =>
                            false,

                        'student_id' =>
                            $tcRequest->student_id,

                        'student_enrollment_id' =>
                            $tcRequest
                                ->student_enrollment_id,

                        'tc_number' =>
                            $validated['tc_number'],

                        'application_date' =>
                            $tcRequest->request_date,

                        'issue_date' =>
                            $validated['issue_date'],

                        'tc_reason_id' =>
                            $tcRequest->tc_reason_id,

                        'remarks' =>
                            $tcRequest
                                ->request_remarks,

                        'status' =>
                            'draft',

                        'created_by' =>
                            auth()->id(),

                    ]);


                /*
                |--------------------------------------------------------------------------
                | Approve Request
                |--------------------------------------------------------------------------
                */

                $tcRequest->update([

                    'status' =>
                        'approved',

                    'reviewed_by' =>
                        auth()->id(),

                    'reviewed_at' =>
                        now(),

                    'review_remarks' =>
                        $validated[
                            'review_remarks'
                        ] ?? null,

                    'transfer_certificate_id' =>
                        $certificate->id,

                ]);
            }
        );


        return back()->with(
            'success',
            'T.C. request approved and Draft Transfer Certificate created successfully.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Reject Request
    |--------------------------------------------------------------------------
    */

    public function reject(
        Request $request,
        TcRequest $tcRequest
    ) {

        $schoolId =
            auth()->user()->school_id;


        if (
            (int) $tcRequest->school_id
            !==
            (int) $schoolId
        ) {
            abort(403);
        }


        if (
            $tcRequest->status
            !==
            'pending'
        ) {

            return back()->with(
                'error',
                'Only pending requests can be rejected.'
            );
        }


        $validated =
            $request->validate([

                'review_remarks' => [
                    'required',
                    'string',
                    'max:2000',
                ],

            ]);


        $tcRequest->update([

            'status' =>
                'rejected',

            'reviewed_by' =>
                auth()->id(),

            'reviewed_at' =>
                now(),

            'review_remarks' =>
                $validated[
                    'review_remarks'
                ],

        ]);


        return back()->with(
            'success',
            'T.C. request rejected successfully.'
        );
    }
}