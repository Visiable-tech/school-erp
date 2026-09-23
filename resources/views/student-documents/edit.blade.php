@extends('layouts.admin')

@section('title', 'Edit Student Document')

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
            Edit Student Document
        </h4>

        <div class="text-muted">
            {{ $studentDocument->student->student_name }}
            —
            {{ $studentDocument->student->admission_no }}
        </div>

    </div>


    <a
        href="{{ route(
            'student-documents.student',
            $studentDocument->student
        ) }}"
        class="btn btn-outline-secondary"
    >
        Back
    </a>

</div>


<div class="card border-0 shadow-sm">

    <div class="card-body">

        <form
            action="{{ route(
                'student-documents.update',
                $studentDocument
            ) }}"
            method="POST"
            enctype="multipart/form-data"
        >

            @csrf
            @method('PUT')


            <div class="row g-3">


                <div class="col-md-4">

                    <label class="form-label">
                        Document Type *
                    </label>

                    <select
                        name="document_type"
                        class="form-select"
                        required
                    >

                        @foreach($documentTypes as $key => $label)

                            <option
                                value="{{ $key }}"
                                @selected(
                                    old(
                                        'document_type',
                                        $studentDocument->document_type
                                    ) === $key
                                )
                            >
                                {{ $label }}
                            </option>

                        @endforeach

                    </select>

                </div>


                <div class="col-md-4">

                    <label class="form-label">
                        Document Name *
                    </label>

                    <input
                        type="text"
                        name="document_name"
                        value="{{ old(
                            'document_name',
                            $studentDocument->document_name
                        ) }}"
                        class="form-control"
                        required
                    >

                </div>


                <div class="col-md-4">

                    <label class="form-label">
                        Document Number
                    </label>

                    <input
                        type="text"
                        name="document_number"
                        value="{{ old(
                            'document_number',
                            $studentDocument->document_number
                        ) }}"
                        class="form-control"
                    >

                </div>


                <div class="col-md-6">

                    <label class="form-label">
                        Replace File
                    </label>

                    <input
                        type="file"
                        name="file"
                        class="form-control"
                        accept=".pdf,.jpg,.jpeg,.png"
                    >

                    <small class="text-muted">
                        Leave empty to keep current file.
                    </small>

                </div>


                <div class="col-md-6">

                    <label class="form-label d-block">
                        Current File
                    </label>

                    <a
                        href="{{ asset(
                            'storage/'
                            . $studentDocument->file_path
                        ) }}"
                        target="_blank"
                        class="btn btn-outline-primary"
                    >
                        <i class="bi bi-eye"></i>
                        View Current Document
                    </a>

                </div>


                <div class="col-12">

                    <label class="form-label">
                        Remarks
                    </label>

                    <textarea
                        name="remarks"
                        class="form-control"
                        rows="3"
                    >{{ old(
                        'remarks',
                        $studentDocument->remarks
                    ) }}</textarea>

                </div>


                <div class="col-12">

                    <div class="form-check">

                        <input
                            type="hidden"
                            name="status"
                            value="0"
                        >

                        <input
                            class="form-check-input"
                            type="checkbox"
                            name="status"
                            value="1"
                            id="status"
                            @checked(
                                old(
                                    'status',
                                    $studentDocument->status
                                )
                            )
                        >

                        <label
                            class="form-check-label"
                            for="status"
                        >
                            Active
                        </label>

                    </div>

                </div>


                <div class="col-12">

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        Update Document
                    </button>


                    <a
                        href="{{ route(
                            'student-documents.student',
                            $studentDocument->student
                        ) }}"
                        class="btn btn-outline-secondary"
                    >
                        Cancel
                    </a>

                </div>

            </div>

        </form>

    </div>

</div>

@endsection