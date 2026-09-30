@extends('layouts.admin')

@section('title', 'T.C Requests')

@section('content')

<div class="container-fluid">

    <div class="mb-4">
        <h4 class="mb-1">T.C Requests</h4>
        <div class="text-muted">
            Submit and process student Transfer Certificate requests.
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

            <strong>Please correct the following:</strong>

            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>

        </div>
    @endif


    {{-- =========================================================
        STUDENT FILTER
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
                action="{{ route('student-management.tc-requests') }}"
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
                            Show Students
                        </button>

                        <a
                            href="{{ route('student-management.tc-requests') }}"
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
        STUDENT TABLE
    ========================================================== --}}

    @if(
        request()->filled('academic_year_id') &&
        request()->filled('school_class_id') &&
        request()->filled('section_id')
    )

        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white py-3">
                <strong>Student List</strong>
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
                                <th width="130">Action</th>
                            </tr>

                        </thead>

                        <tbody>

                            @forelse($enrollments as $enrollment)

                                <tr>

                                    <td>{{ $loop->iteration }}</td>

                                    <td>
                                        <strong class="text-primary">
                                            {{ $enrollment->student?->admission_no ?? '-' }}
                                        </strong>
                                    </td>

                                    <td>
                                        <strong>
                                            {{ $enrollment->student?->student_name ?? '-' }}
                                        </strong>
                                    </td>

                                    <td>
                                        {{ $enrollment->student?->father_name ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $enrollment->roll_no ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $enrollment->schoolClass?->name ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $enrollment->section?->name ?? '-' }}
                                    </td>

                                    <td>

                                        <button
                                            type="button"
                                            class="btn btn-sm btn-primary request-tc-btn"
                                            data-bs-toggle="modal"
                                            data-bs-target="#requestTcModal"
                                            data-enrollment-id="{{ $enrollment->id }}"
                                            data-student-name="{{ $enrollment->student?->student_name }}"
                                            data-admission-no="{{ $enrollment->student?->admission_no }}"
                                        >
                                            Request TC
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
        REQUEST REGISTER FILTER
    ========================================================== --}}

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-body">

            <form
                method="GET"
                action="{{ route('student-management.tc-requests') }}"
            >

                <div class="row g-3">

                    <div class="col-md-4">

                        <label class="form-label">
                            Request Status
                        </label>

                        <select
                            name="request_status"
                            class="form-select"
                        >

                            <option value="">
                                All Requests
                            </option>

                            <option
                                value="pending"
                                @selected(request('request_status') === 'pending')
                            >
                                Pending
                            </option>

                            <option
                                value="approved"
                                @selected(request('request_status') === 'approved')
                            >
                                Approved
                            </option>

                            <option
                                value="rejected"
                                @selected(request('request_status') === 'rejected')
                            >
                                Rejected
                            </option>

                            <option
                                value="cancelled"
                                @selected(request('request_status') === 'cancelled')
                            >
                                Cancelled
                            </option>

                        </select>

                    </div>

                    <div class="col-md-4 d-flex align-items-end">

                        <button
                            type="submit"
                            class="btn btn-primary me-2"
                        >
                            Filter
                        </button>

                        <a
                            href="{{ route('student-management.tc-requests') }}"
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
        REQUEST REGISTER
    ========================================================== --}}

    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white py-3">

            <div class="d-flex justify-content-between">
                <strong>T.C Request Register</strong>

                <span class="badge bg-secondary">
                    {{ $tcRequests->total() }} Records
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
                            <th>#</th>
                            <th>Request Date</th>
                            <th>Admission No.</th>
                            <th>Student</th>
                            <th>Class</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>TC No.</th>
                            <th width="190">Action</th>
                        </tr>

                    </thead>

                    <tbody>

                        @forelse($tcRequests as $tcRequest)

                            <tr>

                                <td>
                                    {{
                                        $tcRequests->firstItem()
                                        + $loop->index
                                    }}
                                </td>

                                <td>
                                    {{ $tcRequest->request_date?->format('d-m-Y') }}
                                </td>

                                <td>
                                    {{ $tcRequest->student?->admission_no ?? '-' }}
                                </td>

                                <td>

                                    <strong>
                                        {{ $tcRequest->student?->student_name ?? '-' }}
                                    </strong>

                                    @if($tcRequest->request_remarks)

                                        <div class="small text-muted mt-1">
                                            {{ $tcRequest->request_remarks }}
                                        </div>

                                    @endif

                                </td>

                                <td>

                                    {{ $tcRequest->enrollment?->schoolClass?->name ?? '-' }}

                                    @if($tcRequest->enrollment?->section)

                                        -
                                        {{ $tcRequest->enrollment->section->name }}

                                    @endif

                                </td>

                                <td>
                                    {{ $tcRequest->reason?->name ?? '-' }}
                                </td>

                                <td>

                                    @if($tcRequest->status === 'pending')

                                        <span class="badge bg-warning text-dark">
                                            Pending
                                        </span>

                                    @elseif($tcRequest->status === 'approved')

                                        <span class="badge bg-success">
                                            Approved
                                        </span>

                                    @elseif($tcRequest->status === 'rejected')

                                        <span class="badge bg-danger">
                                            Rejected
                                        </span>

                                    @else

                                        <span class="badge bg-secondary">
                                            Cancelled
                                        </span>

                                    @endif

                                </td>

                                <td>

                                    @if($tcRequest->transferCertificate)

                                        <strong class="text-primary">
                                            {{ $tcRequest->transferCertificate->tc_number }}
                                        </strong>

                                        <div class="small text-muted">
                                            {{ ucfirst($tcRequest->transferCertificate->status) }}
                                        </div>

                                    @else

                                        -

                                    @endif

                                </td>

                                <td>

                                    @if($tcRequest->status === 'pending')

                                        <button
                                            type="button"
                                            class="btn btn-sm btn-success"
                                            data-bs-toggle="modal"
                                            data-bs-target="#approveModal{{ $tcRequest->id }}"
                                        >
                                            Approve
                                        </button>

                                        <button
                                            type="button"
                                            class="btn btn-sm btn-danger"
                                            data-bs-toggle="modal"
                                            data-bs-target="#rejectModal{{ $tcRequest->id }}"
                                        >
                                            Reject
                                        </button>

                                    @elseif(
                                        $tcRequest->status === 'approved' &&
                                        $tcRequest->transferCertificate
                                    )

                                        <span class="small text-success">
                                            TC Created
                                        </span>

                                    @else

                                        <span class="text-muted">-</span>

                                    @endif

                                </td>

                            </tr>


                            {{-- APPROVE MODAL --}}

                            @if($tcRequest->status === 'pending')

                                <div
                                    class="modal fade"
                                    id="approveModal{{ $tcRequest->id }}"
                                    tabindex="-1"
                                >

                                    <div class="modal-dialog">

                                        <div class="modal-content">

                                            <form
                                                method="POST"
                                                action="{{
                                                    route(
                                                        'student-management.tc-requests.approve',
                                                        $tcRequest
                                                    )
                                                }}"
                                            >

                                                @csrf

                                                <div class="modal-header">

                                                    <h5 class="modal-title">
                                                        Approve T.C Request
                                                    </h5>

                                                    <button
                                                        type="button"
                                                        class="btn-close"
                                                        data-bs-dismiss="modal"
                                                    ></button>

                                                </div>

                                                <div class="modal-body">

                                                    <div class="alert alert-info">

                                                        <strong>
                                                            {{ $tcRequest->student?->student_name }}
                                                        </strong>

                                                        <br>

                                                        Admission No:
                                                        {{ $tcRequest->student?->admission_no ?? '-' }}

                                                    </div>


                                                    <div class="mb-3">

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


                                                    <div class="mb-3">

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


                                                    <div>

                                                        <label class="form-label">
                                                            Approval Remarks
                                                        </label>

                                                        <textarea
                                                            name="review_remarks"
                                                            class="form-control"
                                                            rows="3"
                                                        ></textarea>

                                                    </div>

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
                                                        Approve & Create Draft TC
                                                    </button>

                                                </div>

                                            </form>

                                        </div>

                                    </div>

                                </div>


                                {{-- REJECT MODAL --}}

                                <div
                                    class="modal fade"
                                    id="rejectModal{{ $tcRequest->id }}"
                                    tabindex="-1"
                                >

                                    <div class="modal-dialog">

                                        <div class="modal-content">

                                            <form
                                                method="POST"
                                                action="{{
                                                    route(
                                                        'student-management.tc-requests.reject',
                                                        $tcRequest
                                                    )
                                                }}"
                                            >

                                                @csrf

                                                <div class="modal-header">

                                                    <h5 class="modal-title">
                                                        Reject T.C Request
                                                    </h5>

                                                    <button
                                                        type="button"
                                                        class="btn-close"
                                                        data-bs-dismiss="modal"
                                                    ></button>

                                                </div>

                                                <div class="modal-body">

                                                    <p>
                                                        Reject the T.C request for
                                                        <strong>
                                                            {{ $tcRequest->student?->student_name }}
                                                        </strong>?
                                                    </p>

                                                    <label class="form-label">
                                                        Rejection Reason *
                                                    </label>

                                                    <textarea
                                                        name="review_remarks"
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
                                                        Cancel
                                                    </button>

                                                    <button
                                                        type="submit"
                                                        class="btn btn-danger"
                                                    >
                                                        Reject Request
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
                                    class="text-center text-muted py-5"
                                >
                                    No T.C requests found.
                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        @if($tcRequests->hasPages())

            <div class="card-footer bg-white">

                {{ $tcRequests->links() }}

            </div>

        @endif

    </div>

