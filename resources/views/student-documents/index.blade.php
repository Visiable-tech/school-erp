@extends('layouts.admin')

@section('title', 'Student Documents')

@section('content')

<div class="container-fluid">

    {{-- =========================================================
        PAGE HEADER
    ========================================================== --}}

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


    {{-- =========================================================
        ALERT
    ========================================================== --}}

    @if(session('success'))

        <div class="alert alert-success alert-dismissible fade show">

            {{ session('success') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>

        </div>

    @endif


    {{-- =========================================================
        SEARCH / FILTER
    ========================================================== --}}

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white py-3">

            <strong>
                <i class="bi bi-funnel me-1"></i>
                Search & Filter Documents
            </strong>

        </div>


        <div class="card-body">

            <form
                method="GET"
                action="{{ route('student-documents.index') }}"
            >

                <div class="row g-3">


                    {{-- Academic Year --}}

                    <div class="col-lg-3 col-md-6">

                        <label class="form-label">
                            Academic Year
                        </label>

                        <select
                            name="academic_year_id"
                            id="academic_year_id"
                            class="form-select"
                        >

                            <option value="">
                                All Academic Years
                            </option>

                            @foreach($academicYears as $year)

                                <option
                                    value="{{ $year->id }}"
                                    @selected(
                                        request('academic_year_id')
                                        == $year->id
                                    )
                                >
                                    {{ $year->name }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Class --}}

                    <div class="col-lg-3 col-md-6">

                        <label class="form-label">
                            Class
                        </label>

                        <select
                            name="school_class_id"
                            id="school_class_id"
                            class="form-select"
                        >

                            <option value="">
                                All Classes
                            </option>

                            @foreach($classes as $class)

                                <option
                                    value="{{ $class->id }}"
                                    @selected(
                                        request('school_class_id')
                                        == $class->id
                                    )
                                >
                                    {{ $class->name }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Section --}}

                    <div class="col-lg-3 col-md-6">

                        <label class="form-label">
                            Section
                        </label>

                        <select
                            name="section_id"
                            id="section_id"
                            class="form-select"
                        >

                            <option value="">
                                All Sections
                            </option>

                            @foreach($sections as $section)

                                <option
                                    value="{{ $section->id }}"
                                    @selected(
                                        request('section_id')
                                        == $section->id
                                    )
                                >
                                    {{ $section->name }}
                                </option>

                            @endforeach

                        </select>

                        <div
                            id="sectionLoading"
                            class="small text-primary mt-1 d-none"
                        >
                            Loading sections...
                        </div>

                    </div>


                    {{-- Document Type --}}

                    <div class="col-lg-3 col-md-6">

                        <label class="form-label">
                            Document Type
                        </label>

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
                                        request('document_type')
                                        === $key
                                    )
                                >
                                    {{ $label }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Search --}}

                    <div class="col-lg-6 col-md-6">

                        <label class="form-label">
                            Search
                        </label>

                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            class="form-control"
                            placeholder="Student name, admission no, father name, mobile, document name or number"
                        >

                    </div>


                    {{-- Status --}}

                    <div class="col-lg-3 col-md-3">

                        <label class="form-label">
                            Status
                        </label>

                        <select
                            name="status"
                            class="form-select"
                        >

                            <option value="">
                                All Status
                            </option>

                            <option
                                value="1"
                                @selected(request('status') === '1')
                            >
                                Active
                            </option>

                            <option
                                value="0"
                                @selected(request('status') === '0')
                            >
                                Inactive
                            </option>

                        </select>

                    </div>


                    {{-- Buttons --}}

                    <div class="col-lg-3 col-md-3 d-flex align-items-end">

                        <button
                            type="submit"
                            class="btn btn-primary me-2"
                        >
                            <i class="bi bi-search me-1"></i>
                            Search
                        </button>

                        <a
                            href="{{ route('student-documents.index') }}"
                            class="btn btn-outline-secondary"
                        >
                            <i class="bi bi-arrow-counterclockwise me-1"></i>
                            Reset
                        </a>

                    </div>

                </div>

            </form>

        </div>

    </div>


    {{-- =========================================================
        RESULT SUMMARY
    ========================================================== --}}

    <div class="d-flex justify-content-between align-items-center mb-2">

        <div class="text-muted">

            @if($documents->total())

                Showing
                <strong>{{ $documents->firstItem() }}</strong>
                -
                <strong>{{ $documents->lastItem() }}</strong>
                of
                <strong>{{ $documents->total() }}</strong>
                documents

            @else

                No documents found

            @endif

        </div>

    </div>


    {{-- =========================================================
        DOCUMENT TABLE
    ========================================================== --}}

    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white py-3">

            <div class="d-flex justify-content-between align-items-center">

                <strong>
                    Student Document List
                </strong>

                <span class="badge bg-secondary">
                    {{ $documents->total() }} Records
                </span>

            </div>

        </div>


        <div class="card-body p-0">

            <div class="table-responsive">

                <table
                    class="table table-bordered table-striped table-hover align-middle mb-0"
                >

                    <thead class="table-dark">

                        <tr>

                            <th style="width:60px;">
                                #
                            </th>

                            <th>
                                Admission No.
                            </th>

                            <th>
                                Student
                            </th>

                            <th>
                                Class
                            </th>

                            <th>
                                Section
                            </th>

                            <th>
                                Document
                            </th>

                            <th>
                                Number
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Uploaded
                            </th>

                            <th style="width:160px;">
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($documents as $document)

                            @php

                                $enrollment =
                                    $document
                                        ->student
                                        ?->currentEnrollment;

                            @endphp


                            <tr>

                                {{-- Serial --}}

                                <td>

                                    {{
                                        $documents->firstItem()
                                        +
                                        $loop->index
                                    }}

                                </td>


                                {{-- Admission No --}}

                                <td>

                                    <strong class="text-primary">

                                        {{
                                            $document
                                                ->student
                                                ?->admission_no
                                            ?? '-'
                                        }}

                                    </strong>

                                </td>


                                {{-- Student --}}

                                <td>

                                    @if($document->student)

                                        <a
                                            href="{{
                                                route(
                                                    'students.show',
                                                    $document->student
                                                )
                                            }}"
                                            class="text-decoration-none fw-semibold"
                                        >
                                            {{
                                                $document
                                                    ->student
                                                    ->student_name
                                            }}
                                        </a>

                                        @if(
                                            $document
                                                ->student
                                                ->father_name
                                        )

                                            <div class="small text-muted">

                                                Father:
                                                {{
                                                    $document
                                                        ->student
                                                        ->father_name
                                                }}

                                            </div>

                                        @endif

                                    @else

                                        -

                                    @endif

                                </td>


                                {{-- Class --}}

                                <td>

                                    {{
                                        $enrollment
                                            ?->schoolClass
                                            ?->name
                                        ?? '-'
                                    }}

                                </td>


                                {{-- Section --}}

                                <td>

                                    {{
                                        $enrollment
                                            ?->section
                                            ?->name
                                        ?? '-'
                                    }}

                                </td>


                                {{-- Document --}}

                                <td>

                                    <div class="fw-semibold">

                                        {{
                                            $document
                                                ->document_name
                                        }}

                                    </div>


                                    <small class="text-muted">

                                        {{
                                            ucwords(
                                                str_replace(
                                                    '_',
                                                    ' ',
                                                    $document
                                                        ->document_type
                                                )
                                            )
                                        }}

                                    </small>

                                </td>


                                {{-- Document Number --}}

                                <td>

                                    {{
                                        $document
                                            ->document_number
                                        ?? '-'
                                    }}

                                </td>


                                {{-- Status --}}

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


                                {{-- Uploaded Date --}}

                                <td>

                                    {{
                                        $document
                                            ->created_at
                                            ?->format('d M Y')
                                    }}

                                    @if($document->uploader)

                                        <div class="small text-muted">

                                            By:
                                            {{
                                                $document
                                                    ->uploader
                                                    ->name
                                            }}

                                        </div>

                                    @endif

                                </td>


                                {{-- Actions --}}

                                <td>

                                    <div class="d-flex gap-1">

                                        {{-- View File --}}

                                        <a
                                            href="{{
                                                asset(
                                                    'storage/'
                                                    .
                                                    $document
                                                        ->file_path
                                                )
                                            }}"
                                            target="_blank"
                                            class="btn btn-sm btn-outline-primary"
                                            title="View Document"
                                        >
                                            <i class="bi bi-eye"></i>
                                        </a>


                                        {{-- Manage --}}

                                        <a
                                            href="{{
                                                route(
                                                    'student-documents.student',
                                                    $document->student
                                                )
                                            }}"
                                            class="btn btn-sm btn-outline-secondary"
                                            title="Manage Documents"
                                        >
                                            <i class="bi bi-folder2-open"></i>
                                        </a>


                                        {{-- Edit --}}

                                        @can('student-document.edit')

                                            <a
                                                href="{{
                                                    route(
                                                        'student-documents.edit',
                                                        $document
                                                    )
                                                }}"
                                                class="btn btn-sm btn-outline-warning"
                                                title="Edit Document"
                                            >
                                                <i class="bi bi-pencil"></i>
                                            </a>

                                        @endcan

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="10"
                                    class="text-center py-5"
                                >

                                    <i
                                        class="bi bi-folder-x fs-1 text-muted"
                                    ></i>

                                    <div class="mt-2 fw-semibold">
                                        No Student Documents Found
                                    </div>

                                    <div class="text-muted small">
                                        Try changing your search or filter criteria.
                                    </div>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        {{-- Pagination --}}

        @if($documents->hasPages())

            <div class="card-footer bg-white">

                {{
                    $documents->links()
                }}

            </div>

        @endif

    </div>

