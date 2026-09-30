@extends('layouts.admin')

@section('title', 'Transfer Certificate')

@section('content')

<div class="container-fluid">

    <div class="mb-4">
        <h4 class="mb-1">
            Transfer Certificate
        </h4>

        <div class="text-muted">
            Create, review and issue student Transfer Certificates.
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
        FILTER STUDENT
    ========================================================== --}}

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white py-3">

            <strong>
                <i class="bi bi-search me-1"></i>
                Find Student
            </strong>

        </div>


        <div class="card-body">

            <form
                method="GET"
                action="{{
                    route(
                        'student-management.transfer-certificate'
                    )
                }}"
            >

                <div class="row g-3">

                    <div class="col-lg-3 col-md-6">

                        <label class="form-label">
                            Academic Year
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
                                    'student-management.transfer-certificate'
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
        STUDENTS
    ========================================================== --}}

    @if(
        request()->filled('academic_year_id')
        &&
        request()->filled('school_class_id')
        &&
        request()->filled('section_id')
    )

        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white py-3">

                <strong>
                    Student List
                </strong>

            </div>


            <div class="card-body p-0">

                <div
                    class="table-responsive"
                    style="max-height:600px;"
                >

                    <table
                        class="table table-bordered table-striped table-hover align-middle mb-0"
                    >

                        <thead class="table-dark sticky-top">

                            <tr>

                                <th>#</th>
                                <th>Admission No.</th>
                                <th>Student</th>
                                <th>Father</th>
                                <th>Roll</th>
                                <th>Class</th>
                                <th>Section</th>
                                <th width="110">Action</th>

                            </tr>

                        </thead>


                        <tbody>

                            @forelse($enrollments as $enrollment)

                                <tr>

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

                                        <button
                                            type="button"
                                            class="btn btn-sm btn-primary create-tc-btn"
                                            data-bs-toggle="modal"
                                            data-bs-target="#createTcModal"
                                            data-enrollment-id="{{ $enrollment->id }}"
                                            data-student-name="{{
                                                $enrollment
                                                    ->student
                                                    ?->student_name
                                            }}"
                                            data-admission-no="{{
                                                $enrollment
                                                    ->student
                                                    ?->admission_no
                                            }}"
                                        >
                                            Create TC
                                        </button>

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

    @endif


    {{-- =========================================================
        EXISTING TC
    ========================================================== --}}

    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white py-3">

            <strong>
                Transfer Certificate Register
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
                            <th>TC No.</th>
                            <th>Admission No.</th>
                            <th>Student</th>
                            <th>Class</th>
                            <th>Issue Date</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th width="190">Action</th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($certificates as $certificate)

                            <tr>

                                <td>

                                    {{
                                        $certificates->firstItem()
                                        +
                                        $loop->index
                                    }}

                                </td>

                                <td>

                                    <strong>
                                        {{ $certificate->tc_number }}
                                    </strong>

                                </td>

                                <td>

                                    {{
                                        $certificate
                                            ->student
                                            ?->admission_no
                                        ?? '-'
                                    }}

                                </td>

                                <td>

                                    {{
                                        $certificate
                                            ->student
                                            ?->student_name
                                        ?? '-'
                                    }}

                                </td>

                                <td>

                                    {{
                                        $certificate
                                            ->enrollment
                                            ?->schoolClass
                                            ?->name
                                        ?? '-'
                                    }}

                                </td>

                                <td>

                                    {{
                                        $certificate
                                            ->issue_date
                                            ?->format('d-m-Y')
                                    }}

                                </td>

                                <td>

                                    {{
                                        $certificate
                                            ->reason
                                            ?->name
                                        ?? '-'
                                    }}

                                </td>

                                <td>

                                    @if($certificate->status === 'draft')

                                        <span class="badge bg-warning text-dark">
                                            Draft
                                        </span>

                                    @elseif($certificate->status === 'issued')

                                        <span class="badge bg-success">
                                            Issued
                                        </span>

                                    @else

                                        <span class="badge bg-danger">
                                            Cancelled
                                        </span>

                                    @endif

                                </td>

                                <td>

                                    @if($certificate->status === 'draft')

                                        <form
                                            method="POST"
                                            action="{{
                                                route(
                                                    'student-management.transfer-certificate.issue',
                                                    $certificate
                                                )
                                            }}"
                                            class="d-inline"
                                            onsubmit="return confirm('Issue this Transfer Certificate? The current student enrollment will be closed.');"
                                        >

                                            @csrf

                                            <button
                                                type="submit"
                                                class="btn btn-sm btn-success"
                                            >
                                                Issue
                                            </button>

                                        </form>


                                        <button
                                            type="button"
                                            class="btn btn-sm btn-danger"
                                            data-bs-toggle="modal"
                                            data-bs-target="#cancelTcModal{{ $certificate->id }}"
                                        >
                                            Cancel
                                        </button>

                                    @else

                                        <span class="text-muted">
                                            -
                                        </span>

                                    @endif

                                </td>

                            </tr>


                            @if($certificate->status === 'draft')

                                <div
                                    class="modal fade"
                                    id="cancelTcModal{{ $certificate->id }}"
                                    tabindex="-1"
                                >

                                    <div class="modal-dialog">

                                        <div class="modal-content">

                                            <form
                                                method="POST"
                                                action="{{
                                                    route(
                                                        'student-management.transfer-certificate.cancel',
                                                        $certificate
                                                    )
                                                }}"
                                            >

                                                @csrf

                                                <div class="modal-header">

                                                    <h5 class="modal-title">
                                                        Cancel TC
                                                    </h5>

                                                    <button
                                                        type="button"
                                                        class="btn-close"
                                                        data-bs-dismiss="modal"
                                                    ></button>

                                                </div>


                                                <div class="modal-body">

                                                    <label class="form-label">
                                                        Cancellation Reason
                                                    </label>

                                                    <textarea
                                                        name="cancellation_reason"
                                                        class="form-control"
                                                        rows="4"
                                                        required
                                                    ></textarea>

                                                </div>


                                                <div class="modal-footer">

                                                    <button
                                                        type="button"
                                                        class="btn btn-secondary"
                                                        data-bs-dismiss="modal"
                                                    >
                                                        Close
                                                    </button>

                                                    <button
                                                        type="submit"
                                                        class="btn btn-danger"
                                                    >
                                                        Cancel TC
                                                    </button>

                                                </div>

                                            </form>

                                        </div>

                                    </div>

                                </div>

                            @endif

                        @empty

                            <tr>

                                <td
                                    colspan="9"
                                    class="text-center text-muted py-4"
                                >
                                    No Transfer Certificates found.
                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        @if($certificates->hasPages())

            <div class="card-footer bg-white">

                {{ $certificates->links() }}

            </div>

        @endif

    </div>

