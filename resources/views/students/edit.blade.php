@extends('layouts.admin')

@section('title', 'Edit Student')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h4 class="mb-1">Edit Student</h4>
        <div class="text-muted">{{ $student->student_name }} | {{ $student->admission_no }}</div>
    </div>

    <div class="d-flex gap-2">

        <a
            href="{{ route('students.index') }}"
            class="btn btn-outline-secondary"
        >
            Student List
        </a>

    </div>

</div>


<form
    action="{{ route('students.update', $student) }}"
    method="POST"
    enctype="multipart/form-data"
>

@csrf
@method("PUT")


{{-- ACADEMIC --}}
<div class="card border-0 shadow-sm mb-4"><div class="card-header bg-white py-3"><strong>Admission & Academic Information</strong></div><div class="card-body"><div class="row g-3">
@foreach(['Admission No.' => $student->admission_no, 'Admission Date' => $student->admission_date, 'Academic Year' => $student->currentEnrollment?->academicYear?->name, 'Class' => $student->currentEnrollment?->schoolClass?->name, 'Section' => $student->currentEnrollment?->section?->name, 'Roll No.' => $student->currentEnrollment?->roll_no] as $label => $value)
<div class="col-md-4"><label class="form-label text-muted">{{ $label }}</label><div class="form-control bg-light">{{ $value ?: '—' }}</div></div>
@endforeach
</div><small class="text-muted">Use Student Enrollment / Promotion to change class, section, academic year or roll number.</small></div></div>
{{-- STUDENT --}}

<div class="card border-0 shadow-sm mb-4">

    <div class="card-header bg-white py-3">
        <strong>Student Information</strong>
    </div>

    <div class="card-body">

        <div class="row g-3">

            <div class="col-md-6">

                <label class="form-label">
                    Student Name *
                </label>

                <input
                    type="text"
                    name="student_name"
                    value="{{ old('student_name', $student->student_name) }}"
                    class="form-control"
                    required
                >

            </div>


            <div class="col-md-3">

                <label class="form-label">
                    Date of Birth
                </label>

                <input
                    type="date"
                    name="date_of_birth"
                    value="{{ old('date_of_birth', $student->date_of_birth) }}"
                    class="form-control"
                >

            </div>


            <div class="col-md-3">

                <label class="form-label">
                    Gender
                </label>

                <select
                    name="gender"
                    class="form-select"
                >

                    <option value="">Select</option>

                    @foreach(['Male','Female','Other'] as $gender)

                        <option
                            value="{{ $gender }}"
                            @selected(old('gender', $student->gender) === $gender)
                        >
                            {{ $gender }}
                        </option>

                    @endforeach

                </select>

            </div>


            <div class="col-md-3">

                <label class="form-label">
                    Blood Group
                </label>

                <select
                    name="blood_group"
                    class="form-select"
                >

                    <option value="">Select</option>

                    @foreach([
                        'A+','A-','B+','B-',
                        'AB+','AB-','O+','O-'
                    ] as $blood)

                        <option
                            value="{{ $blood }}"
                            @selected(old('blood_group', $student->blood_group) === $blood)
                        >
                            {{ $blood }}
                        </option>

                    @endforeach

                </select>

            </div>


            <div class="col-md-3">
                <label class="form-label">Nationality</label>

                <input
                    type="text"
                    name="nationality"
                    value="{{ old('nationality', 'Indian') }}"
                    class="form-control"
                >
            </div>


            <div class="col-md-3">
                <label class="form-label">Religion</label>

                <input
                    type="text"
                    name="religion"
                    value="{{ old('religion', $student->religion) }}"
                    class="form-control"
                >
            </div>


            <div class="col-md-3">
                <label class="form-label">Category</label>

                <input
                    type="text"
                    name="category"
                    value="{{ old('category', $student->category) }}"
                    class="form-control"
                >
            </div>


            <div class="col-md-3">
                <label class="form-label">Mother Tongue</label>

                <input
                    type="text"
                    name="mother_tongue"
                    value="{{ old('mother_tongue', $student->mother_tongue) }}"
                    class="form-control"
                >
            </div>

            <div class="col-md-3">
                <label for="photo_student" class="form-label">Student Photo</label>
                <div class="border rounded p-2 text-center bg-light">
                <img src="{{ !empty($photoPaths['student']) ? asset('storage/'.$photoPaths['student']) : '' }}" id="preview_student" alt="Student Photo preview" style="height:115px;max-width:100%;object-fit:cover;display:{{ !empty($photoPaths['student']) ? 'inline-block' : 'none' }}" class="rounded mb-2">
                <input id="photo_student" class="form-control form-control-sm" type="file" name="photos[student]" accept="image/jpeg,image/png,image/webp" data-photo-preview="preview_student">
                </div>
                @error('photos.student')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>

        </div>

    </div>