</div>


{{-- =============================================================
    SECTION AJAX
============================================================= --}}

<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const academicYear =
            document.getElementById(
                'academic_year_id'
            );

        const schoolClass =
            document.getElementById(
                'school_class_id'
            );

        const section =
            document.getElementById(
                'section_id'
            );

        const loading =
            document.getElementById(
                'sectionLoading'
            );

        const selectedSection =
            "{{ request('section_id') }}";


        function loadSections(
            keepSelected = false
        ) {

            section.innerHTML =
                '<option value="">All Sections</option>';


            if (
                !academicYear.value
                ||
                !schoolClass.value
            ) {

                return;
            }


            section.disabled = true;

            loading.classList.remove(
                'd-none'
            );


            const url =
                "{{ route('student-documents.get-sections') }}"
                +
                '?academic_year_id='
                +
                encodeURIComponent(
                    academicYear.value
                )
                +
                '&school_class_id='
                +
                encodeURIComponent(
                    schoolClass.value
                );


            fetch(
                url,
                {
                    headers: {

                        'Accept':
                            'application/json',

                        'X-Requested-With':
                            'XMLHttpRequest'

                    }
                }
            )

            .then(function (response) {

                if (!response.ok) {

                    throw new Error(
                        'Unable to load sections.'
                    );

                }

                return response.json();

            })

            .then(function (sections) {

                section.innerHTML =
                    '<option value="">All Sections</option>';


                sections.forEach(
                    function (item) {

                        const option =
                            document.createElement(
                                'option'
                            );

                        option.value =
                            item.id;

                        option.textContent =
                            item.name;


                        if (
                            keepSelected
                            &&
                            String(item.id)
                            ===
                            String(selectedSection)
                        ) {

                            option.selected =
                                true;

                        }


                        section.appendChild(
                            option
                        );

                    }
                );

            })

            .catch(function (error) {

                console.error(error);

                section.innerHTML =
                    '<option value="">Unable to load sections</option>';

            })

            .finally(function () {

                section.disabled =
                    false;

                loading.classList.add(
                    'd-none'
                );

            });

        }


        academicYear.addEventListener(
            'change',
            function () {

                loadSections(false);

            }
        );


        schoolClass.addEventListener(
            'change',
            function () {

                loadSections(false);

            }
        );


        /*
        |--------------------------------------------------------------------------
        | Restore Section After Search
        |--------------------------------------------------------------------------
        */

        if (
            academicYear.value
            &&
            schoolClass.value
        ) {

            loadSections(true);

        }

    }
);

</script>

@endsection