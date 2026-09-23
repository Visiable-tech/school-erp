<?php

namespace App\Http\Controllers;

use App\Models\AdmissionEnquiry;
use App\Models\AdmissionFollowup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AdmissionFollowupController extends Controller
{
    public function index(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        $query = AdmissionFollowup::with([
                'enquiry.schoolClass',
                'followedBy'
            ])
            ->where('school_id', $schoolId);

        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->whereHas(
                'enquiry',
                function ($q) use ($search) {

                    $q->where(
                        'enquiry_no',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'student_name',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'mobile',
                        'like',
                        "%{$search}%"
                    );
                }
            );
        }

        if ($request->filled('response_status')) {

            $query->where(
                'response_status',
                $request->response_status
            );
        }

        if ($request->filled('followup_date')) {

            $query->whereDate(
                'followup_date',
                $request->followup_date
            );
        }

        $followups = $query
            ->orderByDesc('followup_date')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view(
            'admission-followups.index',
            compact('followups')
        );
    }


    public function create(
        AdmissionEnquiry $admissionEnquiry
    ) {
        $this->checkEnquiryAccess(
            $admissionEnquiry
        );

        return view(
            'admission-followups.create',
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

        $validated = $this->validateFollowup(
            $request
        );

        DB::transaction(function () use (
            $validated,
            $request,
            $admissionEnquiry
        ) {

            AdmissionFollowup::create([

                'school_id' =>
                    $admissionEnquiry->school_id,

                'admission_enquiry_id' =>
                    $admissionEnquiry->id,

                'followup_date' =>
                    $validated['followup_date'],

                'followup_type' =>
                    $validated['followup_type'] ?? null,

                'contact_person' =>
                    $validated['contact_person'] ?? null,

                'response_status' =>
                    $validated['response_status'] ?? null,

                'remarks' =>
                    $validated['remarks'] ?? null,

                'next_followup_date' =>
                    $validated['next_followup_date'] ?? null,

                'followed_by' =>
                    auth()->id(),

                'status' => true,
            ]);


            /*
             * Keep enquiry's latest status and
             * next follow-up date synchronized.
             */
            $update = [
                'next_followup_date' =>
                    $validated['next_followup_date'] ?? null,
            ];


            if (
                !empty(
                    $validated['response_status']
                )
            ) {

                $statusMap = [

                    'follow_up' =>
                        'follow_up',

                    'interested' =>
                        'interested',

                    'not_interested' =>
                        'not_interested',

                    'applied' =>
                        'applied',

                    'closed' =>
                        'closed',
                ];


                if (
                    isset(
                        $statusMap[
                            $validated['response_status']
                        ]
                    )
                ) {

                    $update['enquiry_status'] =
                        $statusMap[
                            $validated['response_status']
                        ];
                }
            }


            $admissionEnquiry->update(
                $update
            );

        });


        return redirect()
            ->route(
                'admission-enquiries.edit',
                $admissionEnquiry
            )
            ->with(
                'success',
                'Follow-up added successfully.'
            );
    }


    public function edit(
        AdmissionFollowup $admissionFollowup
    ) {
        $this->checkFollowupAccess(
            $admissionFollowup
        );

        $admissionFollowup->load(
            'enquiry'
        );

        return view(
            'admission-followups.edit',
            compact('admissionFollowup')
        );
    }


    public function update(
        Request $request,
        AdmissionFollowup $admissionFollowup
    ) {
        $this->checkFollowupAccess(
            $admissionFollowup
        );

        $validated = $this->validateFollowup(
            $request
        );

        $admissionFollowup->update([

            'followup_date' =>
                $validated['followup_date'],

            'followup_type' =>
                $validated['followup_type'] ?? null,

            'contact_person' =>
                $validated['contact_person'] ?? null,

            'response_status' =>
                $validated['response_status'] ?? null,

            'remarks' =>
                $validated['remarks'] ?? null,

            'next_followup_date' =>
                $validated['next_followup_date'] ?? null,

        ]);


        return redirect()
            ->route('admission-followups.index')
            ->with(
                'success',
                'Follow-up updated successfully.'
            );
    }


    public function destroy(
        AdmissionFollowup $admissionFollowup
    ) {
        $this->checkFollowupAccess(
            $admissionFollowup
        );

        $admissionFollowup->delete();

        return back()->with(
            'success',
            'Follow-up deleted successfully.'
        );
    }


    private function validateFollowup(
        Request $request
    ): array {

        return $request->validate([

            'followup_date' => [
                'required',
                'date',
            ],

            'followup_type' => [
                'required',

                Rule::in([
                    'phone',
                    'whatsapp',
                    'email',
                    'school_visit',
                    'home_visit',
                    'other',
                ]),
            ],

            'contact_person' => [
                'nullable',
                'string',
                'max:150',
            ],

            'response_status' => [
                'required',

                Rule::in([
                    'follow_up',
                    'interested',
                    'not_interested',
                    'applied',
                    'closed',
                ]),
            ],

            'remarks' => [
                'nullable',
                'string',
            ],

            'next_followup_date' => [
                'nullable',
                'date',
                'after_or_equal:followup_date',
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


    private function checkFollowupAccess(
        AdmissionFollowup $followup
    ): void {

        if (
            $followup->school_id
            != auth()->user()->school_id
        ) {
            abort(403);
        }
    }
}