<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\StudentProfileModifyRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StudentProfileModifyRequestController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Allowed Profile Fields
    |--------------------------------------------------------------------------
    |
    | IMPORTANT:
    | Only fields listed here can be modified through this workflow.
    |
    */

    private function allowedFields(): array
    {
        return [
            'student_name' => 'Student Name',
            'date_of_birth' => 'Date of Birth',
            'gender' => 'Gender',
            'blood_group' => 'Blood Group',
            'nationality' => 'Nationality',
            'religion' => 'Religion',
            'category' => 'Category',
            'mother_tongue' => 'Mother Tongue',

            'father_name' => 'Father Name',
            'father_mobile' => 'Father Mobile',
            'father_email' => 'Father Email',
            'father_occupation' => 'Father Occupation',

            'mother_name' => 'Mother Name',
            'mother_mobile' => 'Mother Mobile',
            'mother_email' => 'Mother Email',
            'mother_occupation' => 'Mother Occupation',

            'guardian_name' => 'Guardian Name',
            'guardian_relation' => 'Guardian Relation',
            'guardian_mobile' => 'Guardian Mobile',

            'present_address' => 'Present Address',
            'present_city' => 'Present City',
            'present_state' => 'Present State',
            'present_pin_code' => 'Present PIN Code',

            'permanent_address' => 'Permanent Address',
            'permanent_city' => 'Permanent City',
            'permanent_state' => 'Permanent State',
            'permanent_pin_code' => 'Permanent PIN Code',

            'previous_school' => 'Previous School',
            'previous_class' => 'Previous Class',
            'previous_board' => 'Previous Board',
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Index
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
                ->where('is_current', true)
                ->where(
                    'enrollment_status',
                    'active'
                )
                ->where('status', 1)
                ->whereHas(
                    'student',
                    fn ($query) =>
                        $query->where('status', 1)
                );


            if ($request->filled('search')) {

                $search = trim(
                    $request->search
                );

                $query->whereHas(
                    'student',
                    function ($query) use ($search) {

                        $query
                            ->where(
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
        | Requests
        |--------------------------------------------------------------------------
        */

        $modifyQuery =
            StudentProfileModifyRequest::with([
                'student',
                'requestedBy',
                'reviewedBy',
            ])
            ->where(
                'school_id',
                $schoolId
            );


        if (
            $request->filled(
                'request_status'
            )
        ) {

            $modifyQuery->where(
                'status',
                $request->request_status
            );
        }


        if (
            $request->filled(
                'request_search'
            )
        ) {

            $requestSearch =
                trim(
                    $request->request_search
                );

            $modifyQuery->whereHas(
                'student',
                function ($query) use (
                    $requestSearch
                ) {

                    $query
                        ->where(
                            'student_name',
                            'like',
                            "%{$requestSearch}%"
                        )
                        ->orWhere(
                            'admission_no',
                            'like',
                            "%{$requestSearch}%"
                        );
                }
            );
        }


        $modifyRequests =
            $modifyQuery
                ->latest('id')
                ->paginate(20)
                ->withQueryString();


        $allowedFields =
            $this->allowedFields();


        return view(
            'students.management.profile-modify-requests',
            compact(
                'academicYears',
                'classes',
                'sections',
                'enrollments',
                'modifyRequests',
                'allowedFields'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Store Request
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $schoolId =
            auth()->user()->school_id;


        $validated =
            $request->validate([

                'student_id' => [
                    'required',
                    'integer',
                ],

                'request_date' => [
                    'required',
                    'date',
                ],

                'changes' => [
                    'required',
                    'array',
                ],

                'request_remarks' => [
                    'nullable',
                    'string',
                    'max:2000',
                ],

            ]);


        /*
        |--------------------------------------------------------------------------
        | Student Validation
        |--------------------------------------------------------------------------
        */

        $student = Student::where(
                'school_id',
                $schoolId
            )
            ->where(
                'id',
                $validated['student_id']
            )
            ->where('status', 1)
            ->first();


        if (!$student) {

            throw ValidationException::withMessages([

                'student_id' =>
                    'Invalid student selected.',

            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Must Have Current Enrollment
        |--------------------------------------------------------------------------
        */

        $currentEnrollment =
            StudentEnrollment::where(
                'school_id',
                $schoolId
            )
            ->where(
                'student_id',
                $student->id
            )
            ->where(
                'is_current',
                true
            )
            ->where(
                'enrollment_status',
                'active'
            )
            ->where('status', 1)
            ->exists();


        if (!$currentEnrollment) {

            throw ValidationException::withMessages([

                'student_id' =>
                    'Student does not have an active current enrollment.',

            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Prevent Multiple Pending Requests
        |--------------------------------------------------------------------------
        */

        $pendingExists =
            StudentProfileModifyRequest::where(
                'school_id',
                $schoolId
            )
            ->where(
                'student_id',
                $student->id
            )
            ->where(
                'status',
                'pending'
            )
            ->exists();


        if ($pendingExists) {

            throw ValidationException::withMessages([

                'student_id' =>
                    'This student already has a pending profile modification request.',

            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Prepare Changed Fields Only
        |--------------------------------------------------------------------------
        */

        $allowedFields =
            $this->allowedFields();

        $requestedChanges = [];


        foreach (
            $validated['changes']
            as $field => $newValue
        ) {

            if (
                !array_key_exists(
                    $field,
                    $allowedFields
                )
            ) {
                continue;
            }


            if (is_string($newValue)) {
                $newValue = trim($newValue);
            }


            $oldValue =
                $student->{$field};


            if (
                $field === 'date_of_birth'
                &&
                $oldValue
            ) {

                $oldValue =
                    $student
                        ->date_of_birth
                        ?->format('Y-m-d');
            }


            /*
             * Blank means "no requested change".
             *
             * This prevents accidental data clearing.
             */

            if (
                $newValue === null
                ||
                $newValue === ''
            ) {
                continue;
            }


            if (
                (string) $oldValue
                ===
                (string) $newValue
            ) {
                continue;
            }


            $requestedChanges[$field] = [

                'old' =>
                    $oldValue,

                'new' =>
                    $newValue,

            ];
        }


        if (empty($requestedChanges)) {

            throw ValidationException::withMessages([

                'changes' =>
                    'Please enter at least one changed profile value.',

            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Save Request
        |--------------------------------------------------------------------------
        */

        StudentProfileModifyRequest::create([

            'school_id' =>
                $schoolId,

            'student_id' =>
                $student->id,

            'request_date' =>
                $validated[
                    'request_date'
                ],

            'requested_changes' =>
                $requestedChanges,

            'request_remarks' =>
                $validated[
                    'request_remarks'
                ] ?? null,

            'status' =>
                'pending',

            'requested_by' =>
                auth()->id(),

        ]);


        return redirect()
            ->route(
                'student-management.profile-modify-requests'
            )
            ->with(
                'success',
                'Profile modification request submitted successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Approve
    |--------------------------------------------------------------------------
    */

    public function approve(
        Request $request,
        StudentProfileModifyRequest $modifyRequest
    ) {

        $schoolId =
            auth()->user()->school_id;


        if (
            (int) $modifyRequest->school_id
            !==
            (int) $schoolId
        ) {
            abort(403);
        }


        $validated =
            $request->validate([

                'review_remarks' => [
                    'nullable',
                    'string',
                    'max:2000',
                ],

            ]);


        DB::transaction(
            function () use (
                $modifyRequest,
                $validated,
                $schoolId
            ) {

                $modifyRequest =
                    StudentProfileModifyRequest::where(
                        'school_id',
                        $schoolId
                    )
                    ->whereKey(
                        $modifyRequest->id
                    )
                    ->lockForUpdate()
                    ->firstOrFail();


                if (
                    $modifyRequest->status
                    !==
                    'pending'
                ) {

                    throw ValidationException::withMessages([

                        'request' =>
                            'This modification request has already been processed.',

                    ]);
                }


                $student =
                    Student::where(
                        'school_id',
                        $schoolId
                    )
                    ->whereKey(
                        $modifyRequest->student_id
                    )
                    ->lockForUpdate()
                    ->firstOrFail();


                $allowedFields =
                    $this->allowedFields();

                $updates = [];


                foreach (
                    $modifyRequest->requested_changes
                    as $field => $change
                ) {

                    if (
                        !array_key_exists(
                            $field,
                            $allowedFields
                        )
                    ) {
                        continue;
                    }


                    if (
                        !array_key_exists(
                            'new',
                            $change
                        )
                    ) {
                        continue;
                    }


                    $updates[$field] =
                        $change['new'];
                }


                if (empty($updates)) {

                    throw ValidationException::withMessages([

                        'request' =>
                            'No valid profile changes were found.',

                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | Update Actual Student
                |--------------------------------------------------------------------------
                */

                $student->update(
                    $updates
                );


                /*
                |--------------------------------------------------------------------------
                | Approve Request
                |--------------------------------------------------------------------------
                */

                $modifyRequest->update([

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

                ]);

            }
        );


        return back()->with(
            'success',
            'Profile modification request approved and student profile updated.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Reject
    |--------------------------------------------------------------------------
    */

    public function reject(
        Request $request,
        StudentProfileModifyRequest $modifyRequest
    ) {

        $schoolId =
            auth()->user()->school_id;


        if (
            (int) $modifyRequest->school_id
            !==
            (int) $schoolId
        ) {
            abort(403);
        }


        if (
            $modifyRequest->status
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


        $modifyRequest->update([

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
            'Profile modification request rejected.'
        );
    }
}