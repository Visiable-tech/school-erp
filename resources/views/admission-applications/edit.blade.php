@extends('layouts.admin')

@section('title', 'Edit Admission Application')

@section('content')


{{-- =========================================================
     PAGE HEADER
========================================================= --}}

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h4 class="mb-1">
            {{ $admissionApplication->application_no }}
        </h4>

        <div class="text-muted">

            {{ $admissionApplication->student_name }}

            @if($admissionApplication->schoolClass)

                |
                {{ $admissionApplication->schoolClass->name }}

            @endif

        </div>

    </div>


    <div>

        @php

            $statusColors = [
                'draft' => 'secondary',
                'submitted' => 'primary',
                'under_review' => 'warning',
                'approved' => 'success',
                'rejected' => 'danger',
                'admitted' => 'success',
            ];

            $statusColor =
                $statusColors[
                    $admissionApplication->application_status
                ] ?? 'secondary';

        @endphp


        <span class="badge bg-{{ $statusColor }} me-2">

            {{
                ucwords(
                    str_replace(
                        '_',
                        ' ',
                        $admissionApplication->application_status
                    )
                )
            }}

        </span>


        <a
            href="{{ route('admission-applications.index') }}"
            class="btn btn-outline-secondary"
        >

            <i class="bi bi-arrow-left"></i>

            Back

        </a>

    </div>

</div>



{{-- =========================================================
     APPLICATION BASIC INFORMATION
========================================================= --}}

@if(
    !in_array(
        $admissionApplication->application_status,
        ['admitted']
    )
)

    <form
        method="POST"
        action="{{ route(
            'admission-applications.update',
            $admissionApplication
        ) }}"
    >

        @csrf
        @method('PUT')


        @include('admission-applications._form')


        <div class="card border-0 shadow-sm mb-4">

            <div class="card-body">

                <button
                    type="submit"
                    class="btn btn-primary"
                >

                    <i class="bi bi-check-lg"></i>

                    Update Application

                </button>

            </div>

        </div>

    </form>

@endif



{{-- =========================================================
     STEP 10 - DOCUMENT UPLOAD
========================================================= --}}

@can('admission-document.create')

    @if(
        !in_array(
            $admissionApplication->application_status,
            ['rejected', 'admitted']
        )
    )

        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white py-3">

                <strong>

                    <i class="bi bi-cloud-upload me-1"></i>

                    Upload Document

                </strong>

            </div>


            <div class="card-body">

                <form
                    method="POST"
                    enctype="multipart/form-data"
                    action="{{ route(
                        'admission-documents.store',
                        $admissionApplication
                    ) }}"
                >

                    @csrf


                    <div class="row g-3">


                        {{-- DOCUMENT TYPE --}}

                        <div class="col-md-3">

                            <label class="form-label">

                                Document Type

                                <span class="text-danger">*</span>

                            </label>


                            <select
                                name="document_type"
                                class="form-select"
                                required
                            >

                                <option value="">
                                    Select Document
                                </option>

                                <option value="birth_certificate">
                                    Birth Certificate
                                </option>

                                <option value="aadhaar">
                                    Aadhaar
                                </option>

                                <option value="transfer_certificate">
                                    Transfer Certificate
                                </option>

                                <option value="marksheet">
                                    Previous Marksheet
                                </option>

                                <option value="student_photo">
                                    Student Photograph
                                </option>

                                <option value="address_proof">
                                    Address Proof
                                </option>

                                <option value="other">
                                    Other
                                </option>

                            </select>

                        </div>



                        {{-- DOCUMENT NAME --}}

                        <div class="col-md-3">

                            <label class="form-label">

                                Document Name

                                <span class="text-danger">*</span>

                            </label>


                            <input
                                type="text"
                                name="document_name"
                                class="form-control"
                                placeholder="Document name"
                                required
                            >

                        </div>



                        {{-- DOCUMENT NUMBER --}}

                        <div class="col-md-3">

                            <label class="form-label">
                                Document Number
                            </label>


                            <input
                                type="text"
                                name="document_number"
                                class="form-control"
                                placeholder="Optional"
                            >

                        </div>



                        {{-- FILE --}}

                        <div class="col-md-3">

                            <label class="form-label">

                                Select File

                                <span class="text-danger">*</span>

                            </label>


                            <input
                                type="file"
                                name="document_file"
                                class="form-control"
                                accept=".pdf,.jpg,.jpeg,.png"
                                required
                            >


                            <small class="text-muted">

                                PDF/JPG/PNG, max 5 MB

                            </small>

                        </div>

                    </div>


                    <div class="mt-3">

                        <button
                            type="submit"
                            class="btn btn-primary"
                        >

                            <i class="bi bi-upload"></i>

                            Upload Document

                        </button>

                    </div>

                </form>

            </div>

        </div>

    @endif

