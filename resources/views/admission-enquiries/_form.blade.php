@php

    $selectedSession = old(
        'admission_session_id',
        $admissionEnquiry->admission_session_id
            ?? ($currentSession->id ?? '')
    );

@endphp


<div class="card border-0 bg-light mb-4">

    <div class="card-body">

        <h6 class="mb-3">
            Enquiry Information
        </h6>


        <div class="row">

            <div class="col-md-4 mb-3">

                <label class="form-label">
                    Admission Session
                    <span class="text-danger">*</span>
                </label>

                <select
                    name="admission_session_id"
                    class="form-select @error('admission_session_id') is-invalid @enderror"
                    required
                >

                    <option value="">
                        Select Admission Session
                    </option>

                    @foreach($sessions as $session)

                        <option
                            value="{{ $session->id }}"
                            @selected(
                                $selectedSession == $session->id
                            )
                        >

                            {{ $session->name }}

                            @if($session->is_current)
                                (Current)
                            @endif

                        </option>

                    @endforeach

                </select>

                @error('admission_session_id')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror

            </div>


            <div class="col-md-4 mb-3">

                <label class="form-label">
                    Enquiry Date
                    <span class="text-danger">*</span>
                </label>

                <input
                    type="date"
                    name="enquiry_date"
                    class="form-control @error('enquiry_date') is-invalid @enderror"
                    value="{{ old(
                        'enquiry_date',
                        isset($admissionEnquiry)
                            ? $admissionEnquiry
                                ->enquiry_date
                                ->format('Y-m-d')
                            : now()->format('Y-m-d')
                    ) }}"
                    required
                >

                @error('enquiry_date')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror

            </div>


            <div class="col-md-4 mb-3">

                <label class="form-label">
                    Admission For Class
                </label>

                <select
                    name="school_class_id"
                    class="form-select"
                >

                    <option value="">
                        Select Class
                    </option>

                    @foreach($classes as $class)

                        <option
                            value="{{ $class->id }}"
                            @selected(
                                old(
                                    'school_class_id',
                                    $admissionEnquiry->school_class_id ?? ''
                                ) == $class->id
                            )
                        >

                            {{ $class->name }}

                            @if($class->wing)
                                - {{ $class->wing->name }}
                            @endif

                        </option>

                    @endforeach

                </select>

            </div>

        </div>

    </div>

</div>


{{-- Student --}}
<div class="card border-0 bg-light mb-4">

    <div class="card-body">

        <h6 class="mb-3">
            Student Information
        </h6>

        <div class="row">

            <div class="col-md-6 mb-3">

                <label class="form-label">
                    Student Name
                    <span class="text-danger">*</span>
                </label>

                <input
                    type="text"
                    name="student_name"
                    class="form-control @error('student_name') is-invalid @enderror"
                    value="{{ old(
                        'student_name',
                        $admissionEnquiry->student_name ?? ''
                    ) }}"
                    required
                >

                @error('student_name')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror

            </div>


            <div class="col-md-3 mb-3">

                <label class="form-label">
                    Date of Birth
                </label>

                <input
                    type="date"
                    name="date_of_birth"
                    class="form-control"
                    value="{{ old(
                        'date_of_birth',
                        isset($admissionEnquiry)
                        && $admissionEnquiry->date_of_birth
                            ? $admissionEnquiry
                                ->date_of_birth
                                ->format('Y-m-d')
                            : ''
                    ) }}"
                >

            </div>


            <div class="col-md-3 mb-3">

                <label class="form-label">
                    Gender
                </label>

                <select
                    name="gender"
                    class="form-select"
                >

                    <option value="">
                        Select
                    </option>

                    @foreach([
                        'male' => 'Male',
                        'female' => 'Female',
                        'other' => 'Other'
                    ] as $value => $label)

                        <option
                            value="{{ $value }}"
                            @selected(
                                old(
                                    'gender',
                                    $admissionEnquiry->gender ?? ''
                                ) == $value
                            )
                        >
                            {{ $label }}
                        </option>

                    @endforeach

                </select>

            </div>

        </div>

    </div>

</div>


{{-- Parent --}}
<div class="card border-0 bg-light mb-4">

    <div class="card-body">

        <h6 class="mb-3">
            Parent / Guardian Information
        </h6>

        <div class="row">

            <div class="col-md-4 mb-3">

                <label class="form-label">
                    Father Name
                </label>

                <input
                    type="text"
                    name="father_name"
                    class="form-control"
                    value="{{ old(
                        'father_name',
                        $admissionEnquiry->father_name ?? ''
                    ) }}"
                >

            </div>


            <div class="col-md-4 mb-3">

                <label class="form-label">
                    Mother Name
                </label>

                <input
                    type="text"
                    name="mother_name"
                    class="form-control"
                    value="{{ old(
                        'mother_name',
                        $admissionEnquiry->mother_name ?? ''
                    ) }}"
                >

            </div>


            <div class="col-md-4 mb-3">

                <label class="form-label">
                    Guardian Name
                </label>

                <input
                    type="text"
                    name="guardian_name"
                    class="form-control"
                    value="{{ old(
                        'guardian_name',
                        $admissionEnquiry->guardian_name ?? ''
                    ) }}"
                >

            </div>


            <div class="col-md-4 mb-3">

                <label class="form-label">
                    Mobile
                    <span class="text-danger">*</span>
                </label>

                <input
                    type="text"
                    name="mobile"
                    maxlength="20"
                    class="form-control @error('mobile') is-invalid @enderror"
                    value="{{ old(
                        'mobile',
                        $admissionEnquiry->mobile ?? ''
                    ) }}"
                    required
                >

                @error('mobile')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror

            </div>


            <div class="col-md-4 mb-3">

                <label class="form-label">
                    Alternate Mobile
                </label>

                <input
                    type="text"
                    name="alternate_mobile"
                    maxlength="20"
                    class="form-control"
                    value="{{ old(
                        'alternate_mobile',
                        $admissionEnquiry->alternate_mobile ?? ''
                    ) }}"
                >

            </div>


            <div class="col-md-4 mb-3">

                <label class="form-label">
                    Email
                </label>

                <input
                    type="email"
                    name="email"
                    class="form-control"
                    value="{{ old(
                        'email',
                        $admissionEnquiry->email ?? ''
                    ) }}"
                >

            </div>

        </div>

    </div>

