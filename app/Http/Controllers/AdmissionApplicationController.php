<?php

namespace App\Http\Controllers;

use App\Models\AdmissionApplication;
use App\Models\AdmissionEnquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AdmissionApplicationController extends Controller
{
    public function index(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        $query = AdmissionApplication::with([
                'enquiry',
                'admissionSession',
                'academicYear',
                'schoolClass',
            ])
            ->where('school_id', $schoolId);


        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->where(function ($q) use ($search) {

                $q->where(
                    'application_no',
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


        if ($request->filled('application_status')) {

            $query->where(
                'application_status',
                $request->application_status
            );
        }


        $applications = $query
            ->orderByDesc('application_date')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();


        return view(
            'admission-applications.index',
            compact('applications')
        );
    }


    public function create(
        AdmissionEnquiry $admissionEnquiry
    ) {
        $this->checkEnquiryAccess(
            $admissionEnquiry
        );


        /*
         * Prevent duplicate application
         */
        if ($admissionEnquiry->application()->exists()) {

            return redirect()
                ->route(
                    'admission-applications.edit',
                    $admissionEnquiry->application
                )
                ->with(
                    'error',
                    'An application already exists for this enquiry.'
                );
        }


        /*
         * Class must be selected before application.
         */
        if (!$admissionEnquiry->school_class_id) {

            return redirect()
                ->route(
                    'admission-enquiries.edit',
                    $admissionEnquiry
                )
                ->with(
                    'error',
                    'Please select the admission class before creating an application.'
                );
        }


        return view(
            'admission-applications.create',
            compact('admissionEnquiry')
        );
    }


    public function store(
        Request $request,
        AdmissionEnquiry $admissionEnquiry
    ) {
        $this->checkEnquiryAccess(
            $admissionEnquiry
        );


        if ($admissionEnquiry->application()->exists()) {

            return back()->with(
                'error',
                'Application already exists for this enquiry.'
            );
        }


        if (!$admissionEnquiry->school_class_id) {

            return back()->with(
                'error',
                'Admission class is required.'
            );
        }


        $validated = $this->validateApplication(
            $request
        );


        $application = DB::transaction(function () use (
            $validated,
            $request,
            $admissionEnquiry
        ) {

            /*
            |--------------------------------------------------------------------------
            | Application Number
            |--------------------------------------------------------------------------
            */

            $lastApplication =
                AdmissionApplication::where(
                    'school_id',
                    $admissionEnquiry->school_id
                )
                ->lockForUpdate()
                ->orderByDesc('id')
                ->first();


            $nextNumber = $lastApplication
                ? $lastApplication->id + 1
                : 1;


            $applicationNo =
                'APP-' .
                now()->format('Y') .
                '-' .
                str_pad(
                    $nextNumber,
                    5,
                    '0',
                    STR_PAD_LEFT
                );


            /*
            |--------------------------------------------------------------------------
            | Create Application
            |--------------------------------------------------------------------------
            */

            $application =
                AdmissionApplication::create([

                    'school_id' =>
                        $admissionEnquiry->school_id,

                    'admission_enquiry_id' =>
                        $admissionEnquiry->id,

                    'admission_session_id' =>
                        $admissionEnquiry->admission_session_id,

                    'academic_year_id' =>
                        $admissionEnquiry->academic_year_id,

                    'school_class_id' =>
                        $admissionEnquiry->school_class_id,

                    'application_no' =>
                        $applicationNo,

                    'application_date' =>
                        $validated['application_date'],

                    /*
                     * Student
                     */
                    'student_name' =>
                        $validated['student_name'],

                    'date_of_birth' =>
                        $validated['date_of_birth'] ?? null,

                    'gender' =>
                        $validated['gender'] ?? null,

                    'blood_group' =>
                        $validated['blood_group'] ?? null,

                    'nationality' =>
                        $validated['nationality'] ?? 'Indian',

                    'religion' =>
                        $validated['religion'] ?? null,

                    'category' =>
                        $validated['category'] ?? null,

                    'mother_tongue' =>
                        $validated['mother_tongue'] ?? null,

                    /*
                     * Father
                     */
                    'father_name' =>
                        $validated['father_name'] ?? null,

                    'father_mobile' =>
                        $validated['father_mobile'] ?? null,

                    'father_email' =>
                        $validated['father_email'] ?? null,

                    'father_occupation' =>
                        $validated['father_occupation'] ?? null,

                    /*
                     * Mother
                     */
                    'mother_name' =>
                        $validated['mother_name'] ?? null,

                    'mother_mobile' =>
                        $validated['mother_mobile'] ?? null,

                    'mother_email' =>
                        $validated['mother_email'] ?? null,

                    'mother_occupation' =>
                        $validated['mother_occupation'] ?? null,

                    /*
                     * Guardian
                     */
                    'guardian_name' =>
                        $validated['guardian_name'] ?? null,

                    'guardian_relation' =>
                        $validated['guardian_relation'] ?? null,

                    'guardian_mobile' =>
                        $validated['guardian_mobile'] ?? null,

                    /*
                     * Present Address
                     */
                    'present_address' =>
                        $validated['present_address'] ?? null,

                    'present_city' =>
                        $validated['present_city'] ?? null,

                    'present_state' =>
                        $validated['present_state'] ?? null,

                    'present_pin_code' =>
                        $validated['present_pin_code'] ?? null,

                    /*
                     * Permanent Address
                     */
                    'permanent_address' =>
                        $validated['permanent_address'] ?? null,

                    'permanent_city' =>
                        $validated['permanent_city'] ?? null,

                    'permanent_state' =>
                        $validated['permanent_state'] ?? null,

                    'permanent_pin_code' =>
                        $validated['permanent_pin_code'] ?? null,

                    /*
                     * Previous School
                     */
                    'previous_school' =>
                        $validated['previous_school'] ?? null,

                    'previous_class' =>
                        $validated['previous_class'] ?? null,

                    'previous_board' =>
                        $validated['previous_board'] ?? null,

                    'application_status' =>
                        'submitted',

                    'remarks' =>
                        $validated['remarks'] ?? null,

                    'created_by' =>
                        auth()->id(),

                    'status' => true,
                ]);


            /*
             * Update enquiry
             */
            $admissionEnquiry->update([

                'enquiry_status' =>
                    'applied',

                'next_followup_date' =>
                    null,
            ]);


            return $application;
        });


        return redirect()
            ->route(
                'admission-applications.edit',
                $application
            )
            ->with(
                'success',
                "Application {$application->application_no} created successfully."
            );
    }


    public function edit(
        AdmissionApplication $admissionApplication
    ) {
        $this->checkApplicationAccess(
            $admissionApplication
        );

        $admissionApplication->load([
            'enquiry',
            'schoolClass',
            'admissionSession',
            'academicYear',
            'documents.verifier',
            'documents.uploader',
            'reviewer',
            'approver',
            'rejector',
        ]);

        return view(
            'admission-applications.edit',
            compact('admissionApplication')
        );
    }


    public function update(
        Request $request,
        AdmissionApplication $admissionApplication
    ) {
        $this->checkApplicationAccess(
            $admissionApplication
        );

        /*
         * Once admitted, normal editing should stop.
         */
        if (
            $admissionApplication->application_status
            === 'admitted'
        ) {

            return back()->with(
                'error',
                'An admitted application cannot be edited.'
            );
        }


        $validated =
            $this->validateApplication($request);


        $admissionApplication->update(
            $this->applicationData(
                $validated
            )
        );


        return redirect()
            ->route(
                'admission-applications.edit',
                $admissionApplication
            )
            ->with(
                'success',
                'Application updated successfully.'
            );
    }


    private function applicationData(
        array $validated
    ): array {

        return [

            'application_date' =>
                $validated['application_date'],

            'student_name' =>
                $validated['student_name'],

            'date_of_birth' =>
                $validated['date_of_birth'] ?? null,

            'gender' =>
                $validated['gender'] ?? null,

            'blood_group' =>
                $validated['blood_group'] ?? null,

            'nationality' =>
                $validated['nationality'] ?? 'Indian',

            'religion' =>
                $validated['religion'] ?? null,

            'category' =>
                $validated['category'] ?? null,

            'mother_tongue' =>
                $validated['mother_tongue'] ?? null,

            'father_name' =>
                $validated['father_name'] ?? null,

            'father_mobile' =>
                $validated['father_mobile'] ?? null,

            'father_email' =>
                $validated['father_email'] ?? null,

            'father_occupation' =>
                $validated['father_occupation'] ?? null,

            'mother_name' =>
                $validated['mother_name'] ?? null,

            'mother_mobile' =>
                $validated['mother_mobile'] ?? null,

            'mother_email' =>
                $validated['mother_email'] ?? null,

            'mother_occupation' =>
                $validated['mother_occupation'] ?? null,

            'guardian_name' =>
                $validated['guardian_name'] ?? null,

            'guardian_relation' =>
                $validated['guardian_relation'] ?? null,

            'guardian_mobile' =>
                $validated['guardian_mobile'] ?? null,

            'present_address' =>
                $validated['present_address'] ?? null,

            'present_city' =>
                $validated['present_city'] ?? null,

            'present_state' =>
                $validated['present_state'] ?? null,

            'present_pin_code' =>
                $validated['present_pin_code'] ?? null,

            'permanent_address' =>
                $validated['permanent_address'] ?? null,

            'permanent_city' =>
                $validated['permanent_city'] ?? null,

            'permanent_state' =>
                $validated['permanent_state'] ?? null,

            'permanent_pin_code' =>
                $validated['permanent_pin_code'] ?? null,

            'previous_school' =>
                $validated['previous_school'] ?? null,

            'previous_class' =>
                $validated['previous_class'] ?? null,

            'previous_board' =>
                $validated['previous_board'] ?? null,

            'remarks' =>
                $validated['remarks'] ?? null,
        ];
    }


    private function validateApplication(
        Request $request
    ): array {

        return $request->validate([

            'application_date' => [
                'required',
                'date'
            ],

            'student_name' => [
                'required',
                'string',
                'max:150'
            ],

            'date_of_birth' => [
                'nullable',
                'date',
                'before_or_equal:today'
            ],

            'gender' => [
                'nullable',
                Rule::in([
                    'male',
                    'female',
                    'other'
                ])
            ],

            'blood_group' => [
                'nullable',
                'string',
                'max:10'
            ],

            'nationality' => [
                'nullable',
                'string',
                'max:100'
            ],

            'religion' => [
                'nullable',
                'string',
                'max:100'
            ],

            'category' => [
                'nullable',
                'string',
                'max:50'
            ],

            'mother_tongue' => [
                'nullable',
                'string',
                'max:100'
            ],

            'father_name' => [
                'nullable',
                'string',
                'max:150'
            ],

            'father_mobile' => [
                'nullable',
                'string',
                'max:20'
            ],

            'father_email' => [
                'nullable',
                'email',
                'max:150'
            ],

            'father_occupation' => [
                'nullable',
                'string',
                'max:150'
            ],

            'mother_name' => [
                'nullable',
                'string',
                'max:150'
            ],

            'mother_mobile' => [
                'nullable',
                'string',
                'max:20'
            ],

            'mother_email' => [
                'nullable',
                'email',
                'max:150'
            ],

            'mother_occupation' => [
                'nullable',
                'string',
                'max:150'
            ],

            'guardian_name' => [
                'nullable',
                'string',
                'max:150'
            ],

            'guardian_relation' => [
                'nullable',
                'string',
                'max:100'
            ],

            'guardian_mobile' => [
                'nullable',
                'string',
                'max:20'
            ],

            'present_address' => [
                'nullable',
                'string'
            ],

            'present_city' => [
                'nullable',
                'string',
                'max:100'
            ],

            'present_state' => [
                'nullable',
                'string',
                'max:100'
            ],

            'present_pin_code' => [
                'nullable',
                'string',
                'max:10'
            ],

            'permanent_address' => [
                'nullable',
                'string'
            ],

            'permanent_city' => [
                'nullable',
                'string',
                'max:100'
            ],

            'permanent_state' => [
                'nullable',
                'string',
                'max:100'
            ],

            'permanent_pin_code' => [
                'nullable',
                'string',
                'max:10'
            ],

            'previous_school' => [
                'nullable',
                'string',
                'max:200'
            ],

            'previous_class' => [
                'nullable',
                'string',
                'max:100'
            ],

            'previous_board' => [
                'nullable',
                'string',
                'max:100'
            ],

            'remarks' => [
                'nullable',
                'string'
            ],
        ]);
    }


    private function checkEnquiryAccess(
        AdmissionEnquiry $enquiry
    ): void {

        if (
            $enquiry->school_id
            != auth()->user()->school_id
        ) {
            abort(403);
        }
    }


    private function checkApplicationAccess(
        AdmissionApplication $application
    ): void {

        if (
            $application->school_id
            != auth()->user()->school_id
        ) {
            abort(403);
        }
    }
}