@endcan



{{-- =========================================================
     STEP 11 - UPLOADED DOCUMENT LIST
========================================================= --}}

<div class="card border-0 shadow-sm mb-4">

    <div class="card-header bg-white py-3">

        <div class="d-flex justify-content-between align-items-center">

            <strong>

                <i class="bi bi-files me-1"></i>

                Application Documents

            </strong>


            <span class="badge bg-secondary">

                {{ $admissionApplication->documents->count() }}

                Document(s)

            </span>

        </div>

    </div>


    <div class="card-body p-0">

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">

                    <tr>

                        <th class="ps-3">
                            Document
                        </th>

                        <th>
                            Number
                        </th>

                        <th>
                            File
                        </th>

                        <th>
                            Verification Status
                        </th>

                        <th style="width:300px;">
                            Verify / Reject
                        </th>

                        <th style="width:80px;">
                            Action
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse(
                        $admissionApplication->documents
                        as $document
                    )

                        <tr>


                            {{-- DOCUMENT --}}

                            <td class="ps-3">

                                <strong>
                                    {{ $document->document_name }}
                                </strong>


                                <div class="small text-muted">

                                    {{
                                        ucwords(
                                            str_replace(
                                                '_',
                                                ' ',
                                                $document->document_type
                                            )
                                        )
                                    }}

                                </div>

                            </td>



                            {{-- NUMBER --}}

                            <td>

                                {{
                                    $document->document_number
                                        ?: '-'
                                }}

                            </td>



                            {{-- FILE --}}

                            <td>

                                @if($document->file_path)

                                    <a
                                        href="{{ asset(
                                            'storage/' .
                                            $document->file_path
                                        ) }}"
                                        target="_blank"
                                        class="btn btn-sm btn-outline-primary"
                                    >

                                        <i class="bi bi-eye"></i>

                                        View

                                    </a>

                                @else

                                    <span class="text-muted">
                                        No File
                                    </span>

                                @endif

                            </td>



                            {{-- VERIFICATION STATUS --}}

                            <td>

                                @if(
                                    $document->verification_status
                                    == 'verified'
                                )

                                    <span class="badge bg-success">

                                        <i class="bi bi-check-circle"></i>

                                        Verified

                                    </span>


                                @elseif(
                                    $document->verification_status
                                    == 'rejected'
                                )

                                    <span class="badge bg-danger">

                                        <i class="bi bi-x-circle"></i>

                                        Rejected

                                    </span>


                                @else

                                    <span class="badge bg-warning text-dark">

                                        <i class="bi bi-clock"></i>

                                        Pending

                                    </span>

                                @endif


                                @if($document->verifier)

                                    <div class="small text-muted mt-1">

                                        By:
                                        {{ $document->verifier->name }}

                                    </div>

                                @endif


                                @if($document->verification_remarks)

                                    <div class="small mt-1">

                                        {{
                                            $document
                                                ->verification_remarks
                                        }}

                                    </div>

                                @endif

                            </td>



                            {{-- VERIFY / REJECT --}}

                            <td>

                                @can('admission-document.verify')

                                    @if(
                                        !in_array(
                                            $admissionApplication
                                                ->application_status,
                                            ['rejected', 'admitted']
                                        )
                                    )

                                        <form
                                            method="POST"
                                            action="{{ route(
                                                'admission-documents.verify',
                                                $document
                                            ) }}"
                                        >

                                            @csrf
                                            @method('PUT')


                                            <div class="row g-1">

                                                <div class="col-md-5">

                                                    <select
                                                        name="verification_status"
                                                        class="form-select form-select-sm"
                                                        required
                                                    >

                                                        <option
                                                            value="verified"
                                                            @selected(
                                                                $document
                                                                    ->verification_status
                                                                == 'verified'
                                                            )
                                                        >
                                                            Verified
                                                        </option>


                                                        <option
                                                            value="rejected"
                                                            @selected(
                                                                $document
                                                                    ->verification_status
                                                                == 'rejected'
                                                            )
                                                        >
                                                            Rejected
                                                        </option>

                                                    </select>

                                                </div>


                                                <div class="col-md-5">

                                                    <input
                                                        type="text"
                                                        name="verification_remarks"
                                                        class="form-control form-control-sm"
                                                        placeholder="Remarks"
                                                        value="{{ $document->verification_remarks }}"
                                                    >

                                                </div>


                                                <div class="col-md-2">

                                                    <button
                                                        type="submit"
                                                        class="btn btn-sm btn-outline-success"
                                                        title="Save Verification"
                                                    >

                                                        <i class="bi bi-check-lg"></i>

                                                    </button>

                                                </div>

                                            </div>

                                        </form>

                                    @else

                                        -

                                    @endif


                                @else

                                    @if($document->verifier)

                                        {{ $document->verifier->name }}

                                    @else

                                        -

                                    @endif

                                @endcan

                            </td>



                            {{-- DELETE DOCUMENT --}}

                            <td>

                                @can('admission-document.delete')

                                    @if(
                                        !in_array(
                                            $admissionApplication
                                                ->application_status,
                                            ['rejected', 'admitted']
                                        )
                                    )

                                        <form
                                            method="POST"
                                            action="{{ route(
                                                'admission-documents.destroy',
                                                $document
                                            ) }}"
                                            onsubmit="return confirm('Delete this document?')"
                                        >

                                            @csrf
                                            @method('DELETE')


                                            <button
                                                type="submit"
                                                class="btn btn-sm btn-outline-danger"
                                                title="Delete Document"
                                            >

                                                <i class="bi bi-trash"></i>

                                            </button>

                                        </form>

                                    @else

                                        -

                                    @endif

                                @else

                                    -

                                @endcan

                            </td>

                        </tr>


                    @empty

                        <tr>

                            <td
                                colspan="6"
                                class="text-center py-5"
                            >

                                <i
                                    class="bi bi-file-earmark-x fs-2 text-muted"
                                ></i>


                                <div class="text-muted mt-2">

                                    No documents uploaded yet.

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>