</div>


{{-- =============================================================
    CREATE TC MODAL
============================================================= --}}

<div
    class="modal fade"
    id="createTcModal"
    tabindex="-1"
>

    <div class="modal-dialog modal-xl">

        <div class="modal-content">

            <form
                method="POST"
                action="{{
                    route(
                        'student-management.transfer-certificate.store'
                    )
                }}"
            >

                @csrf


                <input
                    type="hidden"
                    name="student_enrollment_id"
                    id="tc_student_enrollment_id"
                >


                <div class="modal-header">

                    <div>

                        <h5 class="modal-title">
                            Create Transfer Certificate
                        </h5>

                        <div
                            id="selectedStudentInfo"
                            class="small text-muted"
                        ></div>

                    </div>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                    ></button>

                </div>


                <div class="modal-body">

                    <div class="row g-3">

                        <div class="col-lg-4">

                            <label class="form-label">
                                TC Number *
                            </label>

                            <input
                                type="text"
                                name="tc_number"
                                class="form-control"
                                required
                            >

                        </div>


                        <div class="col-lg-4">

                            <label class="form-label">
                                Application Date
                            </label>

                            <input
                                type="date"
                                name="application_date"
                                class="form-control"
                            >

                        </div>


                        <div class="col-lg-4">

                            <label class="form-label">
                                Issue Date *
                            </label>

                            <input
                                type="date"
                                name="issue_date"
                                class="form-control"
                                value="{{ date('Y-m-d') }}"
                                required
                            >

                        </div>


                        <div class="col-lg-4">

                            <label class="form-label">
                                Leaving Date
                            </label>

                            <input
                                type="date"
                                name="leaving_date"
                                class="form-control"
                            >

                        </div>


                        <div class="col-lg-4">

                            <label class="form-label">
                                T.C. Reason *
                            </label>

                            <select
                                name="tc_reason_id"
                                class="form-select"
                                required
                            >

                                <option value="">
                                    Select Reason
                                </option>

                                @foreach($tcReasons as $reason)

                                    <option value="{{ $reason->id }}">
                                        {{ $reason->name }}
                                    </option>

                                @endforeach

                            </select>

                        </div>


                        <div class="col-lg-4">

                            <label class="form-label">
                                T.C. Remark
                            </label>

                            <select
                                name="tc_remark_option_id"
                                class="form-select"
                            >

                                <option value="">
                                    Select Remark
                                </option>

                                @foreach($tcRemarks as $remark)

                                    <option value="{{ $remark->id }}">
                                        {{ $remark->name }}
                                    </option>

                                @endforeach

                            </select>

                        </div>


                        <div class="col-lg-4">

                            <label class="form-label">
                                Last Result
                            </label>

                            <select
                                name="tc_last_result_option_id"
                                class="form-select"
                            >

                                <option value="">
                                    Select Result
                                </option>

                                @foreach($tcLastResults as $result)

                                    <option value="{{ $result->id }}">
                                        {{ $result->name }}
                                    </option>

                                @endforeach

                            </select>

                        </div>


                        <div class="col-lg-4">

                            <label class="form-label">
                                Conduct
                            </label>

                            <input
                                type="text"
                                name="conduct"
                                class="form-control"
                                placeholder="e.g. Good"
                            >

                        </div>


                        <div class="col-lg-4">

                            <label class="form-label">
                                Next Class
                            </label>

                            <input
                                type="text"
                                name="next_class"
                                class="form-control"
                            >

                        </div>


                        <div class="col-12">

                            <label class="form-label">
                                Next School
                            </label>

                            <input
                                type="text"
                                name="next_school"
                                class="form-control"
                            >

                        </div>


                        <div class="col-12">

                            <label class="form-label">
                                Clearance
                            </label>

                            <div class="d-flex gap-4 flex-wrap">

                                <div class="form-check">

                                    <input
                                        type="checkbox"
                                        name="fees_cleared"
                                        value="1"
                                        class="form-check-input"
                                        id="fees_cleared"
                                    >

                                    <label
                                        class="form-check-label"
                                        for="fees_cleared"
                                    >
                                        Fees Cleared
                                    </label>

                                </div>


                                <div class="form-check">

                                    <input
                                        type="checkbox"
                                        name="library_cleared"
                                        value="1"
                                        class="form-check-input"
                                        id="library_cleared"
                                    >

                                    <label
                                        class="form-check-label"
                                        for="library_cleared"
                                    >
                                        Library Cleared
                                    </label>

                                </div>


                                <div class="form-check">

                                    <input
                                        type="checkbox"
                                        name="transport_cleared"
                                        value="1"
                                        class="form-check-input"
                                        id="transport_cleared"
                                    >

                                    <label
                                        class="form-check-label"
                                        for="transport_cleared"
                                    >
                                        Transport Cleared
                                    </label>

                                </div>

                            </div>

                        </div>


                        <div class="col-12">

                            <label class="form-label">
                                Additional Remarks
                            </label>

                            <textarea
                                name="remarks"
                                class="form-control"
                                rows="3"
                            ></textarea>

                        </div>

                    </div>

                </div>


                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal"
                    >
                        Close
                    </button>

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        Create Draft TC
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


<script>

document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | CREATE TC MODAL
    |--------------------------------------------------------------------------
    */

    document.querySelectorAll(
        '.create-tc-btn'
    ).forEach(function (button) {

        button.addEventListener(
            'click',
            function () {

                document.getElementById(
                    'tc_student_enrollment_id'
                ).value =
                    this.dataset.enrollmentId;


                document.getElementById(
                    'selectedStudentInfo'
                ).textContent =
                    this.dataset.studentName
                    +
                    ' | Admission No: '
                    +
                    this.dataset.admissionNo;

            }
        );

    });


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

    const selectedSection =
        "{{ request('section_id') }}";


    function loadSections(keepSelected = false)
    {
        if (!year || !schoolClass || !section) {
            return;
        }

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
            encodeURIComponent(year.value)
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

        .then(function (data) {

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
                        String(selectedSection)
                    ) {

                        option.selected = true;
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

            section.disabled = false;

            loading.classList.add(
                'd-none'
            );

        });
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
            year.value
            &&
            schoolClass.value
        ) {

            loadSections(true);
        }
    }

});

</script>

@endsection