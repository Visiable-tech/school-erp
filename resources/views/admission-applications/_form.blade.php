@php
    $app = $admissionApplication ?? null;
    $enquiry = $admissionEnquiry ?? $app?->enquiry;
@endphp


{{-- Student Details --}}
<div class="card border-0 shadow-sm mb-4">

    <div class="card-header bg-white py-3">
        <strong>Student Details</strong>
    </div>

    <div class="card-body">

        <div class="row">

            <div class="col-md-4 mb-3">

                <label class="form-label">
                    Application Date *
                </label>

                <input
                    type="date"
                    name="application_date"
                    class="form-control"
                    value="{{ old(
                        'application_date',
                        $app?->application_date?->format('Y-m-d')
                            ?? now()->format('Y-m-d')
                    ) }}"
                    required
                >

            </div>


            <div class="col-md-5 mb-3">

                <label class="form-label">
                    Student Name *
                </label>

                <input
                    type="text"
                    name="student_name"
                    class="form-control"
                    value="{{ old(
                        'student_name',
                        $app?->student_name
                            ?? $enquiry?->student_name
                    ) }}"
                    required
                >

            </div>


            <div class="col-md-3 mb-3">

                <label class="form-label">
                    DOB
                </label>

                <input
                    type="date"
                    name="date_of_birth"
                    class="form-control"
                    value="{{ old(
                        'date_of_birth',
                        $app?->date_of_birth?->format('Y-m-d')
                            ?? $enquiry?->date_of_birth?->format('Y-m-d')
                    ) }}"
                >

            </div>


            <div class="col-md-3 mb-3">

                <label class="form-label">
                    Gender
                </label>

                <select name="gender" class="form-select">

                    <option value="">Select</option>

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
                                    $app?->gender
                                        ?? $enquiry?->gender
                                ) == $value
                            )
                        >
                            {{ $label }}
                        </option>

                    @endforeach

                </select>

            </div>


            <div class="col-md-3 mb-3">

                <label class="form-label">
                    Blood Group
                </label>

                <select
                    name="blood_group"
                    class="form-select"
                >

                    <option value="">Select</option>

                    @foreach([
                        'A+','A-',
                        'B+','B-',
                        'AB+','AB-',
                        'O+','O-'
                    ] as $group)

                        <option
                            value="{{ $group }}"
                            @selected(
                                old(
                                    'blood_group',
                                    $app?->blood_group
                                ) == $group
                            )
                        >
                            {{ $group }}
                        </option>

                    @endforeach

                </select>

            </div>


            <div class="col-md-3 mb-3">

                <label class="form-label">
                    Nationality
                </label>

                <input
                    type="text"
                    name="nationality"
                    class="form-control"
                    value="{{ old(
                        'nationality',
                        $app?->nationality ?? 'Indian'
                    ) }}"
                >

            </div>


            <div class="col-md-3 mb-3">

                <label class="form-label">
                    Religion
                </label>

                <input
                    type="text"
                    name="religion"
                    class="form-control"
                    value="{{ old(
                        'religion',
                        $app?->religion
                    ) }}"
                >

            </div>


            <div class="col-md-3 mb-3">

                <label class="form-label">
                    Category
                </label>

                <select
                    name="category"
                    class="form-select"
                >

                    <option value="">Select</option>

                    @foreach([
                        'General',
                        'SC',
                        'ST',
                        'OBC',
                        'Other'
                    ] as $category)

                        <option
                            value="{{ $category }}"
                            @selected(
                                old(
                                    'category',
                                    $app?->category
                                ) == $category
                            )
                        >
                            {{ $category }}
                        </option>

                    @endforeach

                </select>

            </div>


            <div class="col-md-3 mb-3">

                <label class="form-label">
                    Mother Tongue
                </label>

                <input
                    type="text"
                    name="mother_tongue"
                    class="form-control"
                    value="{{ old(
                        'mother_tongue',
                        $app?->mother_tongue
                    ) }}"
                >

            </div>

        </div>

    </div>

</div>