</div>


{{-- =============================================================
    REQUEST TC MODAL
============================================================= --}}

<div
    class="modal fade"
    id="requestTcModal"
    tabindex="-1"
>

    <div class="modal-dialog">

        <div class="modal-content">

            <form
                method="POST"
                action="{{ route('student-management.tc-requests.store') }}"
            >

                @csrf

                <input
                    type="hidden"
                    name="student_enrollment_id"
                    id="request_student_enrollment_id"
                >


                <div class="modal-header">

                    <div>

                        <h5 class="modal-title">
                            New T.C Request
                        </h5>

                        <div
                            id="requestStudentInfo"
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

                    <div class="mb-3">

                        <label class="form-label">
                            Request Date *
                        </label>

                        <input
                            type="date"
                            name="request_date"
                            class="form-control"
                            value="{{ date('Y-m-d') }}"
                            required
                        >

                    </div>


                    <div class="mb-3">

                        <label class="form-label">
                            T.C Reason *
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


                    <div>

                        <label class="form-label">
                            Remarks
                        </label>

                        <textarea
                            name="request_remarks"
                            class="form-control"
                            rows="4"
                        ></textarea>

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
                        Submit Request
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
    | TC REQUEST MODAL
    |--------------------------------------------------------------------------
    */

    document.querySelectorAll(
        '.request-tc-btn'
    ).forEach(function (button) {

        button.addEventListener(
            'click',
            function () {

                document.getElementById(
                    'request_student_enrollment_id'
                ).value =
                    this.dataset.enrollmentId;


                document.getElementById(
                    'requestStudentInfo'
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
    | LOAD SECTIONS
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


        if (!year.value || !schoolClass.value) {
            return;
        }


        section.disabled = true;

        loading.classList.remove('d-none');


        const url =
            "{{ route('student-management.get-sections') }}"
            +
            '?academic_year_id='
            +
            encodeURIComponent(year.value)
            +
            '&school_class_id='
            +
            encodeURIComponent(schoolClass.value);


        fetch(url, {

            headers: {

                'Accept':
                    'application/json',

                'X-Requested-With':
                    'XMLHttpRequest'

            }

        })

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


            sections.forEach(function (item) {

                const option =
                    document.createElement(
                        'option'
                    );

                option.value = item.id;

                option.textContent = item.name;


                if (
                    keepSelected &&
                    String(item.id)
                    ===
                    String(selectedSection)
                ) {
                    option.selected = true;
                }


                section.appendChild(option);

            });

        })

        .catch(function (error) {

            console.error(error);

            section.innerHTML =
                '<option value="">Unable to load sections</option>';

        })

        .finally(function () {

            section.disabled = false;

            loading.classList.add('d-none');

        });
    }


    if (year && schoolClass && section) {

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

});

</script>

@endsection