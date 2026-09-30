@extends('layouts.admin')

@section('title', 'Create Manual Transfer Certificate')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h4 class="mb-1">
                Create Manual Transfer Certificate
            </h4>

            <div class="text-muted">
                Create a TC without requiring an existing ERP student record.
            </div>
        </div>

        <a
            href="{{ route('student-management.manual-transfer-certificate') }}"
            class="btn btn-outline-secondary"
        >
            <i class="bi bi-arrow-left me-1"></i>
            Manual TC List
        </a>

    </div>


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


    <form
        method="POST"
        action="{{ route('student-management.manual-transfer-certificate.store') }}"
    >

        @csrf


        {{-- =====================================================
            STUDENT DETAILS
        ====================================================== --}}

        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white py-3">

                <strong>
                    <i class="bi bi-person me-1"></i>
                    Student Details
                </strong>

            </div>

            <div class="card-body">

                <div class="row g-3">

                    <div class="col-lg-4 col-md-6">

                        <label class="form-label">
                            Student Name
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            name="manual_student_name"
                            class="form-control"
                            value="{{ old('manual_student_name') }}"
                            required
                        >

                    </div>


                    <div class="col-lg-4 col-md-6">

                        <label class="form-label">
                            Admission No.
                        </label>

                        <input
                            type="text"
                            name="manual_admission_no"
                            class="form-control"
                            value="{{ old('manual_admission_no') }}"
                        >

                    </div>


                    <div class="col-lg-4 col-md-6">

                        <label class="form-label">
                            Date of Birth
                        </label>

                        <input
                            type="date"
                            name="manual_date_of_birth"
                            class="form-control"
                            value="{{ old('manual_date_of_birth') }}"
                        >

                    </div>


                    <div class="col-lg-4 col-md-6">

                        <label class="form-label">
                            Father's Name
                        </label>

                        <input
                            type="text"
                            name="manual_father_name"
                            class="form-control"
                            value="{{ old('manual_father_name') }}"
                        >

                    </div>


                    <div class="col-lg-4 col-md-6">

                        <label class="form-label">
                            Mother's Name
                        </label>

                        <input
                            type="text"
                            name="manual_mother_name"
                            class="form-control"
                            value="{{ old('manual_mother_name') }}"
                        >

                    </div>

                </div>

            </div>

        </div>


        {{-- =====================================================
            ACADEMIC DETAILS
        ====================================================== --}}

        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white py-3">

                <strong>
                    <i class="bi bi-mortarboard me-1"></i>
                    Academic Details
                </strong>

            </div>

            <div class="card-body">

                <div class="row g-3">

                    <div class="col-lg-4 col-md-6">

                        <label class="form-label">
                            Academic Year
                        </label>

                        <input
                            type="text"
                            name="manual_academic_year"
                            class="form-control"
                            value="{{ old('manual_academic_year') }}"
                            placeholder="Example: 2025-26"
                        >

                    </div>


                    <div class="col-lg-4 col-md-6">

                        <label class="form-label">
                            Class
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            name="manual_class"
                            class="form-control"
                            value="{{ old('manual_class') }}"
                            placeholder="Example: Class X"
                            required
                        >

                    </div>


                    <div class="col-lg-4 col-md-6">

                        <label class="form-label">
                            Section
                        </label>

                        <input
                            type="text"
                            name="manual_section"
                            class="form-control"
                            value="{{ old('manual_section') }}"
                        >

                    </div>

                </div>

            </div>

        </div>


        {{-- =====================================================
            TC DETAILS
        ====================================================== --}}

        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white py-3">

                <strong>
                    <i class="bi bi-file-earmark-text me-1"></i>
                    Transfer Certificate Details
                </strong>

            </div>

            <div class="card-body">

                <div class="row g-3">

                    <div class="col-lg-4 col-md-6">

                        <label class="form-label">
                            TC Number
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            name="tc_number"
                            class="form-control"
                            value="{{ old('tc_number') }}"
                            required
                        >

                    </div>


                    <div class="col-lg-4 col-md-6">

                        <label class="form-label">
                            Application Date
                        </label>

                        <input
                            type="date"
                            name="application_date"
                            class="form-control"
                            value="{{ old('application_date') }}"
                        >

                    </div>


                    <div class="col-lg-4 col-md-6">

                        <label class="form-label">
                            Issue Date
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="date"
                            name="issue_date"
                            class="form-control"
                            value="{{ old('issue_date', date('Y-m-d')) }}"
                            required
                        >

                    </div>


                    <div class="col-lg-4 col-md-6">

                        <label class="form-label">
                            Leaving Date
                        </label>

                        <input
                            type="date"
                            name="leaving_date"
                            class="form-control"
                            value="{{ old('leaving_date') }}"
                        >

                    </div>


                    <div class="col-lg-4 col-md-6">

                        <label class="form-label">
                            T.C. Reason
                            <span class="text-danger">*</span>
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

                                <option
                                    value="{{ $reason->id }}"
                                    @selected(
                                        old('tc_reason_id')
                                        == $reason->id
                                    )
                                >
                                    {{ $reason->name }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    <div class="col-lg-4 col-md-6">

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

                                <option
                                    value="{{ $remark->id }}"
                                    @selected(
                                        old('tc_remark_option_id')
                                        == $remark->id
                                    )
                                >
                                    {{ $remark->name }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    <div class="col-lg-4 col-md-6">

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

                                <option
                                    value="{{ $result->id }}"
                                    @selected(
                                        old('tc_last_result_option_id')
                                        == $result->id
                                    )
                                >
                                    {{ $result->name }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    <div class="col-lg-4 col-md-6">

                        <label class="form-label">
                            Conduct
                        </label>

                        <input
                            type="text"
                            name="conduct"
                            class="form-control"
                            value="{{ old('conduct') }}"
                            placeholder="Example: Good"
                        >

                    </div>


                    <div class="col-lg-4 col-md-6">

                        <label class="form-label">
                            Next Class
                        </label>

                        <input
                            type="text"
                            name="next_class"
                            class="form-control"
                            value="{{ old('next_class') }}"
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
                            value="{{ old('next_school') }}"
                        >

                    </div>


                    <div class="col-12">

                        <label class="form-label">
                            Clearance
                        </label>

                        <div class="d-flex flex-wrap gap-4">

                            <div class="form-check">

                                <input
                                    type="checkbox"
                                    name="fees_cleared"
                                    value="1"
                                    class="form-check-input"
                                    id="fees_cleared"
                                    @checked(old('fees_cleared'))
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
                                    @checked(old('library_cleared'))
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
                                    @checked(old('transport_cleared'))
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
                        >{{ old('remarks') }}</textarea>

                    </div>

                </div>

            </div>


            <div class="card-footer bg-white">

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    <i class="bi bi-save me-1"></i>
                    Create Manual TC
                </button>

            </div>

        </div>

    </form>

</div>

@endsection