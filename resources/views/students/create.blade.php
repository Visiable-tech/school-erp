@extends('layouts.admin')

@section('title', 'Add Student')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h4 class="mb-1">Add Student</h4>
        <div class="text-muted">Direct Student Admission</div>
    </div>

    <div class="d-flex gap-2">

        @can('student.create')
            <a
                href="{{ route('students.import') }}"
                class="btn btn-success"
            >
                <i class="bi bi-file-earmark-excel"></i>
                Import Excel
            </a>
        @endcan

        <a
            href="{{ route('students.index') }}"
            class="btn btn-outline-secondary"
        >
            Student List
        </a>

    </div>

</div>


<form
    action="{{ route('students.store') }}"
    method="POST"
>

@csrf


{{-- ACADEMIC --}}

<div class="card border-0 shadow-sm mb-4">

    <div class="card-header bg-white py-3">
        <strong>Admission & Academic Information</strong>
    </div>

    <div class="card-body">

        <div class="row g-3">

            <div class="col-md-3">

                <label class="form-label">
                    Admission Date *
                </label>

                <input
                    type="date"
                    name="admission_date"
                    value="{{ old('admission_date', now()->format('Y-m-d')) }}"
                    class="form-control @error('admission_date') is-invalid @enderror"
                    required
                >

                @error('admission_date')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror

            </div>


            <div class="col-md-3">

                <label class="form-label">
                    Academic Year *
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
                                old(
                                    'academic_year_id',
                                    $currentAcademicYear?->id
                                ) == $year->id
                            )
                        >
                            {{ $year->name }}
                        </option>

                    @endforeach

                </select>

            </div>


            <div class="col-md-3">

                <label class="form-label">
                    Class *
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
                                old('school_class_id') == $class->id
                            )
                        >
                            {{ $class->name }}
                        </option>

                    @endforeach

                </select>

            </div>


            <div class="col-md-3">

                <label class="form-label">
                    Section *
                </label>

                <select
                    name="section_id"
                    id="section_id"
                    data-old="{{ old('section_id') }}"
                    class="form-select"
                    required
                >

                    <option value="">
                        Select Year & Class
                    </option>

                </select>

            </div>


            <div class="col-md-3">

                <label class="form-label">
                    Roll No.
                </label>

                <input
                    type="text"
                    name="roll_no"
                    value="{{ old('roll_no') }}"
                    class="form-control"
                >

            </div>

        </div>

    </div>

</div>


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
                    value="{{ old('student_name') }}"
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
                    value="{{ old('date_of_birth') }}"
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
                            @selected(old('gender') === $gender)
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
                            @selected(old('blood_group') === $blood)
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
                    value="{{ old('religion') }}"
                    class="form-control"
                >
            </div>


            <div class="col-md-3">
                <label class="form-label">Category</label>

                <input
                    type="text"
                    name="category"
                    value="{{ old('category') }}"
                    class="form-control"
                >
            </div>


            <div class="col-md-3">
                <label class="form-label">Mother Tongue</label>

                <input
                    type="text"
                    name="mother_tongue"
                    value="{{ old('mother_tongue') }}"
                    class="form-control"
                >
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
                        value="{{ old($field) }}"
                        class="form-control"
                    >

                </div>

            @endforeach

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
                        value="{{ old($field) }}"
                        class="form-control"
                    >

                </div>

            @endforeach

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
                    value="{{ old('guardian_name') }}"
                    class="form-control"
                >
            </div>

            <div class="col-md-4">
                <label class="form-label">Relation</label>

                <input
                    type="text"
                    name="guardian_relation"
                    value="{{ old('guardian_relation') }}"
                    class="form-control"
                >
            </div>

            <div class="col-md-4">
                <label class="form-label">Mobile</label>

                <input
                    type="text"
                    name="guardian_mobile"
                    value="{{ old('guardian_mobile') }}"
                    class="form-control"
                >
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
                >{{ old('present_address') }}</textarea>

            </div>

            <div class="col-md-4">
                <input
                    name="present_city"
                    value="{{ old('present_city') }}"
                    class="form-control"
                    placeholder="City"
                >
            </div>

            <div class="col-md-4">
                <input
                    name="present_state"
                    value="{{ old('present_state') }}"
                    class="form-control"
                    placeholder="State"
                >
            </div>

            <div class="col-md-4">
                <input
                    name="present_pin_code"
                    value="{{ old('present_pin_code') }}"
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
                >{{ old('permanent_address') }}</textarea>

            </div>

            <div class="col-md-4">
                <input
                    name="permanent_city"
                    id="permanent_city"
                    value="{{ old('permanent_city') }}"
                    class="form-control"
                    placeholder="City"
                >
            </div>

            <div class="col-md-4">
                <input
                    name="permanent_state"
                    id="permanent_state"
                    value="{{ old('permanent_state') }}"
                    class="form-control"
                    placeholder="State"
                >
            </div>

            <div class="col-md-4">
                <input
                    name="permanent_pin_code"
                    id="permanent_pin_code"
                    value="{{ old('permanent_pin_code') }}"
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
                    value="{{ old('previous_school') }}"
                    class="form-control"
                    placeholder="Previous School"
                >

            </div>

            <div class="col-md-4">

                <input
                    name="previous_class"
                    value="{{ old('previous_class') }}"
                    class="form-control"
                    placeholder="Previous Class"
                >

            </div>

            <div class="col-md-4">

                <input
                    name="previous_board"
                    value="{{ old('previous_board') }}"
                    class="form-control"
                    placeholder="Previous Board"
                >

            </div>

        </div>

    </div>

</div>


<div class="mb-5">

    <button
        class="btn btn-primary"
        type="submit"
    >
        <i class="bi bi-person-check"></i>
        Admit Student
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