{{-- Parent Details --}}
<div class="card border-0 shadow-sm mb-4">

    <div class="card-header bg-white py-3">
        <strong>Parent Details</strong>
    </div>

    <div class="card-body">

        <div class="row">

            <div class="col-md-3 mb-3">
                <label class="form-label">Father Name</label>

                <input
                    name="father_name"
                    class="form-control"
                    value="{{ old(
                        'father_name',
                        $app?->father_name
                            ?? $enquiry?->father_name
                    ) }}"
                >
            </div>

            <div class="col-md-3 mb-3">
                <label class="form-label">Father Mobile</label>

                <input
                    name="father_mobile"
                    class="form-control"
                    value="{{ old(
                        'father_mobile',
                        $app?->father_mobile
                            ?? $enquiry?->mobile
                    ) }}"
                >
            </div>

            <div class="col-md-3 mb-3">
                <label class="form-label">Father Email</label>

                <input
                    type="email"
                    name="father_email"
                    class="form-control"
                    value="{{ old(
                        'father_email',
                        $app?->father_email
                            ?? $enquiry?->email
                    ) }}"
                >
            </div>

            <div class="col-md-3 mb-3">
                <label class="form-label">Father Occupation</label>

                <input
                    name="father_occupation"
                    class="form-control"
                    value="{{ old(
                        'father_occupation',
                        $app?->father_occupation
                    ) }}"
                >
            </div>


            <div class="col-md-3 mb-3">
                <label class="form-label">Mother Name</label>

                <input
                    name="mother_name"
                    class="form-control"
                    value="{{ old(
                        'mother_name',
                        $app?->mother_name
                            ?? $enquiry?->mother_name
                    ) }}"
                >
            </div>

            <div class="col-md-3 mb-3">
                <label class="form-label">Mother Mobile</label>

                <input
                    name="mother_mobile"
                    class="form-control"
                    value="{{ old(
                        'mother_mobile',
                        $app?->mother_mobile
                    ) }}"
                >
            </div>

            <div class="col-md-3 mb-3">
                <label class="form-label">Mother Email</label>

                <input
                    type="email"
                    name="mother_email"
                    class="form-control"
                    value="{{ old(
                        'mother_email',
                        $app?->mother_email
                    ) }}"
                >
            </div>

            <div class="col-md-3 mb-3">
                <label class="form-label">Mother Occupation</label>

                <input
                    name="mother_occupation"
                    class="form-control"
                    value="{{ old(
                        'mother_occupation',
                        $app?->mother_occupation
                    ) }}"
                >
            </div>

        </div>

    </div>

</div>


{{-- Guardian --}}
<div class="card border-0 shadow-sm mb-4">

    <div class="card-header bg-white py-3">
        <strong>Guardian Details</strong>
    </div>

    <div class="card-body">

        <div class="row">

            <div class="col-md-4 mb-3">
                <label class="form-label">Guardian Name</label>

                <input
                    name="guardian_name"
                    class="form-control"
                    value="{{ old(
                        'guardian_name',
                        $app?->guardian_name
                            ?? $enquiry?->guardian_name
                    ) }}"
                >
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">Relation</label>

                <input
                    name="guardian_relation"
                    class="form-control"
                    value="{{ old(
                        'guardian_relation',
                        $app?->guardian_relation
                    ) }}"
                >
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">Guardian Mobile</label>

                <input
                    name="guardian_mobile"
                    class="form-control"
                    value="{{ old(
                        'guardian_mobile',
                        $app?->guardian_mobile
                    ) }}"
                >
            </div>

        </div>

    </div>

</div>


{{-- Present Address --}}
<div class="card border-0 shadow-sm mb-4">

    <div class="card-header bg-white py-3">
        <strong>Present Address</strong>
    </div>

    <div class="card-body">

        <div class="row">

            <div class="col-md-6 mb-3">

                <textarea
                    name="present_address"
                    class="form-control"
                    rows="3"
                    placeholder="Address"
                >{{ old(
                    'present_address',
                    $app?->present_address
                        ?? $enquiry?->address
                ) }}</textarea>

            </div>

            <div class="col-md-2 mb-3">
                <input
                    name="present_city"
                    class="form-control"
                    placeholder="City"
                    value="{{ old(
                        'present_city',
                        $app?->present_city
                            ?? $enquiry?->city
                    ) }}"
                >
            </div>

            <div class="col-md-2 mb-3">
                <input
                    name="present_state"
                    class="form-control"
                    placeholder="State"
                    value="{{ old(
                        'present_state',
                        $app?->present_state
                    ) }}"
                >
            </div>

            <div class="col-md-2 mb-3">
                <input
                    name="present_pin_code"
                    class="form-control"
                    placeholder="PIN"
                    value="{{ old(
                        'present_pin_code',
                        $app?->present_pin_code
                            ?? $enquiry?->pin_code
                    ) }}"
                >
            </div>

        </div>

    </div>

</div>


{{-- Permanent Address --}}
<div class="card border-0 shadow-sm mb-4">

    <div class="card-header bg-white py-3">
        <strong>Permanent Address</strong>
    </div>

    <div class="card-body">

        <div class="row">

            <div class="col-md-6 mb-3">

                <textarea
                    name="permanent_address"
                    class="form-control"
                    rows="3"
                    placeholder="Address"
                >{{ old(
                    'permanent_address',
                    $app?->permanent_address
                ) }}</textarea>

            </div>

            <div class="col-md-2 mb-3">
                <input
                    name="permanent_city"
                    class="form-control"
                    placeholder="City"
                    value="{{ old(
                        'permanent_city',
                        $app?->permanent_city
                    ) }}"
                >
            </div>

            <div class="col-md-2 mb-3">
                <input
                    name="permanent_state"
                    class="form-control"
                    placeholder="State"
                    value="{{ old(
                        'permanent_state',
                        $app?->permanent_state
                    ) }}"
                >
            </div>

            <div class="col-md-2 mb-3">
                <input
                    name="permanent_pin_code"
                    class="form-control"
                    placeholder="PIN"
                    value="{{ old(
                        'permanent_pin_code',
                        $app?->permanent_pin_code
                    ) }}"
                >
            </div>

        </div>

    </div>

