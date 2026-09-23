@extends('layouts.admin')

@section('title', 'Student Documents')

@section('content')

@php

    $documentTypes = [
        'birth_certificate' => 'Birth Certificate',
        'aadhaar_card' => 'Aadhaar Card',
        'transfer_certificate' => 'Transfer Certificate',
        'previous_marksheet' => 'Previous School Marksheet',
        'address_proof' => 'Address Proof',
        'medical_certificate' => 'Medical Certificate',
        'student_photo' => 'Student Photo',
        'caste_certificate' => 'Caste Certificate',
        'income_certificate' => 'Income Certificate',
        'migration_certificate' => 'Migration Certificate',
        'other' => 'Other',
    ];

@endphp


<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h4 class="mb-1">
            Student Documents
        </h4>

        <div class="text-muted">
            {{ $student->student_name }}
            —
            {{ $student->admission_no }}
        </div>
    </div>


    <a
        href="{{ route('students.show', $student) }}"
        class="btn btn-outline-secondary"
    >
        <i class="bi bi-arrow-left"></i>
        Student Profile
    </a>

</div>


{{-- STUDENT INFORMATION --}}

<div class="card border-0 shadow-sm mb-4">

    <div class="card-body">

        <div class="row">

            <div class="col-md-3">

                <small class="text-muted">
                    Admission No.
                </small>

                <div class="fw-semibold">
                    {{ $student->admission_no }}
                </div>

            </div>


            <div class="col-md-3">

                <small class="text-muted">
                    Student
                </small>

                <div class="fw-semibold">
                    {{ $student->student_name }}
                </div>

            </div>


            <div class="col-md-2">

                <small class="text-muted">
                    Class
                </small>

                <div>
                    {{ $student->currentEnrollment?->schoolClass?->name ?? '-' }}
                </div>

            </div>


            <div class="col-md-2">

                <small class="text-muted">
                    Section
                </small>

                <div>
                    {{ $student->currentEnrollment?->section?->name ?? '-' }}
                </div>

            </div>


            <div class="col-md-2">

                <small class="text-muted">
                    Roll No.
                </small>

                <div>
                    {{ $student->currentEnrollment?->roll_no ?? '-' }}
                </div>

            </div>

        </div>

    </div>

</div>


{{-- UPLOAD DOCUMENT --}}

@can('student-document.create')

<div class="card border-0 shadow-sm mb-4">

    <div class="card-header bg-white py-3">
        <strong>Upload Document</strong>
    </div>


    <div class="card-body">

        <form
            action="{{ route('student-documents.store', $student) }}"
            method="POST"
            enctype="multipart/form-data"
        >

            @csrf


            <div class="row g-3">

                <div class="col-md-3">

                    <label class="form-label">
                        Document Type *
                    </label>

                    <select
                        name="document_type"
                        class="form-select"
                        required
                    >

                        <option value="">
                            Select Type
                        </option>

                        @foreach($documentTypes as $key => $label)

                            <option
                                value="{{ $key }}"
                                @selected(old('document_type') === $key)
                            >
                                {{ $label }}
                            </option>

                        @endforeach

                    </select>

                </div>


                <div class="col-md-3">

                    <label class="form-label">
                        Document Name *
                    </label>

                    <input
                        type="text"
                        name="document_name"
                        value="{{ old('document_name') }}"
                        class="form-control"
                        placeholder="Example: Birth Certificate"
                        required
                    >

                </div>


                <div class="col-md-3">

                    <label class="form-label">
                        Document Number
                    </label>

                    <input
                        type="text"
                        name="document_number"
                        value="{{ old('document_number') }}"
                        class="form-control"
                    >

                </div>


                <div class="col-md-3">

                    <label class="form-label">
                        File *
                    </label>

                    <input
                        type="file"
                        name="file"
                        class="form-control"
                        accept=".pdf,.jpg,.jpeg,.png"
                        required
                    >

                    <small class="text-muted">
                        PDF/JPG/PNG, maximum 5 MB
                    </small>

                </div>


                <div class="col-md-12">

                    <label class="form-label">
                        Remarks
                    </label>

                    <textarea
                        name="remarks"
                        class="form-control"
                        rows="2"
                    >{{ old('remarks') }}</textarea>

                </div>


                <div class="col-12">

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        <i class="bi bi-cloud-upload"></i>
                        Upload Document
                    </button>

                </div>

            </div>

        </form>

    </div>

</div>

@endcan


{{-- DOCUMENT LIST --}}

<div class="card border-0 shadow-sm">

    <div class="card-header bg-white py-3">

        <div class="d-flex justify-content-between">

            <strong>
                Uploaded Documents
            </strong>

            <span class="badge bg-secondary">
                {{ $student->documents->count() }}
            </span>

        </div>

    </div>


    <div class="card-body p-0">

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">

                    <tr>
                        <th>#</th>
                        <th>Type</th>
                        <th>Document</th>
                        <th>Number</th>
                        <th>Uploaded</th>
                        <th>Status</th>
                        <th width="180">Action</th>
                    </tr>

                </thead>


                <tbody>

                @forelse($student->documents as $document)

                    <tr>

                        <td>
                            {{ $loop->iteration }}
                        </td>


                        <td>

                            {{ $documentTypes[$document->document_type]
                                ?? ucfirst(str_replace('_', ' ', $document->document_type))
                            }}

                        </td>


                        <td>

                            <div class="fw-semibold">
                                {{ $document->document_name }}
                            </div>

                            @if($document->remarks)

                                <small class="text-muted">
                                    {{ $document->remarks }}
                                </small>

                            @endif

                        </td>


                        <td>
                            {{ $document->document_number ?? '-' }}
                        </td>


                        <td>

                            {{ $document->created_at->format('d M Y') }}

                            @if($document->uploader)

                                <div class="small text-muted">
                                    {{ $document->uploader->name }}
                                </div>

                            @endif

                        </td>


                        <td>

                            @if($document->status)

                                <span class="badge bg-success">
                                    Active
                                </span>

                            @else

                                <span class="badge bg-secondary">
                                    Inactive
                                </span>

                            @endif

                        </td>


                        <td>

                            <div class="d-flex gap-1">


                                <a
                                    href="{{ asset('storage/' . $document->file_path) }}"
                                    target="_blank"
                                    class="btn btn-sm btn-outline-primary"
                                    title="View"
                                >
                                    <i class="bi bi-eye"></i>
                                </a>


                                @can('student-document.edit')

                                    <a
                                        href="{{ route('student-documents.edit', $document) }}"
                                        class="btn btn-sm btn-outline-warning"
                                        title="Edit"
                                    >
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                @endcan


                                @can('student-document.delete')

                                    <form
                                        action="{{ route('student-documents.destroy', $document) }}"
                                        method="POST"
                                        onsubmit="return confirm('Delete this document?')"
                                    >

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-outline-danger"
                                            title="Delete"
                                        >
                                            <i class="bi bi-trash"></i>
                                        </button>

                                    </form>

                                @endcan

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="7"
                            class="text-center py-5 text-muted"
                        >
                            No documents uploaded for this student.
                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection