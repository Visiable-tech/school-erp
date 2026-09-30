@extends('layouts.admin')

@section('title', 'Profile Modify Requests')

@section('content')

<div class="container-fluid">

    <div class="mb-4">

        <h4 class="mb-1">
            Profile Modify Requests
        </h4>

        <div class="text-muted">
            Review and approve requested student profile changes.
        </div>

    </div>


    {{-- =========================================================
        ALERTS
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

                    <li>
                        {{ $error }}
                    </li>

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
                action="{{
                    route(
                        'student-management.profile-modify-requests'
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
                            class="form-control"
                            value="{{ request('search') }}"
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
                            href="{{
                                route(
                                    'student-management.profile-modify-requests'
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
                                <th>Mobile</th>
                                <th>Roll</th>
                                <th>Class</th>
                                <th>Section</th>
                                <th>Action</th>

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
                                        {{
                                            $enrollment
                                                ->student
                                                ?->father_mobile
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

                                        <button
                                            type="button"
                                            class="btn btn-sm btn-primary modify-profile-btn"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modifyProfileModal"

                                            data-student-id="{{
                                                $enrollment->student->id
                                            }}"

                                            data-student-name="{{
                                                $enrollment->student->student_name
                                            }}"

                                            data-admission-no="{{
                                                $enrollment->student->admission_no
                                            }}"

                                            data-student='{!!
                                                json_encode(
                                                    $enrollment->student
                                                        ->only(
                                                            array_keys(
                                                                $allowedFields
                                                            )
                                                        )
                                                )
                                            !!}'
                                        >
                                            Request Change
                                        </button>

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td
                                        colspan="9"
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
        REQUEST FILTER
    ========================================================== --}}

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-body">

            <form
                method="GET"
                action="{{
                    route(
                        'student-management.profile-modify-requests'
                    )
                }}"
            >

                <div class="row g-3">

                    <div class="col-lg-4">

                        <label class="form-label">
                            Search Request
                        </label>

                        <input
                            type="text"
                            name="request_search"
                            class="form-control"
                            value="{{ request('request_search') }}"
                            placeholder="Student / Admission No."
                        >

                    </div>


                    <div class="col-lg-3">

                        <label class="form-label">
                            Status
                        </label>

                        <select
                            name="request_status"
                            class="form-select"
                        >

                            <option value="">
                                All Status
                            </option>

                            <option
                                value="pending"
                                @selected(
                                    request('request_status')
                                    ===
                                    'pending'
                                )
                            >
                                Pending
                            </option>

                            <option
                                value="approved"
                                @selected(
                                    request('request_status')
                                    ===
                                    'approved'
                                )
                            >
                                Approved
                            </option>

                            <option
                                value="rejected"
                                @selected(
                                    request('request_status')
                                    ===
                                    'rejected'
                                )
                            >
                                Rejected
                            </option>

                        </select>

                    </div>


                    <div class="col-lg-5 d-flex align-items-end">

                        <button
                            type="submit"
                            class="btn btn-primary me-2"
                        >
                            Filter
                        </button>

                        <a
                            href="{{
                                route(
                                    'student-management.profile-modify-requests'
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
        REQUEST REGISTER
    ========================================================== --}}

    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white py-3">

            <div
                class="d-flex justify-content-between align-items-center"
            >

                <strong>
                    Profile Modification Request Register
                </strong>

                <span class="badge bg-secondary">
                    {{ $modifyRequests->total() }} Records
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
                            <th>Date</th>
                            <th>Admission No.</th>
                            <th>Student</th>
                            <th>Changes</th>
                            <th>Status</th>
                            <th>Review Remarks</th>
                            <th style="width:180px;">
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse(
                            $modifyRequests as $modifyRequest
                        )

                            <tr>

                                <td>

                                    {{
                                        $modifyRequests->firstItem()
                                        +
                                        $loop->index
                                    }}

                                </td>


                                <td>

                                    {{
                                        $modifyRequest
                                            ->request_date
                                            ?->format('d-m-Y')
                                    }}

                                </td>


                                <td>

                                    {{
                                        $modifyRequest
                                            ->student
                                            ?->admission_no
                                        ?? '-'
                                    }}

                                </td>


                                <td>

                                    <strong>
                                        {{
                                            $modifyRequest
                                                ->student
                                                ?->student_name
                                            ?? '-'
                                        }}
                                    </strong>

                                </td>


                                <td>

                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#changesModal{{ $modifyRequest->id }}"
                                    >
                                        View Changes

                                        <span class="badge bg-primary ms-1">
                                            {{
                                                count(
                                                    $modifyRequest
                                                        ->requested_changes
                                                    ?? []
                                                )
                                            }}
                                        </span>

                                    </button>

                                </td>


                                <td>

                                    @if(
                                        $modifyRequest->status
                                        ===
                                        'pending'
                                    )

                                        <span class="badge bg-warning text-dark">
                                            Pending
                                        </span>

                                    @elseif(
                                        $modifyRequest->status
                                        ===
                                        'approved'
                                    )

                                        <span class="badge bg-success">
                                            Approved
                                        </span>

                                    @elseif(
                                        $modifyRequest->status
                                        ===
                                        'rejected'
                                    )

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

                                    {{
                                        $modifyRequest
                                            ->review_remarks
                                        ?? '-'
                                    }}

                                </td>


                                <td>

                                    @if(
                                        $modifyRequest->status
                                        ===
                                        'pending'
                                    )

                                        <button
                                            type="button"
                                            class="btn btn-sm btn-success"
                                            data-bs-toggle="modal"
                                            data-bs-target="#approveModal{{ $modifyRequest->id }}"
                                        >
                                            Approve
                                        </button>


                                        <button
                                            type="button"
                                            class="btn btn-sm btn-danger"
                                            data-bs-toggle="modal"
                                            data-bs-target="#rejectModal{{ $modifyRequest->id }}"
                                        >
                                            Reject
                                        </button>

                                    @else

                                        <span class="text-muted">
                                            Processed
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
                                    No profile modification requests found.
                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        @if($modifyRequests->hasPages())

            <div class="card-footer bg-white">

                {{ $modifyRequests->links() }}

            </div>

        @endif

    </div>

</div>


{{-- =============================================================
    REQUEST CHANGE MODAL
============================================================= --}}

<div
    class="modal fade"
    id="modifyProfileModal"
    tabindex="-1"
>

    <div class="modal-dialog modal-xl">

        <div class="modal-content">

            <form
                method="POST"
                action="{{
                    route(
                        'student-management.profile-modify-requests.store'
                    )
                }}"
            >

                @csrf


                <input
                    type="hidden"
                    name="student_id"
                    id="modify_student_id"
                >


                <div class="modal-header">

                    <div>

                        <h5 class="modal-title">
                            Request Profile Modification
                        </h5>

                        <div
                            id="modifyStudentInfo"
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

                    <div class="alert alert-info">

                        Only enter fields that need to be changed.
                        Leave all other fields blank.

                    </div>


                    <div class="row g-3">

                        <div class="col-md-4">

                            <label class="form-label">
                                Request Date
                            </label>

                            <input
                                type="date"
                                name="request_date"
                                value="{{ date('Y-m-d') }}"
                                class="form-control"
                                required
                            >

                        </div>


                        <div class="col-12">

                            <hr>

                            <h6>
                                Student Information
                            </h6>

                        </div>


                        <div class="col-md-4">

                            <label class="form-label">
                                Student Name
                            </label>

                            <input
                                type="text"
                                name="changes[student_name]"
                                data-profile-field="student_name"
                                class="form-control"
                            >

                            <small
                                class="text-muted current-value"
                                data-current="student_name"
                            ></small>

                        </div>


                        <div class="col-md-4">

                            <label class="form-label">
                                Date of Birth
                            </label>

                            <input
                                type="date"
                                name="changes[date_of_birth]"
                                data-profile-field="date_of_birth"
                                class="form-control"
                            >

                            <small
                                class="text-muted current-value"
                                data-current="date_of_birth"
                            ></small>

                        </div>


                        <div class="col-md-4">

                            <label class="form-label">
                                Gender
                            </label>

                            <select
                                name="changes[gender]"
                                data-profile-field="gender"
                                class="form-select"
                            >

                                <option value="">
                                    No Change
                                </option>

                                <option value="Male">
                                    Male
                                </option>

                                <option value="Female">
                                    Female
                                </option>

                                <option value="Other">
                                    Other
                                </option>

                            </select>

                            <small
                                class="text-muted current-value"
                                data-current="gender"
                            ></small>

                        </div>


                        <div class="col-md-4">

                            <label class="form-label">
                                Blood Group
                            </label>

                            <input
                                type="text"
                                name="changes[blood_group]"
                                data-profile-field="blood_group"
                                class="form-control"
                            >

                            <small
                                class="text-muted current-value"
                                data-current="blood_group"
                            ></small>

                        </div>


                        <div class="col-md-4">

                            <label class="form-label">
                                Nationality
                            </label>

                            <input
                                type="text"
                                name="changes[nationality]"
                                data-profile-field="nationality"
                                class="form-control"
                            >

                            <small
                                class="text-muted current-value"
                                data-current="nationality"
                            ></small>

                        </div>


                        <div class="col-md-4">

                            <label class="form-label">
                                Religion
                            </label>

                            <input
                                type="text"
                                name="changes[religion]"
                                data-profile-field="religion"
                                class="form-control"
                            >

                            <small
                                class="text-muted current-value"
                                data-current="religion"
                            ></small>

                        </div>


                        <div class="col-md-4">

                            <label class="form-label">
                                Category
                            </label>

                            <input
                                type="text"
                                name="changes[category]"
                                data-profile-field="category"
                                class="form-control"
                            >

                            <small
                                class="text-muted current-value"
                                data-current="category"
                            ></small>

                        </div>


                        <div class="col-md-4">

                            <label class="form-label">
                                Mother Tongue
                            </label>

                            <input
                                type="text"
                                name="changes[mother_tongue]"
                                data-profile-field="mother_tongue"
                                class="form-control"
                            >

                            <small
                                class="text-muted current-value"
                                data-current="mother_tongue"
                            ></small>

                        </div>


                        {{-- PARENT DETAILS --}}

                        <div class="col-12">

                            <hr>

                            <h6>
                                Parent / Guardian Details
                            </h6>

                        </div>


                        @foreach([
                            'father_name' => 'Father Name',
                            'father_mobile' => 'Father Mobile',
                            'father_email' => 'Father Email',
                            'father_occupation' => 'Father Occupation',

                            'mother_name' => 'Mother Name',
                            'mother_mobile' => 'Mother Mobile',
                            'mother_email' => 'Mother Email',
                            'mother_occupation' => 'Mother Occupation',

                            'guardian_name' => 'Guardian Name',
                            'guardian_relation' => 'Guardian Relation',
                            'guardian_mobile' => 'Guardian Mobile',
                        ] as $field => $label)

                            <div class="col-md-4">

                                <label class="form-label">
                                    {{ $label }}
                                </label>

                                <input
                                    type="text"
                                    name="changes[{{ $field }}]"
                                    data-profile-field="{{ $field }}"
                                    class="form-control"
                                >

                                <small
                                    class="text-muted current-value"
                                    data-current="{{ $field }}"
                                ></small>

                            </div>

                        @endforeach


                        {{-- ADDRESS --}}

                        <div class="col-12">

                            <hr>

                            <h6>
                                Address Details
                            </h6>

                        </div>


                        @foreach([
                            'present_address' => 'Present Address',
                            'present_city' => 'Present City',
                            'present_state' => 'Present State',
                            'present_pin_code' => 'Present PIN Code',

                            'permanent_address' => 'Permanent Address',
                            'permanent_city' => 'Permanent City',
                            'permanent_state' => 'Permanent State',
                            'permanent_pin_code' => 'Permanent PIN Code',
                        ] as $field => $label)

                            <div
                                class="{{
                                    str_contains(
                                        $field,
                                        'address'
                                    )
                                    ? 'col-md-6'
                                    : 'col-md-3'
                                }}"
                            >

                                <label class="form-label">
                                    {{ $label }}
                                </label>

                                <input
                                    type="text"
                                    name="changes[{{ $field }}]"
                                    data-profile-field="{{ $field }}"
                                    class="form-control"
                                >

                                <small
                                    class="text-muted current-value"
                                    data-current="{{ $field }}"
                                ></small>

                            </div>

                        @endforeach


                        {{-- PREVIOUS SCHOOL --}}

                        <div class="col-12">

                            <hr>

                            <h6>
                                Previous School Details
                            </h6>

                        </div>


                        @foreach([
                            'previous_school' => 'Previous School',
                            'previous_class' => 'Previous Class',
                            'previous_board' => 'Previous Board',
                        ] as $field => $label)

                            <div class="col-md-4">

                                <label class="form-label">
                                    {{ $label }}
                                </label>

                                <input
                                    type="text"
                                    name="changes[{{ $field }}]"
                                    data-profile-field="{{ $field }}"
                                    class="form-control"
                                >

                                <small
                                    class="text-muted current-value"
                                    data-current="{{ $field }}"
                                ></small>

                            </div>

                        @endforeach


                        <div class="col-12">

                            <hr>

                            <label class="form-label">
                                Request Remarks
                            </label>

                            <textarea
                                name="request_remarks"
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
                        Submit Request
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