</div>


{{-- Previous School --}}
<div class="card border-0 shadow-sm mb-4">

    <div class="card-header bg-white py-3">
        <strong>Previous School</strong>
    </div>

    <div class="card-body">

        <div class="row">

            <div class="col-md-4 mb-3">

                <input
                    name="previous_school"
                    class="form-control"
                    placeholder="Previous School"
                    value="{{ old(
                        'previous_school',
                        $app?->previous_school
                            ?? $enquiry?->previous_school
                    ) }}"
                >

            </div>

            <div class="col-md-4 mb-3">

                <input
                    name="previous_class"
                    class="form-control"
                    placeholder="Previous Class"
                    value="{{ old(
                        'previous_class',
                        $app?->previous_class
                    ) }}"
                >

            </div>

            <div class="col-md-4 mb-3">

                <input
                    name="previous_board"
                    class="form-control"
                    placeholder="Board"
                    value="{{ old(
                        'previous_board',
                        $app?->previous_board
                    ) }}"
                >

            </div>


            <div class="col-md-12">

                <label class="form-label">
                    Remarks
                </label>

                <textarea
                    name="remarks"
                    class="form-control"
                    rows="3"
                >{{ old(
                    'remarks',
                    $app?->remarks
                ) }}</textarea>

            </div>

        </div>

    </div>

</div>

<div class="card border-0 shadow-sm mt-4">

    <div class="card-header bg-white py-3">

        <strong>
            Application Documents
        </strong>

    </div>

    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-hover align-middle">

                <thead class="table-light">

                    <tr>
                        <th>Document</th>
                        <th>Number</th>
                        <th>File</th>
                        <th>Status</th>
                        <th>Verification</th>
                        <th>Action</th>
                    </tr>

                </thead>

                <tbody>

                @forelse(
                    ($app?->documents ?? collect())
                    as $document
                )

                    <tr>

                        <td>

                            <strong>
                                {{ $document->document_name }}
                            </strong>

                            <div class="small text-muted">

                                {{
                                    ucwords(
                                        str_replace(
                                            '_',
                                            ' ',
                                            $document->document_type
                                        )
                                    )
                                }}

                            </div>

                        </td>


                        <td>
                            {{ $document->document_number ?: '-' }}
                        </td>


                        <td>

                            @if($document->file_path)

                                <a
                                    href="{{ asset(
                                        'storage/' .
                                        $document->file_path
                                    ) }}"
                                    target="_blank"
                                    class="btn btn-sm btn-outline-secondary"
                                >
                                    <i class="bi bi-eye"></i>
                                    View
                                </a>

                            @endif

                        </td>


                        <td>

                            @if(
                                $document->verification_status
                                === 'verified'
                            )

                                <span class="badge bg-success">
                                    Verified
                                </span>

                            @elseif(
                                $document->verification_status
                                === 'rejected'
                            )

                                <span class="badge bg-danger">
                                    Rejected
                                </span>

                            @else

                                <span class="badge bg-warning text-dark">
                                    Pending
                                </span>

                            @endif

                        </td>


                        <td>

                            @can('admission-document.verify')

                                <form
                                    method="POST"
                                    action="{{ route(
                                        'admission-documents.verify',
                                        $document
                                    ) }}"
                                >

                                    @csrf
                                    @method('PUT')

                                    <div class="input-group input-group-sm">

                                        <select
                                            name="verification_status"
                                            class="form-select"
                                            required
                                        >

                                            <option value="verified">
                                                Verify
                                            </option>

                                            <option value="rejected">
                                                Reject
                                            </option>

                                        </select>

                                        <button
                                            class="btn btn-outline-primary"
                                        >
                                            Save
                                        </button>

                                    </div>

                                    <input
                                        type="text"
                                        name="verification_remarks"
                                        class="form-control form-control-sm mt-1"
                                        placeholder="Remarks"
                                    >

                                </form>

                            @else

                                {{ $document->verifier?->name ?? '-' }}

                            @endcan

                        </td>


                        <td>

                            @can('admission-document.delete')

                                <form
                                    method="POST"
                                    action="{{ route(
                                        'admission-documents.destroy',
                                        $document
                                    ) }}"
                                    onsubmit="return confirm('Delete this document?')"
                                >

                                    @csrf
                                    @method('DELETE')

                                    <button
                                        class="btn btn-sm btn-outline-danger"
                                    >
                                        <i class="bi bi-trash"></i>
                                    </button>

                                </form>

                            @endcan

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="6"
                            class="text-center text-muted py-4"
                        >
                            No documents uploaded.
                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>