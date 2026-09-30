@extends('layouts.admin')

@section('title', 'Student Suspension')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h4 class="mb-1">Student Suspension</h4>

            <div class="text-muted">
                Suspend one or multiple students and maintain suspension history.
            </div>
        </div>

    </div>


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


    @if(session('error'))

        <div class="alert alert-danger alert-dismissible fade show">

            {{ session('error') }}

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

                    <li>{{ $error }}</li>

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
                action="{{ route('student-management.suspension') }}"
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
                            <span class="spinner-border spinner-border-sm"></span>
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
                            href="{{ route('student-management.suspension') }}"
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
        STUDENTS
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
            action="{{ route('student-management.suspension.store') }}"
            id="suspensionForm"
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

                    <div class="d-flex justify-content-between flex-wrap gap-2">

                        <strong>
                            Students
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

                                    <th class="text-center">

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
                                        Roll
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

                                            @if(
                                                $enrollment
                                                    ->student
                                                    ?->father_name
                                            )

                                                <div class="small text-muted">

                                                    Father:
                                                    {{
                                                        $enrollment
                                                            ->student
                                                            ->father_name
                                                    }}

                                                </div>

                                            @endif

                                        </td>

                                        <td>
                                            {{ $enrollment->roll_no ?? '-' }}
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

                                            @if($enrollment->studentType)

                                                <span class="badge bg-info text-dark">

                                                    {{
                                                        $enrollment
                                                            ->studentType
                                                            ->name
                                                    }}

                                                </span>

                                            @else

                                                <span class="text-muted">
                                                    -
                                                </span>

                                            @endif

                                        </td>

                                    </tr>

                                @empty

                                    <tr>

                                        <td
                                            colspan="8"
                                            class="text-center text-muted py-5"
                                        >
                                            No students found.
                                        </td>

                                    </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>


            {{-- =================================================
                SUSPENSION DETAILS
            ================================================== --}}

            @if($enrollments->count())

                <div class="card border-0 shadow-sm mb-4">

                    <div class="card-header bg-white py-3">

                        <strong>
                            Suspension Details
                        </strong>

                    </div>

                    <div class="card-body">

                        <div class="row g-3">

                            <div class="col-lg-3 col-md-6">

                                <label class="form-label">
                                    Suspension From
                                    <span class="text-danger">*</span>
                                </label>

                                <input
                                    type="date"
                                    name="suspension_from"
                                    class="form-control"
                                    value="{{
                                        old(
                                            'suspension_from',
                                            date('Y-m-d')
                                        )
                                    }}"
                                    required
                                >

                            </div>


                            <div class="col-lg-3 col-md-6">

                                <label class="form-label">
                                    Suspension To
                                </label>

                                <input
                                    type="date"
                                    name="suspension_to"
                                    class="form-control"
                                    value="{{ old('suspension_to') }}"
                                >

                                <div class="form-text">
                                    Leave blank for indefinite suspension.
                                </div>

                            </div>


                            <div class="col-lg-6">

                                <label class="form-label">
                                    Reason
                                    <span class="text-danger">*</span>
                                </label>

                                <input
                                    type="text"
                                    name="reason"
                                    class="form-control"
                                    value="{{ old('reason') }}"
                                    placeholder="Enter suspension reason"
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
                            <i class="bi bi-person-x me-1"></i>

                            Suspend Selected Students
                        </button>

                    </div>

                </div>

            @endif

        </form>

    @endif


    {{-- =========================================================
        ACTIVE SUSPENSIONS
    ========================================================== --}}

    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white py-3">

            <strong>
                <i class="bi bi-exclamation-triangle me-1"></i>
                Active Suspensions
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
                            <th>Class</th>
                            <th>Section</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Reason</th>
                            <th width="120">Action</th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse(
                            $activeSuspensions as $suspension
                        )

                            <tr>

                                <td>
                                    {{ $loop->iteration }}
                                </td>

                                <td>

                                    {{
                                        $suspension
                                            ->student
                                            ?->admission_no
                                        ?? '-'
                                    }}

                                </td>

                                <td>

                                    <strong>

                                        {{
                                            $suspension
                                                ->student
                                                ?->student_name
                                            ?? '-'
                                        }}

                                    </strong>

                                </td>

                                <td>

                                    {{
                                        $suspension
                                            ->enrollment
                                            ?->schoolClass
                                            ?->name
                                        ?? '-'
                                    }}

                                </td>

                                <td>

                                    {{
                                        $suspension
                                            ->enrollment
                                            ?->section
                                            ?->name
                                        ?? '-'
                                    }}

                                </td>

                                <td>

                                    {{
                                        $suspension
                                            ->suspension_from
                                            ?->format('d-m-Y')
                                    }}

                                </td>

                                <td>

                                    {{
                                        $suspension
                                            ->suspension_to
                                            ?->format('d-m-Y')
                                        ?? 'Until Revoked'
                                    }}

                                </td>

                                <td>

                                    {{ $suspension->reason }}

                                </td>

                                <td>

                                    <button
                                        type="button"
                                        class="btn btn-sm btn-success"
                                        data-bs-toggle="modal"
                                        data-bs-target="#revokeModal{{ $suspension->id }}"
                                    >
                                        Revoke
                                    </button>

                                </td>

                            </tr>


                            {{-- REVOKE MODAL --}}

                            <div
                                class="modal fade"
                                id="revokeModal{{ $suspension->id }}"
                                tabindex="-1"
                            >

                                <div class="modal-dialog">

                                    <div class="modal-content">

                                        <form
                                            method="POST"
                                            action="{{
                                                route(
                                                    'student-management.suspension.revoke',
                                                    $suspension
                                                )
                                            }}"
                                        >

                                            @csrf

                                            <div class="modal-header">

                                                <h5 class="modal-title">
                                                    Revoke Suspension
                                                </h5>

                                                <button
                                                    type="button"
                                                    class="btn-close"
                                                    data-bs-dismiss="modal"
                                                ></button>

                                            </div>

                                            <div class="modal-body">

                                                <p>

                                                    Revoke suspension for

                                                    <strong>

                                                        {{
                                                            $suspension
                                                                ->student
                                                                ?->student_name
                                                        }}

                                                    </strong>?

                                                </p>

                                                <label class="form-label">
                                                    Remarks
                                                </label>

                                                <textarea
                                                    name="revocation_remarks"
                                                    class="form-control"
                                                    rows="3"
                                                ></textarea>

                                            </div>

                                            <div class="modal-footer">

                                                <button
                                                    type="button"
                                                    class="btn btn-secondary"
                                                    data-bs-dismiss="modal"
                                                >
                                                    Cancel
                                                </button>

                                                <button
                                                    type="submit"
                                                    class="btn btn-success"
                                                >
                                                    Revoke Suspension
                                                </button>

                                            </div>

                                        </form>

                                    </div>

                                </div>

                            </div>

                        @empty

                            <tr>

                                <td
                                    colspan="9"
                                    class="text-center text-muted py-4"
                                >
                                    No active suspensions.
                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>