{{-- =============================================================
    REQUEST-SPECIFIC MODALS
============================================================= --}}

@foreach($modifyRequests as $modifyRequest)

    {{-- VIEW CHANGES --}}

    <div
        class="modal fade"
        id="changesModal{{ $modifyRequest->id }}"
        tabindex="-1"
    >

        <div class="modal-dialog modal-lg">

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title">
                        Requested Changes
                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                    ></button>

                </div>


                <div class="modal-body">

                    <div class="mb-3">

                        <strong>
                            {{
                                $modifyRequest
                                    ->student
                                    ?->student_name
                            }}
                        </strong>

                        <div class="text-muted">

                            Admission No:
                            {{
                                $modifyRequest
                                    ->student
                                    ?->admission_no
                                ?? '-'
                            }}

                        </div>

                    </div>


                    <div class="table-responsive">

                        <table
                            class="table table-bordered table-striped table-hover align-middle mb-0"
                        >

                            <thead class="table-dark">

                                <tr>
                                    <th>Field</th>
                                    <th>Current / Old</th>
                                    <th>Requested</th>
                                </tr>

                            </thead>

                            <tbody>

                                @foreach(
                                    $modifyRequest->requested_changes
                                    ?? []
                                    as $field => $change
                                )

                                    <tr>

                                        <td>
                                            {{
                                                $allowedFields[$field]
                                                ??
                                                ucwords(
                                                    str_replace(
                                                        '_',
                                                        ' ',
                                                        $field
                                                    )
                                                )
                                            }}
                                        </td>

                                        <td>
                                            {{
                                                $change['old']
                                                ?? '-'
                                            }}
                                        </td>

                                        <td>
                                            <strong class="text-primary">
                                                {{
                                                    $change['new']
                                                    ?? '-'
                                                }}
                                            </strong>
                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>


                    @if($modifyRequest->request_remarks)

                        <div class="mt-3">

                            <strong>
                                Request Remarks:
                            </strong>

                            <div>
                                {{
                                    $modifyRequest
                                        ->request_remarks
                                }}
                            </div>

                        </div>

                    @endif

                </div>

            </div>

        </div>

    </div>


    @if($modifyRequest->status === 'pending')

        {{-- APPROVE --}}

        <div
            class="modal fade"
            id="approveModal{{ $modifyRequest->id }}"
            tabindex="-1"
        >

            <div class="modal-dialog">

                <div class="modal-content">

                    <form
                        method="POST"
                        action="{{
                            route(
                                'student-management.profile-modify-requests.approve',
                                $modifyRequest
                            )
                        }}"
                    >

                        @csrf


                        <div class="modal-header">

                            <h5 class="modal-title">
                                Approve Profile Changes
                            </h5>

                            <button
                                type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"
                            ></button>

                        </div>


                        <div class="modal-body">

                            <div class="alert alert-warning">

                                Approval will update the student's
                                actual profile immediately.

                            </div>


                            <p>

                                Student:

                                <strong>
                                    {{
                                        $modifyRequest
                                            ->student
                                            ?->student_name
                                    }}
                                </strong>

                            </p>


                            <label class="form-label">
                                Approval Remarks
                            </label>

                            <textarea
                                name="review_remarks"
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
                                Approve Changes
                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>


        {{-- REJECT --}}

        <div
            class="modal fade"
            id="rejectModal{{ $modifyRequest->id }}"
            tabindex="-1"
        >

            <div class="modal-dialog">

                <div class="modal-content">

                    <form
                        method="POST"
                        action="{{
                            route(
                                'student-management.profile-modify-requests.reject',
                                $modifyRequest
                            )
                        }}"
                    >

                        @csrf


                        <div class="modal-header">

                            <h5 class="modal-title">
                                Reject Profile Changes
                            </h5>

                            <button
                                type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"
                            ></button>

                        </div>


                        <div class="modal-body">

                            <p>

                                Reject request for

                                <strong>
                                    {{
                                        $modifyRequest
                                            ->student
                                            ?->student_name
                                    }}
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

@endforeach


<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        /*
        |--------------------------------------------------------------------------
        | PROFILE MODAL
        |--------------------------------------------------------------------------
        */

        document.querySelectorAll(
            '.modify-profile-btn'
        ).forEach(
            function (button) {

                button.addEventListener(
                    'click',
                    function () {

                        const student =
                            JSON.parse(
                                this.dataset.student
                                || '{}'
                            );


                        document.getElementById(
                            'modify_student_id'
                        ).value =
                            this.dataset.studentId;


                        document.getElementById(
                            'modifyStudentInfo'
                        ).textContent =
                            this.dataset.studentName
                            +
                            ' | Admission No: '
                            +
                            this.dataset.admissionNo;


                        /*
                         * Clear requested values
                         */

                        document.querySelectorAll(
                            '[data-profile-field]'
                        ).forEach(
                            function (input) {

                                input.value = '';

                            }
                        );


                        /*
                         * Show current values
                         */

                        document.querySelectorAll(
                            '[data-current]'
                        ).forEach(
                            function (element) {

                                const field =
                                    element.dataset.current;

                                let value =
                                    student[field];


                                if (
                                    value === null
                                    ||
                                    value === undefined
                                    ||
                                    value === ''
                                ) {

                                    value = '-';
                                }


                                element.textContent =
                                    'Current: '
                                    +
                                    value;

                            }
                        );

                    }
                );

            }
        );


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


        function loadSections(
            keepSelected = false
        ) {

            if (
                !year
                ||
                !schoolClass
                ||
                !section
            ) {
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
                function (response) {

                    if (!response.ok) {

                        throw new Error(
                            'Unable to load sections.'
                        );
                    }

                    return response.json();
                }
            )

            .then(
                function (data) {

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
                function (error) {

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
            year
            &&
            schoolClass
            &&
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

    }
);

</script>

@endsection