</div>


{{-- FATHER --}}

<div class="card border-0 shadow-sm mb-4">

    <div class="card-header bg-white py-3">
        <strong>Father Information</strong>
    </div>

    <div class="card-body">

        <div class="row g-3">

            @foreach([
                'father_name' => 'Father Name',
                'father_mobile' => 'Mobile',
                'father_email' => 'Email',
                'father_occupation' => 'Occupation'
            ] as $field => $label)

                <div class="col-md-3">

                    <label class="form-label">
                        {{ $label }}
                    </label>

                    <input
                        type="{{ $field === 'father_email' ? 'email' : 'text' }}"
                        name="{{ $field }}"
                        value="{{ old($field, data_get($student, $field)) }}"
                        class="form-control"
                    >

                </div>

            @endforeach
<!-- Additional father details -->
<div class="col-md-3">
    <label class="form-label" for="father_aadhaar_last4">Aadhaar Last 4 Digits</label>
    <input class="form-control @error('father_aadhaar_last4') is-invalid @enderror" type="text" name="father_aadhaar_last4" id="father_aadhaar_last4" value="{{ old('father_aadhaar_last4', data_get($extraDetails, 'father_aadhaar_last4')) }}" maxlength="4" inputmode="numeric" pattern="[0-9]{4}">
    @error('father_aadhaar_last4')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="col-md-3">
    <label class="form-label" for="father_nationality">Nationality</label>
    <input class="form-control @error('father_nationality') is-invalid @enderror" type="text" name="father_nationality" id="father_nationality" value="{{ old('father_nationality', data_get($extraDetails, 'father_nationality', 'Indian')) }}" maxlength="255">
    @error('father_nationality')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="col-md-3">
    <label class="form-label" for="father_qualification">Educational Qualification</label>
    <input class="form-control @error('father_qualification') is-invalid @enderror" type="text" name="father_qualification" id="father_qualification" value="{{ old('father_qualification', data_get($extraDetails, 'father_qualification')) }}" maxlength="255">
    @error('father_qualification')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="col-md-3">
    <label class="form-label" for="father_organization">Organization / Workplace</label>
    <input class="form-control @error('father_organization') is-invalid @enderror" type="text" name="father_organization" id="father_organization" value="{{ old('father_organization', data_get($extraDetails, 'father_organization')) }}" maxlength="255">
    @error('father_organization')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="col-md-3">
    <label class="form-label" for="father_work_address">Work Address</label>
    <input class="form-control @error('father_work_address') is-invalid @enderror" type="text" name="father_work_address" id="father_work_address" value="{{ old('father_work_address', data_get($extraDetails, 'father_work_address')) }}" maxlength="255">
    @error('father_work_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="col-md-3">
    <label class="form-label" for="father_address">Residential Address</label>
    <input class="form-control @error('father_address') is-invalid @enderror" type="text" name="father_address" id="father_address" value="{{ old('father_address', data_get($extraDetails, 'father_address')) }}" maxlength="255">
    @error('father_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="col-md-3">
    <label class="form-label" for="father_city">City</label>
    <input class="form-control @error('father_city') is-invalid @enderror" type="text" name="father_city" id="father_city" value="{{ old('father_city', data_get($extraDetails, 'father_city')) }}" maxlength="255">
    @error('father_city')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="col-md-3">
    <label class="form-label" for="father_state">State</label>
    <input class="form-control @error('father_state') is-invalid @enderror" type="text" name="father_state" id="father_state" value="{{ old('father_state', data_get($extraDetails, 'father_state')) }}" maxlength="255">
    @error('father_state')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="col-md-3">
    <label class="form-label" for="father_pin_code">PIN Code</label>
    <input class="form-control @error('father_pin_code') is-invalid @enderror" type="text" name="father_pin_code" id="father_pin_code" value="{{ old('father_pin_code', data_get($extraDetails, 'father_pin_code')) }}" maxlength="255">
    @error('father_pin_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="col-md-3">
    <label class="form-label" for="father_alternate_mobile">Alternate Mobile</label>
    <input class="form-control @error('father_alternate_mobile') is-invalid @enderror" type="tel" name="father_alternate_mobile" id="father_alternate_mobile" value="{{ old('father_alternate_mobile', data_get($extraDetails, 'father_alternate_mobile')) }}" maxlength="255">
    @error('father_alternate_mobile')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="col-md-3">
        <label for="photo_father" class="form-label">Father Photo</label>
        <div class="border rounded p-2 text-center bg-light">
          <img src="{{ !empty($photoPaths['father']) ? asset('storage/'.$photoPaths['father']) : '' }}" id="preview_father" alt="Father Photo preview" style="height:115px;max-width:100%;object-fit:cover;display:{{ !empty($photoPaths['father']) ? 'inline-block' : 'none' }}" class="rounded mb-2">
          <input id="photo_father" class="form-control form-control-sm" type="file" name="photos[father]" accept="image/jpeg,image/png,image/webp" data-photo-preview="preview_father">
        </div>
        @error('photos.father')<div class="text-danger small">{{ $message }}</div>@enderror
      </div>

        </div>

    </div>

</div>


{{-- MOTHER --}}

<div class="card border-0 shadow-sm mb-4">

    <div class="card-header bg-white py-3">
        <strong>Mother Information</strong>
    </div>

    <div class="card-body">

        <div class="row g-3">

            @foreach([
                'mother_name' => 'Mother Name',
                'mother_mobile' => 'Mobile',
                'mother_email' => 'Email',
                'mother_occupation' => 'Occupation'
            ] as $field => $label)

                <div class="col-md-3">

                    <label class="form-label">
                        {{ $label }}
                    </label>

                    <input
                        type="{{ $field === 'mother_email' ? 'email' : 'text' }}"
                        name="{{ $field }}"
                        value="{{ old($field, data_get($student, $field)) }}"
                        class="form-control"
                    >

                </div>

            @endforeach
<!-- Additional mother details -->
<div class="col-md-3">
    <label class="form-label" for="mother_aadhaar_last4">Aadhaar Last 4 Digits</label>
    <input class="form-control @error('mother_aadhaar_last4') is-invalid @enderror" type="text" name="mother_aadhaar_last4" id="mother_aadhaar_last4" value="{{ old('mother_aadhaar_last4', data_get($extraDetails, 'mother_aadhaar_last4')) }}" maxlength="4" inputmode="numeric" pattern="[0-9]{4}">
    @error('mother_aadhaar_last4')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="col-md-3">
    <label class="form-label" for="mother_nationality">Nationality</label>
    <input class="form-control @error('mother_nationality') is-invalid @enderror" type="text" name="mother_nationality" id="mother_nationality" value="{{ old('mother_nationality', data_get($extraDetails, 'mother_nationality', 'Indian')) }}" maxlength="255">
    @error('mother_nationality')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="col-md-3">
    <label class="form-label" for="mother_qualification">Educational Qualification</label>
    <input class="form-control @error('mother_qualification') is-invalid @enderror" type="text" name="mother_qualification" id="mother_qualification" value="{{ old('mother_qualification', data_get($extraDetails, 'mother_qualification')) }}" maxlength="255">
    @error('mother_qualification')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="col-md-3">
    <label class="form-label" for="mother_organization">Organization / Workplace</label>
    <input class="form-control @error('mother_organization') is-invalid @enderror" type="text" name="mother_organization" id="mother_organization" value="{{ old('mother_organization', data_get($extraDetails, 'mother_organization')) }}" maxlength="255">
    @error('mother_organization')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="col-md-3">
    <label class="form-label" for="mother_work_address">Work Address</label>
    <input class="form-control @error('mother_work_address') is-invalid @enderror" type="text" name="mother_work_address" id="mother_work_address" value="{{ old('mother_work_address', data_get($extraDetails, 'mother_work_address')) }}" maxlength="255">
    @error('mother_work_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="col-md-3">
    <label class="form-label" for="mother_address">Residential Address</label>
    <input class="form-control @error('mother_address') is-invalid @enderror" type="text" name="mother_address" id="mother_address" value="{{ old('mother_address', data_get($extraDetails, 'mother_address')) }}" maxlength="255">
    @error('mother_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="col-md-3">
    <label class="form-label" for="mother_city">City</label>
    <input class="form-control @error('mother_city') is-invalid @enderror" type="text" name="mother_city" id="mother_city" value="{{ old('mother_city', data_get($extraDetails, 'mother_city')) }}" maxlength="255">
    @error('mother_city')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="col-md-3">
    <label class="form-label" for="mother_state">State</label>
    <input class="form-control @error('mother_state') is-invalid @enderror" type="text" name="mother_state" id="mother_state" value="{{ old('mother_state', data_get($extraDetails, 'mother_state')) }}" maxlength="255">
    @error('mother_state')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="col-md-3">
    <label class="form-label" for="mother_pin_code">PIN Code</label>
    <input class="form-control @error('mother_pin_code') is-invalid @enderror" type="text" name="mother_pin_code" id="mother_pin_code" value="{{ old('mother_pin_code', data_get($extraDetails, 'mother_pin_code')) }}" maxlength="255">
    @error('mother_pin_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="col-md-3">
    <label class="form-label" for="mother_alternate_mobile">Alternate Mobile</label>
    <input class="form-control @error('mother_alternate_mobile') is-invalid @enderror" type="tel" name="mother_alternate_mobile" id="mother_alternate_mobile" value="{{ old('mother_alternate_mobile', data_get($extraDetails, 'mother_alternate_mobile')) }}" maxlength="255">
    @error('mother_alternate_mobile')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>


<div class="col-md-3">
        <label for="photo_mother" class="form-label">Mother Photo</label>
        <div class="border rounded p-2 text-center bg-light">
          <img src="{{ !empty($photoPaths['mother']) ? asset('storage/'.$photoPaths['mother']) : '' }}" id="preview_mother" alt="Mother Photo preview" style="height:115px;max-width:100%;object-fit:cover;display:{{ !empty($photoPaths['mother']) ? 'inline-block' : 'none' }}" class="rounded mb-2">
          <input id="photo_mother" class="form-control form-control-sm" type="file" name="photos[mother]" accept="image/jpeg,image/png,image/webp" data-photo-preview="preview_mother">
        </div>
        @error('photos.mother')<div class="text-danger small">{{ $message }}</div>@enderror
      </div>

        </div>

    </div>

</div>


{{-- GUARDIAN --}}

<div class="card border-0 shadow-sm mb-4">

    <div class="card-header bg-white py-3">
        <strong>Guardian Information</strong>
    </div>

    <div class="card-body">

        <div class="row g-3">

            <div class="col-md-4">
                <label class="form-label">Guardian Name</label>

                <input
                    type="text"
                    name="guardian_name"
                    value="{{ old('guardian_name', $student->guardian_name) }}"
                    class="form-control"
                >
            </div>

            <div class="col-md-4">
                <label class="form-label">Relation</label>

                <input
                    type="text"
                    name="guardian_relation"
                    value="{{ old('guardian_relation', $student->guardian_relation) }}"
                    class="form-control"
                >
            </div>

            <div class="col-md-4">
                <label class="form-label">Mobile</label>

                <input
                    type="text"
                    name="guardian_mobile"
                    value="{{ old('guardian_mobile', $student->guardian_mobile) }}"
                    class="form-control"
                >
            </div>
<!-- Additional guardian details -->
<div class="col-md-3">
    <label class="form-label" for="guardian_aadhaar_last4">Aadhaar Last 4 Digits</label>
    <input class="form-control @error('guardian_aadhaar_last4') is-invalid @enderror" type="text" name="guardian_aadhaar_last4" id="guardian_aadhaar_last4" value="{{ old('guardian_aadhaar_last4', data_get($extraDetails, 'guardian_aadhaar_last4')) }}" maxlength="4" inputmode="numeric" pattern="[0-9]{4}">
    @error('guardian_aadhaar_last4')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="col-md-3">
    <label class="form-label" for="guardian_nationality">Nationality</label>
    <input class="form-control @error('guardian_nationality') is-invalid @enderror" type="text" name="guardian_nationality" id="guardian_nationality" value="{{ old('guardian_nationality', data_get($extraDetails, 'guardian_nationality', 'Indian')) }}" maxlength="255">
    @error('guardian_nationality')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="col-md-3">
    <label class="form-label" for="guardian_occupation">Occupation</label>
    <input class="form-control @error('guardian_occupation') is-invalid @enderror" type="text" name="guardian_occupation" id="guardian_occupation" value="{{ old('guardian_occupation', data_get($extraDetails, 'guardian_occupation')) }}" maxlength="255">
    @error('guardian_occupation')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="col-md-3">
    <label class="form-label" for="guardian_email">Email</label>
    <input class="form-control @error('guardian_email') is-invalid @enderror" type="email" name="guardian_email" id="guardian_email" value="{{ old('guardian_email', data_get($extraDetails, 'guardian_email')) }}" maxlength="255">
    @error('guardian_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="col-md-3">
    <label class="form-label" for="guardian_qualification">Educational Qualification</label>
    <input class="form-control @error('guardian_qualification') is-invalid @enderror" type="text" name="guardian_qualification" id="guardian_qualification" value="{{ old('guardian_qualification', data_get($extraDetails, 'guardian_qualification')) }}" maxlength="255">
    @error('guardian_qualification')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="col-md-3">
    <label class="form-label" for="guardian_address">Residential Address</label>
    <input class="form-control @error('guardian_address') is-invalid @enderror" type="text" name="guardian_address" id="guardian_address" value="{{ old('guardian_address', data_get($extraDetails, 'guardian_address')) }}" maxlength="255">
    @error('guardian_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="col-md-3">
    <label class="form-label" for="guardian_city">City</label>
    <input class="form-control @error('guardian_city') is-invalid @enderror" type="text" name="guardian_city" id="guardian_city" value="{{ old('guardian_city', data_get($extraDetails, 'guardian_city')) }}" maxlength="255">
    @error('guardian_city')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="col-md-3">
    <label class="form-label" for="guardian_state">State</label>
    <input class="form-control @error('guardian_state') is-invalid @enderror" type="text" name="guardian_state" id="guardian_state" value="{{ old('guardian_state', data_get($extraDetails, 'guardian_state')) }}" maxlength="255">
    @error('guardian_state')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="col-md-3">
    <label class="form-label" for="guardian_pin_code">PIN Code</label>
    <input class="form-control @error('guardian_pin_code') is-invalid @enderror" type="text" name="guardian_pin_code" id="guardian_pin_code" value="{{ old('guardian_pin_code', data_get($extraDetails, 'guardian_pin_code')) }}" maxlength="255">
    @error('guardian_pin_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="col-md-3">
    <label class="form-label" for="guardian_alternate_mobile">Alternate Mobile</label>
    <input class="form-control @error('guardian_alternate_mobile') is-invalid @enderror" type="tel" name="guardian_alternate_mobile" id="guardian_alternate_mobile" value="{{ old('guardian_alternate_mobile', data_get($extraDetails, 'guardian_alternate_mobile')) }}" maxlength="255">
    @error('guardian_alternate_mobile')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="col-md-3">
        <label for="photo_guardian" class="form-label">Guardian Photo (optional)</label>
        <div class="border rounded p-2 text-center bg-light">
          <img src="{{ !empty($photoPaths['guardian']) ? asset('storage/'.$photoPaths['guardian']) : '' }}" id="preview_guardian" alt="Guardian Photo (optional) preview" style="height:115px;max-width:100%;object-fit:cover;display:{{ !empty($photoPaths['guardian']) ? 'inline-block' : 'none' }}" class="rounded mb-2">
          <input id="photo_guardian" class="form-control form-control-sm" type="file" name="photos[guardian]" accept="image/jpeg,image/png,image/webp" data-photo-preview="preview_guardian">
        </div>
        @error('photos.guardian')<div class="text-danger small">{{ $message }}</div>@enderror
      </div>

        </div>

    </div>

</div>


{{-- PRESENT ADDRESS --}}

<div class="card border-0 shadow-sm mb-4">

    <div class="card-header bg-white py-3">
        <strong>Present Address</strong>
    </div>

    <div class="card-body">

        <div class="row g-3">

            <div class="col-12">

                <textarea
                    name="present_address"
                    rows="2"
                    class="form-control"
                    placeholder="Address"
                >{{ old('present_address', $student->present_address) }}</textarea>

            </div>

            <div class="col-md-4">
                <input
                    name="present_city"
                    value="{{ old('present_city', $student->present_city) }}"
                    class="form-control"
                    placeholder="City"
                >
            </div>

            <div class="col-md-4">
                <input
                    name="present_state"
                    value="{{ old('present_state', $student->present_state) }}"
                    class="form-control"
                    placeholder="State"
                >
            </div>

            <div class="col-md-4">
                <input
                    name="present_pin_code"
                    value="{{ old('present_pin_code', $student->present_pin_code) }}"
                    class="form-control"
                    placeholder="PIN Code"
                >
            </div>

        </div>

    </div>

</div>


{{-- PERMANENT ADDRESS --}}

<div class="card border-0 shadow-sm mb-4">

    <div class="card-header bg-white py-3">

        <div class="d-flex justify-content-between">

            <strong>Permanent Address</strong>

            <div class="form-check">

                <input
                    class="form-check-input"
                    type="checkbox"
                    id="sameAddress"
                >

                <label
                    class="form-check-label"
                    for="sameAddress"
                >
                    Same as Present
                </label>

            </div>

        </div>

    </div>


    <div class="card-body">

        <div class="row g-3">

            <div class="col-12">

                <textarea
                    name="permanent_address"
                    id="permanent_address"
                    rows="2"
                    class="form-control"
                    placeholder="Address"
                >{{ old('permanent_address', $student->permanent_address) }}</textarea>

            </div>

            <div class="col-md-4">
                <input
                    name="permanent_city"
                    id="permanent_city"
                    value="{{ old('permanent_city', $student->permanent_city) }}"
                    class="form-control"
                    placeholder="City"
                >
            </div>

            <div class="col-md-4">
                <input
                    name="permanent_state"
                    id="permanent_state"
                    value="{{ old('permanent_state', $student->permanent_state) }}"
                    class="form-control"
                    placeholder="State"
                >
            </div>

            <div class="col-md-4">
                <input
                    name="permanent_pin_code"
                    id="permanent_pin_code"
                    value="{{ old('permanent_pin_code', $student->permanent_pin_code) }}"
                    class="form-control"
                    placeholder="PIN Code"
                >
            </div>

        </div>

    </div>

</div>


{{-- PREVIOUS SCHOOL --}}

<div class="card border-0 shadow-sm mb-4">

    <div class="card-header bg-white py-3">
        <strong>Previous School</strong>
    </div>

    <div class="card-body">

        <div class="row g-3">

            <div class="col-md-4">

                <input
                    name="previous_school"
                    value="{{ old('previous_school', $student->previous_school) }}"
                    class="form-control"
                    placeholder="Previous School"
                >

            </div>

            <div class="col-md-4">

                <input
                    name="previous_class"
                    value="{{ old('previous_class', $student->previous_class) }}"
                    class="form-control"
                    placeholder="Previous Class"
                >

            </div>

            <div class="col-md-4">

                <input
                    name="previous_board"
                    value="{{ old('previous_board', $student->previous_board) }}"
                    class="form-control"
                    placeholder="Previous Board"
                >

            </div>

        </div>

    </div>

</div>



{{-- Additional student information and family photographs --}}
<div class="card border-0 shadow-sm mb-4">
  <div class="card-header bg-white py-3"><strong>Student & Family Photographs</strong></div>
  <div class="card-body">
    <p class="text-muted small">JPG, PNG or WebP; maximum 2 MB each. Upload a new photo to replace the current photo. Leave blank to keep it.</p>
    <div class="row g-3">

      

      

      

      <div class="col-md-4 col-lg-3">
        <label for="photo_parents" class="form-label">Parents Together Photo</label>
        <div class="border rounded p-2 text-center bg-light">
          <img src="{{ !empty($photoPaths['parents']) ? asset('storage/'.$photoPaths['parents']) : '' }}" id="preview_parents" alt="Parents Together Photo preview" style="height:115px;max-width:100%;object-fit:cover;display:{{ !empty($photoPaths['parents']) ? 'inline-block' : 'none' }}" class="rounded mb-2">
          <input id="photo_parents" class="form-control form-control-sm" type="file" name="photos[parents]" accept="image/jpeg,image/png,image/webp" data-photo-preview="preview_parents">
        </div>
        @error('photos.parents')<div class="text-danger small">{{ $message }}</div>@enderror
      </div>

      
    </div>
  </div>
</div>
<div class="card border-0 shadow-sm mb-4">
  <div class="card-header bg-white py-3"><strong>Emergency, Medical & Pickup Information</strong></div>
  <div class="card-body"><div class="row g-3">
<div class="col-md-4"><label class="form-label" for="emergency_contact_name">Emergency Contact Name</label><input type="text" id="emergency_contact_name" name="emergency_contact_name" value="{{ old('emergency_contact_name', data_get($extraDetails, 'emergency_contact_name')) }}" class="form-control" >@error("emergency_contact_name")<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-md-4"><label class="form-label" for="emergency_contact_mobile">Emergency Contact Mobile</label><input type="tel" id="emergency_contact_mobile" name="emergency_contact_mobile" value="{{ old('emergency_contact_mobile', data_get($extraDetails, 'emergency_contact_mobile')) }}" class="form-control" >@error("emergency_contact_mobile")<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-md-4"><label class="form-label" for="emergency_contact_relation">Emergency Contact Relation</label><input type="text" id="emergency_contact_relation" name="emergency_contact_relation" value="{{ old('emergency_contact_relation', data_get($extraDetails, 'emergency_contact_relation')) }}" class="form-control" >@error("emergency_contact_relation")<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-md-4"><label class="form-label" for="student_aadhaar_last4">Student Aadhaar Last 4 Digits (optional)</label><input type="text" id="student_aadhaar_last4" name="student_aadhaar_last4" value="{{ old('student_aadhaar_last4', data_get($extraDetails, 'student_aadhaar_last4')) }}" class="form-control" maxlength=4 inputmode=numeric pattern=[0-9]{4}>@error("student_aadhaar_last4")<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-md-4"><label class="form-label" for="birth_place">Place of Birth</label><input type="text" id="birth_place" name="birth_place" value="{{ old('birth_place', data_get($extraDetails, 'birth_place')) }}" class="form-control" >@error("birth_place")<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-md-4"><label class="form-label" for="identification_mark">Identification Mark</label><input type="text" id="identification_mark" name="identification_mark" value="{{ old('identification_mark', data_get($extraDetails, 'identification_mark')) }}" class="form-control" >@error("identification_mark")<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-md-4"><label class="form-label" for="medical_allergies">Allergies / Medical Alerts</label><input type="text" id="medical_allergies" name="medical_allergies" value="{{ old('medical_allergies', data_get($extraDetails, 'medical_allergies')) }}" class="form-control" >@error("medical_allergies")<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-md-4"><label class="form-label" for="medical_conditions">Important Medical Conditions</label><input type="text" id="medical_conditions" name="medical_conditions" value="{{ old('medical_conditions', data_get($extraDetails, 'medical_conditions')) }}" class="form-control" >@error("medical_conditions")<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-md-4"><label class="form-label" for="transport_required">Transport Required</label><select class="form-select" id="transport_required" name="transport_required"><option value="">Select</option><option value="yes" {{ old('transport_required', data_get($extraDetails, 'transport_required')) === "yes" ? "selected" : "" }}>Yes</option><option value="no" {{ old('transport_required', data_get($extraDetails, 'transport_required')) === "no" ? "selected" : "" }}>No</option></select>@error("transport_required")<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-md-4"><label class="form-label" for="pickup_person_name">Authorized Pickup Person</label><input type="text" id="pickup_person_name" name="pickup_person_name" value="{{ old('pickup_person_name', data_get($extraDetails, 'pickup_person_name')) }}" class="form-control" >@error("pickup_person_name")<div class="text-danger small">{{ $message }}</div>@enderror</div>
<div class="col-md-4"><label class="form-label" for="pickup_person_mobile">Authorized Pickup Mobile</label><input type="tel" id="pickup_person_mobile" name="pickup_person_mobile" value="{{ old('pickup_person_mobile', data_get($extraDetails, 'pickup_person_mobile')) }}" class="form-control" >@error("pickup_person_mobile")<div class="text-danger small">{{ $message }}</div>@enderror</div>
  </div><p class="text-muted small mt-3 mb-0">Only collect sensitive information needed for school operations; restrict access to authorized staff.</p></div>
</div>
<script>
document.addEventListener('change', function(event) {
  const input = event.target;
  if (!input.matches('[data-photo-preview]')) return;
  const preview = document.getElementById(input.dataset.photoPreview);
  const file = input.files && input.files[0];
  if (!file) { preview.removeAttribute('src'); preview.style.display='none'; return; }
  if (!['image/jpeg','image/png','image/webp'].includes(file.type) || file.size > 2*1024*1024) {
    alert('Please select a JPG, PNG or WebP image up to 2 MB.');
    input.value=''; preview.removeAttribute('src'); preview.style.display='none'; return;
  }
  const reader = new FileReader();
  reader.onload = function(e) { preview.src=e.target.result; preview.style.display='inline-block'; };
  reader.readAsDataURL(file);
});
</script>

<div class="mb-5">

    <button
        class="btn btn-primary"
        type="submit"
    >
        <i class="bi bi-person-check"></i>
        Update Student
    </button>

    <a
        href="{{ route('students.index') }}"
        class="btn btn-outline-secondary"
    >
        Cancel
    </a>

</div>

</form>


<script>
document.addEventListener('DOMContentLoaded', function () {

    const year =
        document.getElementById('academic_year_id');

    const schoolClass =
        document.getElementById('school_class_id');

    const section =
        document.getElementById('section_id');

    const sectionUrl =
        "{{ route('students.direct-admission.sections') }}";


    function loadSections() {

        section.innerHTML =
            '<option value="">Select Section</option>';


        if (!year.value || !schoolClass.value) {
            return;
        }


        fetch(
            sectionUrl
            + '?academic_year_id='
            + encodeURIComponent(year.value)
            + '&school_class_id='
            + encodeURIComponent(schoolClass.value),
            {
                headers: {
                    'Accept': 'application/json'
                }
            }
        )
        .then(response => response.json())
        .then(data => {

            data.forEach(item => {

                const option =
                    document.createElement('option');

                option.value = item.id;

                option.textContent =
                    item.name
                    +
                    (
                        item.capacity
                        ? ' (Capacity: '
                            + item.capacity
                            + ')'
                        : ''
                    );

                if (
                    String(section.dataset.old)
                    === String(item.id)
                ) {
                    option.selected = true;
                }

                section.appendChild(option);
            });

        });
    }


    year.addEventListener('change', loadSections);

    schoolClass.addEventListener(
        'change',
        loadSections
    );


    if (year.value && schoolClass.value) {
        loadSections();
    }


    document.getElementById('sameAddress')
        .addEventListener('change', function () {

            if (!this.checked) {
                return;
            }

            document.getElementById(
                'permanent_address'
            ).value =
                document.querySelector(
                    '[name="present_address"]'
                ).value;

            document.getElementById(
                'permanent_city'
            ).value =
                document.querySelector(
                    '[name="present_city"]'
                ).value;

            document.getElementById(
                'permanent_state'
            ).value =
                document.querySelector(
                    '[name="present_state"]'
                ).value;

            document.getElementById(
                'permanent_pin_code'
            ).value =
                document.querySelector(
                    '[name="present_pin_code"]'
                ).value;

        });

});
</script>

@endsection