<style>

.student-table {
    max-height: 700px;
    overflow: auto;
}

.student-table table {
    min-width: 1000px;
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

        const selectedSection =
            "{{ request('section_id') }}";


        /*
        |--------------------------------------------------------------------------
        | LOAD SECTIONS
        |--------------------------------------------------------------------------
        */

        function loadSections(
            keepSelected = false
        ) {

            if (
                !year ||
                !schoolClass ||
                !section
            ) {
                return;
            }


            section.innerHTML =
                '<option value="">Select Section</option>';


            if (
                !year.value ||
                !schoolClass.value
            ) {
                return;
            }


            section.disabled =
                true;

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


                    /*
                     * StudentManagementController returns:
                     *
                     * {
                     *     success: true,
                     *     sections: [...]
                     * }
                     */

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


        if (year && schoolClass) {

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
        | BULK CHECKBOX
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


        function updateCount()
        {
            const checked =
                document.querySelectorAll(
                    '.student-checkbox:checked'
                ).length;


            if (selectedCount) {

                selectedCount.textContent =
                    checked;
            }


            if (selectAll) {

                selectAll.checked =
                    checkboxes.length > 0
                    &&
                    checked === checkboxes.length;

                selectAll.indeterminate =
                    checked > 0
                    &&
                    checked < checkboxes.length;
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

                    updateCount();
                }
            );
        }


        checkboxes.forEach(
            function (checkbox) {

                checkbox.addEventListener(
                    'change',
                    updateCount
                );
            }
        );


        /*
        |--------------------------------------------------------------------------
        | FORM CONFIRMATION
        |--------------------------------------------------------------------------
        */

        const form =
            document.getElementById(
                'suspensionForm'
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


                    if (
                        !confirm(
                            'Suspend '
                            +
                            selected.length
                            +
                            ' selected student(s)?'
                        )
                    ) {

                        event.preventDefault();
                    }
                }
            );
        }


        updateCount();

    }
);

</script>

@endsection