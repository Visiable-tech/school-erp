@extends('layouts.admin')

@section('title', 'Student De-registration')

@section('content')

<div class="container-fluid">

    <div class="mb-4">

        <h4 class="mb-1">
            Student De-registration
        </h4>

        <div class="text-muted">
            De-register students while preserving their academic history.
        </div>

    </div>


    {{-- =========================================================
        MESSAGES
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


    @if($errors->any())

        <div class="alert alert-danger">

            <strong>
                Please correct the following:
            </strong>

            <ul class="mb-0 mt-2">

                @foreach($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- =========================================================
        FILTER
    ========================================================== --}}

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white py-3">

            <strong>
                <i class="bi bi-funnel me-1"></i>
                Find Students
            </strong>

        </div>


        <div class="card-body">

            <form
                method="GET"
                action="{{
                    route(
                        'student-management.deregistration'
                    )
                }}"
            >

                <div class="row g-3">

                    <div class="col-lg-3 col-md-6">

                        <label class="form-label">
                            Academic Year
                            <span class="text-danger">*</span>
                        </label>

                        <select
                            name="academic_year_id"
                            id="academic_year_id"
                            class="form-select"
                            required
                        >

                            <option value="">
                                Select Academic Year
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


                    <div class="col-lg-3 col-md-6">

                        <label class="form-label">
                            Class
                            <span class="text-danger">*</span>
                        </label>

                        <select
                            name="school_class_id"
                            id="school_class_id"
                            class="form-select"
                            required
                        >

                            <option value="">
                                Select Class
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


                    <div class="col-lg-3 col-md-6">

                        <label class="form-label">
                            Section
                            <span class="text-danger">*</span>
                        </label>

                        <select
                            name="section_id"
                            id="section_id"
                            class="form-select"
                            required
                        >

                            <option value="">
                                Select Section
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

                            <span
                                class="spinner-border spinner-border-sm"
                            ></span>

                            Loading sections...

                        </div>

                    </div>


                    <div class="col-lg-3 col-md-6">

                        <label class="form-label">
                            Search
                        </label>

                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            class="form-control"
                            placeholder="Name / Admission No / Mobile"
                        >

                    </div>


                    <div class="col-12">

                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            <i class="bi bi-search me-1"></i>
                            Show Students
                        </button>

                        <a
                            href="{{
                                route(
                                    'student-management.deregistration'
                                )
                            }}"
                            class="btn btn-outline-secondary"
                        >
                            Reset
                        </a>

                    </div>

                </div>

            </form>

        </div>

    </div>


    {{-- =========================================================
        STUDENT LIST
    ========================================================== --}}

    @if(
        request()->filled('academic_year_id')
        &&
        request()->filled('school_class_id')
        &&
        request()->filled('section_id')
    )

        <form
            method="POST"
            action="{{
                route(
                    'student-management.deregistration.store'
                )
            }}"
            id="deregistrationForm"
        >

            @csrf

            <input
                type="hidden"
                name="academic_year_id"
                value="{{ request('academic_year_id') }}"
            >

            <input
                type="hidden"
                name="school_class_id"
                value="{{ request('school_class_id') }}"
            >

            <input
                type="hidden"
                name="section_id"
                value="{{ request('section_id') }}"
            >


            <div class="card border-0 shadow-sm mb-4">

                <div class="card-header bg-white py-3">

                    <div class="d-flex justify-content-between">

                        <strong>
                            Student List
                        </strong>

                        <div>

                            Total:
                            <strong>
                                {{ $enrollments->count() }}
                            </strong>

                            &nbsp; | &nbsp;

                            Selected:
                            <strong
                                id="selectedCount"
                                class="text-primary"
                            >
                                0
                            </strong>

                        </div>

                    </div>

                </div>


                <div class="card-body p-0">

                    <div class="student-table">

                        <table
                            class="table table-bordered table-striped table-hover align-middle mb-0"
                        >

                            <thead class="table-dark">

                                <tr>

                                    <th
                                        class="text-center"
                                        style="width:60px;"
                                    >

                                        <input
                                            type="checkbox"
                                            id="selectAll"
                                            class="form-check-input"
                                        >

                                    </th>

                                    <th>#</th>

                                    <th>
                                        Admission No.
                                    </th>

                                    <th>
                                        Student Name
                                    </th>

                                    <th>
                                        Father
                                    </th>

                                    <th>
                                        Roll No.
                                    </th>

                                    <th>
                                        Class
                                    </th>

                                    <th>
                                        Section
                                    </th>

                                    <th>
                                        Type
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                @forelse($enrollments as $enrollment)

                                    <tr>

                                        <td class="text-center">

                                            <input
                                                type="checkbox"
                                                name="enrollment_ids[]"
                                                value="{{ $enrollment->id }}"
                                                class="form-check-input student-checkbox"
                                            >

                                        </td>

                                        <td>
                                            {{ $loop->iteration }}
                                        </td>

                                        <td>

                                            <strong class="text-primary">

                                                {{
                                                    $enrollment
                                                        ->student
                                                        ?->admission_no
                                                    ?? '-'
                                                }}

                                            </strong>

                                        </td>

                                        <td>

                                            <strong>

                                                {{
                                                    $enrollment
                                                        ->student
                                                        ?->student_name
                                                    ?? '-'
                                                }}

                                            </strong>

                                        </td>

                                        <td>

                                            {{
                                                $enrollment
                                                    ->student
                                                    ?->father_name
                                                ?? '-'
                                            }}

                                        </td>

                                        <td>

                                            {{
                                                $enrollment->roll_no
                                                ?? '-'
                                            }}

                                        </td>

                                        <td>

                                            {{
                                                $enrollment
                                                    ->schoolClass
                                                    ?->name
                                                ?? '-'
                                            }}

                                        </td>

                                        <td>

                                            {{
                                                $enrollment
                                                    ->section
                                                    ?->name
                                                ?? '-'
                                            }}

                                        </td>

                                        <td>

                                            @if(
                                                $enrollment->studentType
                                            )

                                                <span
                                                    class="badge bg-info text-dark"
                                                >

                                                    {{
                                                        $enrollment
                                                            ->studentType
                                                            ->name
                                                    }}

                                                </span>

                                            @else

                                                -

                                            @endif

                                        </td>

                                    </tr>

                                @empty

                                    <tr>

                                        <td
                                            colspan="9"
                                            class="text-center text-muted py-5"
                                        >

                                            No active students found.

                                        </td>

                                    </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>


            {{-- =================================================
                DE-REGISTRATION DETAILS
            ================================================== --}}

            @if($enrollments->count())

                <div class="card border-0 shadow-sm mb-4">

                    <div class="card-header bg-white py-3">

                        <strong>
                            De-registration Details
                        </strong>

                    </div>


                    <div class="card-body">

                        <div class="row g-3">

                            <div class="col-lg-3 col-md-6">

                                <label class="form-label">

                                    De-registration Date

                                    <span class="text-danger">
                                        *
                                    </span>

                                </label>

                                <input
                                    type="date"
                                    name="deregistration_date"
                                    class="form-control"
                                    value="{{
                                        old(
                                            'deregistration_date',
                                            date('Y-m-d')
                                        )
                                    }}"
                                    required
                                >

                            </div>


                            <div class="col-lg-9">

                                <label class="form-label">

                                    Reason

                                    <span class="text-danger">
                                        *
                                    </span>

                                </label>

                                <input
                                    type="text"
                                    name="reason"
                                    class="form-control"
                                    value="{{ old('reason') }}"
                                    placeholder="Reason for de-registration"
                                    maxlength="255"
                                    required
                                >

                            </div>


                            <div class="col-12">

                                <label class="form-label">
                                    Remarks
                                </label>

                                <textarea
                                    name="remarks"
                                    class="form-control"
                                    rows="3"
                                    placeholder="Additional remarks"
                                >{{ old('remarks') }}</textarea>

                            </div>

                        </div>

                    </div>


                    <div class="card-footer bg-white">

                        <button
                            type="submit"
                            class="btn btn-danger"
                        >

                            <i class="bi bi-person-dash me-1"></i>

                            De-register Selected Students

                        </button>

                    </div>

                </div>

            @endif

        </form>

    @endif


    {{-- =========================================================
        HISTORY
    ========================================================== --}}

    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white py-3">

            <strong>
                De-registration History
            </strong>

        </div>


        <div class="card-body p-0">

            <div class="table-responsive">

                <table
                    class="table table-bordered table-striped table-hover align-middle mb-0"
                >

                    <thead class="table-dark">

                        <tr>

                            <th>#</th>
                            <th>Admission No.</th>
                            <th>Student</th>
                            <th>Academic Year</th>
                            <th>Class</th>
                            <th>Section</th>
                            <th>Date</th>
                            <th>Reason</th>
                            <th>Remarks</th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse(
                            $deregistrations as $item
                        )

                            <tr>

                                <td>

                                    {{
                                        $deregistrations->firstItem()
                                        +
                                        $loop->index
                                    }}

                                </td>

                                <td>

                                    {{
                                        $item
                                            ->student
                                            ?->admission_no
                                        ?? '-'
                                    }}

                                </td>

                                <td>

                                    <strong>

                                        {{
                                            $item
                                                ->student
                                                ?->student_name
                                            ?? '-'
                                        }}

                                    </strong>

                                </td>

                                <td>

                                    {{
                                        $item
                                            ->enrollment
                                            ?->academicYear
                                            ?->name
                                        ?? '-'
                                    }}

                                </td>

                                <td>

                                    {{
                                        $item
                                            ->enrollment
                                            ?->schoolClass
                                            ?->name
                                        ?? '-'
                                    }}

                                </td>

                                <td>

                                    {{
                                        $item
                                            ->enrollment
                                            ?->section
                                            ?->name
                                        ?? '-'
                                    }}

                                </td>

                                <td>

                                    {{
                                        $item
                                            ->deregistration_date
                                            ?->format('d-m-Y')
                                    }}

                                </td>

                                <td>
                                    {{ $item->reason }}
                                </td>

                                <td>
                                    {{ $item->remarks ?? '-' }}
                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="9"
                                    class="text-center text-muted py-4"
                                >

                                    No de-registration records found.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        @if($deregistrations->hasPages())

            <div class="card-footer bg-white">

                {{
                    $deregistrations
                        ->links()
                }}

            </div>

        @endif

    </div>

