<?php

namespace App\Http\Controllers;

use App\Models\AdmissionApplication;
use App\Models\AdmissionApplicationDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AdmissionApplicationDocumentController extends Controller
{
    public function store(
        Request $request,
        AdmissionApplication $admissionApplication
    ) {
        $this->checkApplicationAccess(
            $admissionApplication
        );

        if (
            in_array(
                $admissionApplication->application_status,
                ['admitted', 'rejected']
            )
        ) {
            return back()->with(
                'error',
                'Documents cannot be added to this application.'
            );
        }

        $validated = $request->validate([

            'document_type' => [
                'required',
                'string',
                'max:100',
            ],

            'document_name' => [
                'required',
                'string',
                'max:150',
            ],

            'document_number' => [
                'nullable',
                'string',
                'max:100',
            ],

            'document_file' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120',
            ],
        ]);

        $path = $request
            ->file('document_file')
            ->store(
                'admission-documents/' .
                $admissionApplication->school_id .
                '/' .
                $admissionApplication->id,
                'public'
            );

        AdmissionApplicationDocument::create([

            'school_id' =>
                $admissionApplication->school_id,

            'admission_application_id' =>
                $admissionApplication->id,

            'document_type' =>
                $validated['document_type'],

            'document_name' =>
                $validated['document_name'],

            'document_number' =>
                $validated['document_number'] ?? null,

            'file_path' =>
                $path,

            'verification_status' =>
                'pending',

            'uploaded_by' =>
                auth()->id(),

            'status' =>
                true,
        ]);

        return back()->with(
            'success',
            'Document uploaded successfully.'
        );
    }


    public function verify(
        Request $request,
        AdmissionApplicationDocument $document
    ) {
        $this->checkDocumentAccess($document);

        $validated = $request->validate([

            'verification_status' => [
                'required',

                Rule::in([
                    'verified',
                    'rejected'
                ]),
            ],

            'verification_remarks' => [
                'nullable',
                'string'
            ],
        ]);

        $document->update([

            'verification_status' =>
                $validated['verification_status'],

            'verification_remarks' =>
                $validated['verification_remarks'] ?? null,

            'verified_by' =>
                auth()->id(),

            'verified_at' =>
                now(),
        ]);

        return back()->with(
            'success',
            'Document verification updated.'
        );
    }


    public function destroy(
        AdmissionApplicationDocument $document
    ) {
        $this->checkDocumentAccess($document);

        if ($document->file_path) {

            Storage::disk('public')
                ->delete($document->file_path);
        }

        $document->delete();

        return back()->with(
            'success',
            'Document deleted successfully.'
        );
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


    private function checkDocumentAccess(
        AdmissionApplicationDocument $document
    ): void {

        if (
            $document->school_id
            != auth()->user()->school_id
        ) {
            abort(403);
        }
    }
}