{{-- =========================================================
     STEP 12 - APPLICATION REVIEW
========================================================= --}}

<div class="card border-0 shadow-sm mb-4">

    <div class="card-header bg-white py-3">

        <strong>

            <i class="bi bi-clipboard-check me-1"></i>

            Application Review

        </strong>

    </div>


    <div class="card-body">


        {{-- CURRENT STATUS --}}

        <div class="row mb-4">

            <div class="col-md-4">

                <small class="text-muted">
                    Application Status
                </small>


                <div class="mt-1">

                    <span class="badge bg-{{ $statusColor }}">

                        {{
                            ucwords(
                                str_replace(
                                    '_',
                                    ' ',
                                    $admissionApplication
                                        ->application_status
                                )
                            )
                        }}

                    </span>

                </div>

            </div>


            <div class="col-md-4">

                <small class="text-muted">
                    Total Documents
                </small>


                <div class="fw-semibold mt-1">

                    {{
                        $admissionApplication
                            ->documents
                            ->count()
                    }}

                </div>

            </div>


            <div class="col-md-4">

                <small class="text-muted">
                    Verified Documents
                </small>


                <div class="fw-semibold mt-1">

                    {{
                        $admissionApplication
                            ->documents
                            ->where(
                                'verification_status',
                                'verified'
                            )
                            ->count()
                    }}

                </div>

            </div>

        </div>



        {{-- REVIEW REMARKS --}}

        @if($admissionApplication->review_remarks)

            <div class="alert alert-light border">

                <strong>
                    Review Remarks
                </strong>


                <div class="mt-1">

                    {{ $admissionApplication->review_remarks }}

                </div>

            </div>

        @endif



        {{-- =====================================================
             REVIEW ACTIONS
        ====================================================== --}}

        @if(
            in_array(
                $admissionApplication->application_status,
                [
                    'submitted',
                    'under_review'
                ]
            )
        )

            <div class="row">


                {{-- MARK UNDER REVIEW --}}

                @can('admission-application.review')

                    <div class="col-md-4 mb-3">

                        <div class="border rounded p-3 h-100">

                            <h6>

                                <i class="bi bi-search"></i>

                                Review Application

                            </h6>


                            <p class="small text-muted">

                                Mark the application as
                                under review.

                            </p>


                            <form
                                method="POST"
                                action="{{ route(
                                    'admission-applications.review',
                                    $admissionApplication
                                ) }}"
                            >

                                @csrf


                                <textarea
                                    name="review_remarks"
                                    class="form-control mb-3"
                                    rows="3"
                                    placeholder="Review remarks..."
                                ></textarea>


                                <button
                                    type="submit"
                                    class="btn btn-warning w-100"
                                >

                                    <i class="bi bi-search"></i>

                                    Mark Under Review

                                </button>

                            </form>

                        </div>

                    </div>

                @endcan



                {{-- APPROVE --}}

                @can('admission-application.approve')

                    <div class="col-md-4 mb-3">

                        <div class="border rounded p-3 h-100">

                            <h6 class="text-success">

                                <i class="bi bi-check-circle"></i>

                                Approve Application

                            </h6>


                            <p class="small text-muted">

                                Approve the application
                                after checking all details
                                and documents.

                            </p>


                            <form
                                method="POST"
                                action="{{ route(
                                    'admission-applications.approve',
                                    $admissionApplication
                                ) }}"
                                onsubmit="return confirm('Are you sure you want to approve this application?')"
                            >

                                @csrf


                                <textarea
                                    name="review_remarks"
                                    class="form-control mb-3"
                                    rows="3"
                                    placeholder="Approval remarks..."
                                ></textarea>


                                <button
                                    type="submit"
                                    class="btn btn-success w-100"
                                >

                                    <i class="bi bi-check-circle"></i>

                                    Approve Application

                                </button>

                            </form>

                        </div>

                    </div>

                @endcan



                {{-- REJECT --}}

                @can('admission-application.reject')

                    <div class="col-md-4 mb-3">

                        <div class="border rounded p-3 h-100">

                            <h6 class="text-danger">

                                <i class="bi bi-x-circle"></i>

                                Reject Application

                            </h6>


                            <p class="small text-muted">

                                Reject the application
                                with a mandatory reason.

                            </p>


                            <form
                                method="POST"
                                action="{{ route(
                                    'admission-applications.reject',
                                    $admissionApplication
                                ) }}"
                                onsubmit="return confirm('Are you sure you want to reject this application?')"
                            >

                                @csrf


                                <textarea
                                    name="review_remarks"
                                    class="form-control mb-3"
                                    rows="3"
                                    placeholder="Reason for rejection..."
                                    required
                                ></textarea>


                                <button
                                    type="submit"
                                    class="btn btn-danger w-100"
                                >

                                    <i class="bi bi-x-circle"></i>

                                    Reject Application

                                </button>

                            </form>

                        </div>

                    </div>

                @endcan

            </div>

        @endif



        {{-- =====================================================
             APPROVED APPLICATION
        ====================================================== --}}

        @if($admissionApplication->application_status === 'approved')

            <div class="alert alert-success mb-0">

                <h6 class="alert-heading">

                    <i class="bi bi-check-circle-fill"></i>

                    Application Approved

                </h6>


                @if($admissionApplication->approver)

                    <div>

                        Approved by:

                        <strong>
                            {{ $admissionApplication->approver->name }}
                        </strong>

                    </div>

                @endif


                @if($admissionApplication->approved_at)

                    <div>

                        Date:

                        {{
                            $admissionApplication
                                ->approved_at
                                ->format('d M Y h:i A')
                        }}

                    </div>

                @endif



                {{-- =============================================
                     ADMIT STUDENT
                ============================================== --}}

                @can('student-admission.create')

                    <div class="mt-3">

                        @if(!$admissionApplication->student)

                            <a
                                href="{{ route(
                                    'student-admissions.create',
                                    $admissionApplication
                                ) }}"
                                class="btn btn-success"
                            >

                                <i class="bi bi-person-check"></i>

                                Admit Student

                            </a>

                        @else

                            <a
                                href="{{ route(
                                    'students.show',
                                    $admissionApplication->student
                                ) }}"
                                class="btn btn-outline-success"
                            >

                                <i class="bi bi-person-vcard"></i>

                                View Student

                            </a>

                        @endif

                    </div>

                @endcan

            </div>

        @endif



        {{-- =====================================================
             ADMITTED APPLICATION
        ====================================================== --}}

        @if($admissionApplication->application_status === 'admitted')

            <div class="alert alert-success mb-0">

                <h6 class="alert-heading">

                    <i class="bi bi-person-check-fill"></i>

                    Student Admitted

                </h6>


                <p class="mb-2">

                    This application has successfully
                    been converted into a student record.

                </p>


                @if($admissionApplication->student)

                    <div class="mb-3">

                        <strong>
                            Admission No:
                        </strong>

                        {{
                            $admissionApplication
                                ->student
                                ->admission_no
                        }}

                    </div>


                    @can('student.view')

                        <a
                            href="{{ route(
                                'students.show',
                                $admissionApplication->student
                            ) }}"
                            class="btn btn-success"
                        >

                            <i class="bi bi-person-vcard"></i>

                            View Student Profile

                        </a>

                    @endcan

                @endif

            </div>

        @endif



        {{-- =====================================================
             REJECTED APPLICATION
        ====================================================== --}}

        @if($admissionApplication->application_status === 'rejected')

            <div class="alert alert-danger mb-0">

                <h6 class="alert-heading">

                    <i class="bi bi-x-circle-fill"></i>

                    Application Rejected

                </h6>


                @if($admissionApplication->rejector)

                    <div>

                        Rejected by:

                        <strong>
                            {{ $admissionApplication->rejector->name }}
                        </strong>

                    </div>

                @endif


                @if($admissionApplication->rejected_at)

                    <div>

                        Date:

                        {{
                            $admissionApplication
                                ->rejected_at
                                ->format('d M Y h:i A')
                        }}

                    </div>

                @endif


                @if($admissionApplication->review_remarks)

                    <div class="mt-2">

                        <strong>
                            Reason:
                        </strong>

                        {{
                            $admissionApplication
                                ->review_remarks
                        }}

                    </div>

                @endif

            </div>

        @endif


    </div>

</div>


@endsection