</div>


<style>

.student-table {
    max-height: 700px;
    overflow: auto;
}

.student-table table {
    min-width: 1100px;
}

.student-table thead th {
    position: sticky;
    top: 0;
    z-index: 10;
    white-space: nowrap;
}

.student-checkbox,
#selectAll {
    cursor: pointer;
}

</style>


<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        /*
        |--------------------------------------------------------------------------
        | SECTION AJAX
        |--------------------------------------------------------------------------
        */

        const year =
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

        const currentSection =
            "{{ request('section_id') }}";


        function loadSections(
            keepSelected = false
        ) {

            section.innerHTML =
                '<option value="">Select Section</option>';


            if (
                !year.value
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
                "{{ route('student-management.get-sections') }}"
                +
                '?academic_year_id='
                +
                encodeURIComponent(
                    year.value
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

            .then(
                response => {

                    if (!response.ok) {

                        throw new Error(
                            'Unable to load sections.'
                        );
                    }

                    return response.json();
                }
            )

            .then(
                data => {

                    section.innerHTML =
                        '<option value="">Select Section</option>';


                    const sections =
                        data.sections || [];


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
                                String(currentSection)
                            ) {

                                option.selected =
                                    true;
                            }


                            section.appendChild(
                                option
                            );

                        }
                    );

                }
            )

            .catch(
                error => {

                    console.error(error);

                    section.innerHTML =
                        '<option value="">Unable to load sections</option>';
                }
            )

            .finally(
                function () {

                    section.disabled =
                        false;

                    loading.classList.add(
                        'd-none'
                    );
                }
            );
        }


        if (
            year &&
            schoolClass &&
            section
        ) {

            year.addEventListener(
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


            if (
                year.value &&
                schoolClass.value
            ) {

                loadSections(true);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | SELECT ALL
        |--------------------------------------------------------------------------
        */

        const selectAll =
            document.getElementById(
                'selectAll'
            );

        const checkboxes =
            document.querySelectorAll(
                '.student-checkbox'
            );

        const selectedCount =
            document.getElementById(
                'selectedCount'
            );


        function updateSelectedCount()
        {

            const checked =
                document.querySelectorAll(
                    '.student-checkbox:checked'
                );


            if (selectedCount) {

                selectedCount.textContent =
                    checked.length;
            }


            if (selectAll) {

                selectAll.checked =
                    checkboxes.length > 0
                    &&
                    checked.length
                    ===
                    checkboxes.length;


                selectAll.indeterminate =
                    checked.length > 0
                    &&
                    checked.length
                    <
                    checkboxes.length;
            }
        }


        if (selectAll) {

            selectAll.addEventListener(
                'change',
                function () {

                    checkboxes.forEach(
                        function (checkbox) {

                            checkbox.checked =
                                selectAll.checked;
                        }
                    );


                    updateSelectedCount();

                }
            );
        }


        checkboxes.forEach(
            function (checkbox) {

                checkbox.addEventListener(
                    'change',
                    updateSelectedCount
                );

            }
        );


        /*
        |--------------------------------------------------------------------------
        | SUBMIT
        |--------------------------------------------------------------------------
        */

        const form =
            document.getElementById(
                'deregistrationForm'
            );


        if (form) {

            form.addEventListener(
                'submit',
                function (event) {

                    const selected =
                        document.querySelectorAll(
                            '.student-checkbox:checked'
                        );


                    if (
                        selected.length === 0
                    ) {

                        event.preventDefault();

                        alert(
                            'Please select at least one student.'
                        );

                        return;
                    }


                    const message =
                        'De-register '
                        +
                        selected.length
                        +
                        ' selected student(s)?\n\n'
                        +
                        'Their current enrollment will be closed, '
                        +
                        'but their academic history will be preserved.';


                    if (!confirm(message)) {

                        event.preventDefault();
                    }

                }
            );
        }


        updateSelectedCount();

    }
);

</script>

@endsection