</div>


{{-- Address --}}
<div class="card border-0 bg-light mb-4">

    <div class="card-body">

        <h6 class="mb-3">
            Address / Previous School
        </h6>

        <div class="row">

            <div class="col-md-6 mb-3">

                <label class="form-label">
                    Address
                </label>

                <textarea
                    name="address"
                    rows="3"
                    class="form-control"
                >{{ old(
                    'address',
                    $admissionEnquiry->address ?? ''
                ) }}</textarea>

            </div>


            <div class="col-md-3 mb-3">

                <label class="form-label">
                    City
                </label>

                <input
                    type="text"
                    name="city"
                    class="form-control"
                    value="{{ old(
                        'city',
                        $admissionEnquiry->city ?? ''
                    ) }}"
                >

            </div>


            <div class="col-md-3 mb-3">

                <label class="form-label">
                    PIN Code
                </label>

                <input
                    type="text"
                    name="pin_code"
                    maxlength="10"
                    class="form-control"
                    value="{{ old(
                        'pin_code',
                        $admissionEnquiry->pin_code ?? ''
                    ) }}"
                >

            </div>


            <div class="col-md-6 mb-3">

                <label class="form-label">
                    Previous School
                </label>

                <input
                    type="text"
                    name="previous_school"
                    class="form-control"
                    value="{{ old(
                        'previous_school',
                        $admissionEnquiry->previous_school ?? ''
                    ) }}"
                >

            </div>

        </div>

    </div>

</div>


{{-- Lead / Status --}}
<div class="card border-0 bg-light mb-3">

    <div class="card-body">

        <h6 class="mb-3">
            Enquiry Status & Source
        </h6>

        <div class="row">

            <div class="col-md-4 mb-3">

                <label class="form-label">
                    Enquiry Source
                </label>

                <select
                    name="source"
                    class="form-select"
                >

                    <option value="">
                        Select Source
                    </option>

                    @foreach([
                        'Website',
                        'Facebook',
                        'Instagram',
                        'Google',
                        'Walk-in',
                        'Phone Call',
                        'Existing Parent',
                        'Student Reference',
                        'Staff Reference',
                        'Advertisement',
                        'Other'
                    ] as $source)

                        <option
                            value="{{ $source }}"
                            @selected(
                                old(
                                    'source',
                                    $admissionEnquiry->source ?? ''
                                ) == $source
                            )
                        >
                            {{ $source }}
                        </option>

                    @endforeach

                </select>

            </div>


            <div class="col-md-4 mb-3">

                <label class="form-label">
                    Reference Name
                </label>

                <input
                    type="text"
                    name="reference_name"
                    class="form-control"
                    value="{{ old(
                        'reference_name',
                        $admissionEnquiry->reference_name ?? ''
                    ) }}"
                >

            </div>


            <div class="col-md-4 mb-3">

                <label class="form-label">
                    Enquiry Status
                    <span class="text-danger">*</span>
                </label>

                <select
                    name="enquiry_status"
                    class="form-select"
                >

                    @foreach([
                        'open' => 'Open',
                        'follow_up' => 'Follow Up',
                        'interested' => 'Interested',
                        'not_interested' => 'Not Interested',
                        'closed' => 'Closed',
                    ] as $value => $label)

                        <option
                            value="{{ $value }}"
                            @selected(
                                old(
                                    'enquiry_status',
                                    $admissionEnquiry->enquiry_status ?? 'open'
                                ) == $value
                            )
                        >
                            {{ $label }}
                        </option>

                    @endforeach

                </select>

            </div>


            <div class="col-md-4 mb-3">

                <label class="form-label">
                    Next Follow-up Date
                </label>

                <input
                    type="date"
                    name="next_followup_date"
                    class="form-control"
                    value="{{ old(
                        'next_followup_date',
                        isset($admissionEnquiry)
                        && $admissionEnquiry->next_followup_date
                            ? $admissionEnquiry
                                ->next_followup_date
                                ->format('Y-m-d')
                            : ''
                    ) }}"
                >

            </div>


            <div class="col-md-8 mb-3">

                <label class="form-label">
                    Remarks
                </label>

                <textarea
                    name="remarks"
                    rows="2"
                    class="form-control"
                >{{ old(
                    'remarks',
                    $admissionEnquiry->remarks ?? ''
                ) }}</textarea>

            </div>


            <div class="col-md-4">

                <div class="form-check">

                    <input
                        type="checkbox"
                        name="status"
                        value="1"
                        id="status"
                        class="form-check-input"
                        @checked(
                            old(
                                'status',
                                $admissionEnquiry->status ?? true
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

        </div>

    </div>

</div>