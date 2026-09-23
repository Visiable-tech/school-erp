<?php

namespace App\Http\Controllers;

use App\Models\AdmissionEnquiry;
use App\Models\AdmissionSession;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AdmissionEnquiryController extends Controller
{
    public function index(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        $query = AdmissionEnquiry::with([
            'admissionSession',
            'academicYear',
            'schoolClass.wing',
            'creator',
            'application', // IMPORTANT
        ])
        ->where('school_id', $schoolId);

        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->where(function ($q) use ($search) {

                $q->where('enquiry_no', 'like', "%{$search}%")
                    ->orWhere('student_name', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%");
            });
        }

        if ($request->filled('admission_session_id')) {
            $query->where(
                'admission_session_id',
                $request->admission_session_id
            );
        }

        if ($request->filled('school_class_id')) {
            $query->where(
                'school_class_id',
                $request->school_class_id
            );
        }

        if ($request->filled('enquiry_status')) {
            $query->where(
                'enquiry_status',
                $request->enquiry_status
            );
        }

        $enquiries = $query
            ->orderByDesc('enquiry_date')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();


        $sessions = AdmissionSession::where(
                'school_id',
                $schoolId
            )
            ->where('status', 1)
            ->orderByDesc('is_current')
            ->get();


        $classes = SchoolClass::where(
                'school_id',
                $schoolId
            )
            ->where('status', 1)
            ->orderBy('sort_order')
            ->get();


        return view(
            'admission-enquiries.index',
            compact(
                'enquiries',
                'sessions',
                'classes'
            )
        );
    }


    public function create()
    {
        $schoolId = auth()->user()->school_id;

        $sessions = AdmissionSession::with('academicYear')
            ->where('school_id', $schoolId)
            ->where('status', 1)
            ->orderByDesc('is_current')
            ->orderByDesc('start_date')
            ->get();

        $classes = SchoolClass::with('wing')
            ->where('school_id', $schoolId)
            ->where('status', 1)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $currentSession = $sessions->firstWhere(
            'is_current',
            true
        );

        return view(
            'admission-enquiries.create',
            compact(
                'sessions',
                'classes',
                'currentSession'
            )
        );
    }


    public function store(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        $validated = $this->validateEnquiry(
            $request,
            $schoolId
        );

        // Get admission session and its academic year
        $session = AdmissionSession::where('school_id', $schoolId)
            ->where('status', 1)
            ->findOrFail($validated['admission_session_id']);


        $enquiry = DB::transaction(function () use (
            $validated,
            $request,
            $schoolId,
            $session
        ) {

            /*
            |--------------------------------------------------------------------------
            | Generate Enquiry Number
            |--------------------------------------------------------------------------
            */

            $count = AdmissionEnquiry::where(
                'school_id',
                $schoolId
            )->count();

            $nextNumber = $count + 1;

            $enquiryNo =
                'ENQ-' .
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
            | Create Enquiry
            |--------------------------------------------------------------------------
            */

            return AdmissionEnquiry::create([

                'school_id' => $schoolId,

                'admission_session_id' =>
                    $session->id,

                'academic_year_id' =>
                    $session->academic_year_id,

                'school_class_id' =>
                    $validated['school_class_id'] ?? null,

                'enquiry_no' =>
                    $enquiryNo,

                'enquiry_date' =>
                    $validated['enquiry_date'],

                'student_name' =>
                    $validated['student_name'],

                'date_of_birth' =>
                    $validated['date_of_birth'] ?? null,

                'gender' =>
                    $validated['gender'] ?? null,

                'father_name' =>
                    $validated['father_name'] ?? null,

                'mother_name' =>
                    $validated['mother_name'] ?? null,

                'guardian_name' =>
                    $validated['guardian_name'] ?? null,

                'mobile' =>
                    $validated['mobile'],

                'alternate_mobile' =>
                    $validated['alternate_mobile'] ?? null,

                'email' =>
                    $validated['email'] ?? null,

                'address' =>
                    $validated['address'] ?? null,

                'city' =>
                    $validated['city'] ?? null,

                'pin_code' =>
                    $validated['pin_code'] ?? null,

                'previous_school' =>
                    $validated['previous_school'] ?? null,

                'source' =>
                    $validated['source'] ?? null,

                'reference_name' =>
                    $validated['reference_name'] ?? null,

                'enquiry_status' =>
                    $validated['enquiry_status'],

                'next_followup_date' =>
                    $validated['next_followup_date'] ?? null,

                'remarks' =>
                    $validated['remarks'] ?? null,

                'created_by' =>
                    auth()->id(),

                'status' =>
                    $request->boolean('status'),

            ]);

        });


        return redirect()
            ->route('admission-enquiries.index')
            ->with(
                'success',
                "Admission enquiry {$enquiry->enquiry_no} created successfully."
            );
    }

    public function edit(
        AdmissionEnquiry $admissionEnquiry
    ) {
        $this->checkSchoolAccess(
            $admissionEnquiry
        );

        $schoolId =
            $admissionEnquiry->school_id;

        $sessions = AdmissionSession::with('academicYear')
            ->where('school_id', $schoolId)
            ->where('status', 1)
            ->orderByDesc('is_current')
            ->orderByDesc('start_date')
            ->get();

        $classes = SchoolClass::with('wing')
            ->where('school_id', $schoolId)
            ->where('status', 1)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view(
            'admission-enquiries.edit',
            compact(
                'admissionEnquiry',
                'sessions',
                'classes'
            )
        );
    }


    public function update(
        Request $request,
        AdmissionEnquiry $admissionEnquiry
    ) {
        $this->checkSchoolAccess(
            $admissionEnquiry
        );

        $schoolId =
            $admissionEnquiry->school_id;

        $validated = $this->validateEnquiry(
            $request,
            $schoolId
        );

        $session = AdmissionSession::where(
                'school_id',
                $schoolId
            )
            ->findOrFail(
                $validated['admission_session_id']
            );


        $admissionEnquiry->update([

            'admission_session_id' =>
                $session->id,

            'academic_year_id' =>
                $session->academic_year_id,

            'school_class_id' =>
                $validated['school_class_id'] ?? null,

            'enquiry_date' =>
                $validated['enquiry_date'],

            'student_name' =>
                $validated['student_name'],

            'date_of_birth' =>
                $validated['date_of_birth'] ?? null,

            'gender' =>
                $validated['gender'] ?? null,

            'father_name' =>
                $validated['father_name'] ?? null,

            'mother_name' =>
                $validated['mother_name'] ?? null,

            'guardian_name' =>
                $validated['guardian_name'] ?? null,

            'mobile' =>
                $validated['mobile'],

            'alternate_mobile' =>
                $validated['alternate_mobile'] ?? null,

            'email' =>
                $validated['email'] ?? null,

            'address' =>
                $validated['address'] ?? null,

            'city' =>
                $validated['city'] ?? null,

            'pin_code' =>
                $validated['pin_code'] ?? null,

            'previous_school' =>
                $validated['previous_school'] ?? null,

            'source' =>
                $validated['source'] ?? null,

            'reference_name' =>
                $validated['reference_name'] ?? null,

            'enquiry_status' =>
                $validated['enquiry_status'],

            'next_followup_date' =>
                $validated['next_followup_date'] ?? null,

            'remarks' =>
                $validated['remarks'] ?? null,

            'status' =>
                $request->boolean('status'),

        ]);


        return redirect()
            ->route('admission-enquiries.index')
            ->with(
                'success',
                'Admission enquiry updated successfully.'
            );
    }


    public function destroy(
        AdmissionEnquiry $admissionEnquiry
    ) {
        $this->checkSchoolAccess(
            $admissionEnquiry
        );

        /*
         * Once follow-ups/applications are introduced,
         * dependency checks will be added here.
         */
        $admissionEnquiry->delete();

        return redirect()
            ->route('admission-enquiries.index')
            ->with(
                'success',
                'Admission enquiry deleted successfully.'
            );
    }


    private function validateEnquiry(
        Request $request,
        int $schoolId
    ): array {

        return $request->validate([

            'admission_session_id' => [
                'required',

                Rule::exists(
                    'admission_sessions',
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
                'nullable',

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

            'enquiry_date' => [
                'required',
                'date',
            ],

            'student_name' => [
                'required',
                'string',
                'max:150',
            ],

            'date_of_birth' => [
                'nullable',
                'date',
                'before_or_equal:today',
            ],

            'gender' => [
                'nullable',
                Rule::in([
                    'male',
                    'female',
                    'other',
                ]),
            ],

            'father_name' => [
                'nullable',
                'string',
                'max:150',
            ],

            'mother_name' => [
                'nullable',
                'string',
                'max:150',
            ],

            'guardian_name' => [
                'nullable',
                'string',
                'max:150',
            ],

            'mobile' => [
                'required',
                'string',
                'max:20',
            ],

            'alternate_mobile' => [
                'nullable',
                'string',
                'max:20',
            ],

            'email' => [
                'nullable',
                'email',
                'max:150',
            ],

            'address' => [
                'nullable',
                'string',
            ],

            'city' => [
                'nullable',
                'string',
                'max:100',
            ],

            'pin_code' => [
                'nullable',
                'string',
                'max:10',
            ],

            'previous_school' => [
                'nullable',
                'string',
                'max:200',
            ],

            'source' => [
                'nullable',
                'string',
                'max:100',
            ],

            'reference_name' => [
                'nullable',
                'string',
                'max:150',
            ],

            'enquiry_status' => [
                'required',

                Rule::in([
                    'open',
                    'follow_up',
                    'interested',
                    'not_interested',
                    'applied',
                    'admitted',
                    'closed',
                ]),
            ],

            'next_followup_date' => [
                'nullable',
                'date',
            ],

            'remarks' => [
                'nullable',
                'string',
            ],

        ]);
    }


    private function checkSchoolAccess(
        AdmissionEnquiry $enquiry
    ): void {

        if (
            $enquiry->school_id
            != auth()->user()->school_id
        ) {
            abort(403);
        }
    }
}