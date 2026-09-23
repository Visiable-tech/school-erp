<?php

namespace App\Http\Controllers;

use App\Models\AdmissionApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdmissionApplicationReviewController extends Controller
{
    public function review(
        Request $request,
        AdmissionApplication $admissionApplication
    ) {
        $this->checkAccess($admissionApplication);

        if (
            !in_array(
                $admissionApplication->application_status,
                ['submitted', 'under_review']
            )
        ) {
            return back()->with(
                'error',
                'This application cannot be moved to review.'
            );
        }

        $request->validate([
            'review_remarks' => [
                'nullable',
                'string'
            ],
        ]);

        $admissionApplication->update([

            'application_status' =>
                'under_review',

            'reviewed_by' =>
                auth()->id(),

            'reviewed_at' =>
                now(),

            'review_remarks' =>
                $request->review_remarks,
        ]);

        return back()->with(
            'success',
            'Application moved to review.'
        );
    }


    public function approve(
        Request $request,
        AdmissionApplication $admissionApplication
    ) {
        $this->checkAccess($admissionApplication);

        if (
            !in_array(
                $admissionApplication->application_status,
                ['submitted', 'under_review']
            )
        ) {
            return back()->with(
                'error',
                'This application cannot be approved.'
            );
        }

        $request->validate([
            'review_remarks' => [
                'nullable',
                'string'
            ],
        ]);


        /*
         * Block approval if any uploaded document
         * has explicitly been rejected.
         */
        if (
            $admissionApplication
                ->documents()
                ->where(
                    'verification_status',
                    'rejected'
                )
                ->exists()
        ) {

            return back()->with(
                'error',
                'Application cannot be approved because one or more documents are rejected.'
            );
        }


        DB::transaction(function () use (
            $admissionApplication,
            $request
        ) {

            $admissionApplication->update([

                'application_status' =>
                    'approved',

                'approved_by' =>
                    auth()->id(),

                'approved_at' =>
                    now(),

                'rejected_by' =>
                    null,

                'rejected_at' =>
                    null,

                'review_remarks' =>
                    $request->review_remarks,
            ]);


            $admissionApplication
                ->enquiry
                ->update([

                    'enquiry_status' =>
                        'applied',

                    'next_followup_date' =>
                        null,
                ]);
        });


        return back()->with(
            'success',
            'Application approved successfully.'
        );
    }


    public function reject(
        Request $request,
        AdmissionApplication $admissionApplication
    ) {
        $this->checkAccess($admissionApplication);

        if (
            !in_array(
                $admissionApplication->application_status,
                ['submitted', 'under_review']
            )
        ) {
            return back()->with(
                'error',
                'This application cannot be rejected.'
            );
        }

        $validated = $request->validate([

            'review_remarks' => [
                'required',
                'string',
                'max:2000'
            ],
        ]);


        DB::transaction(function () use (
            $admissionApplication,
            $validated
        ) {

            $admissionApplication->update([

                'application_status' =>
                    'rejected',

                'rejected_by' =>
                    auth()->id(),

                'rejected_at' =>
                    now(),

                'approved_by' =>
                    null,

                'approved_at' =>
                    null,

                'review_remarks' =>
                    $validated['review_remarks'],
            ]);


            $admissionApplication
                ->enquiry
                ->update([

                    'enquiry_status' =>
                        'closed',

                    'next_followup_date' =>
                        null,
                ]);
        });


        return back()->with(
            'success',
            'Application rejected.'
        );
    }


    private function checkAccess(
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