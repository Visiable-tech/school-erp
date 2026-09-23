@extends('layouts.admin')

@section('title', 'Student Documents')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h4 class="mb-1">
            Student Documents
        </h4>

        <div class="text-muted">
            Search and manage documents across all students.
        </div>

    </div>


    @can('student-document.create')

        <a
            href="{{ route('student-documents.create') }}"
            class="btn btn-primary"
        >

            <i class="bi bi-plus-circle me-1"></i>

            Add Document

        </a>

    @endcan

</div>

<div class="card border-0 shadow-sm mb-4">

    <div class="card-body">

        <form
            method="GET"
            action="{{ route('student-documents.index') }}"
        >

            <div class="row g-2">

                <div class="col-md-5">

                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        class="form-control"
                        placeholder="Student, admission no, document..."
                    >

                </div>


                <div class="col-md-4">

                    <select
                        name="document_type"
                        class="form-select"
                    >

                        <option value="">
                            All Document Types
                        </option>

                        @foreach([
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
                        ] as $key => $label)

                            <option
                                value="{{ $key }}"
                                @selected(
                                    request('document_type') === $key
                                )
                            >
                                {{ $label }}
                            </option>

                        @endforeach

                    </select>

                </div>


                <div class="col-md-3">

                    <button
                        class="btn btn-primary"
                    >
                        Search
                    </button>

                    <a
                        href="{{ route('student-documents.index') }}"
                        class="btn btn-outline-secondary"
                    >
                        Reset
                    </a>

                </div>

            </div>

        </form>

    </div>

</div>


<div class="card border-0 shadow-sm">

    <div class="card-body p-0">

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">

                    <tr>
                        <th>Admission No.</th>
                        <th>Student</th>
                        <th>Document</th>
                        <th>Number</th>
                        <th>Uploaded</th>
                        <th>Action</th>
                    </tr>

                </thead>


                <tbody>

                @forelse($documents as $document)

                    <tr>

                        <td>
                            {{ $document->student?->admission_no }}
                        </td>


                        <td>

                            <a href="{{ route(
                                'students.show',
                                $document->student
                            ) }}">
                                {{ $document->student?->student_name }}
                            </a>

                        </td>


                        <td>

                            <div class="fw-semibold">
                                {{ $document->document_name }}
                            </div>

                            <small class="text-muted">

                                {{
                                    ucwords(
                                        str_replace(
                                            '_',
                                            ' ',
                                            $document->document_type
                                        )
                                    )
                                }}

                            </small>

                        </td>


                        <td>
                            {{ $document->document_number ?? '-' }}
                        </td>


                        <td>
                            {{ $document->created_at->format('d M Y') }}
                        </td>


                        <td>

                            <a
                                href="{{ asset(
                                    'storage/'
                                    . $document->file_path
                                ) }}"
                                target="_blank"
                                class="btn btn-sm btn-outline-primary"
                            >
                                <i class="bi bi-eye"></i>
                            </a>


                            <a
                                href="{{ route(
                                    'student-documents.student',
                                    $document->student
                                ) }}"
                                class="btn btn-sm btn-outline-secondary"
                            >
                                Manage
                            </a>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="6"
                            class="text-center text-muted py-5"
                        >
                            No student documents found.
                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

    </div>


    @if($documents->hasPages())

        <div class="card-footer bg-white">

            {{ $documents->links() }}

        </div>

    @endif

</div>

@endsection