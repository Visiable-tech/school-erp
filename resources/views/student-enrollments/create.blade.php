@extends('layouts.admin')

@section('title', 'Add Student Enrollment')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h4 class="mb-1">
            Add Academic Enrollment
        </h4>

        <div class="text-muted">
            {{ $student->student_name }}
            |
            {{ $student->admission_no }}
        </div>
    </div>

    <a
        href="{{ route('students.show', $student) }}"
        class="btn btn-outline-secondary"
    >
        <i class="bi bi-arrow-left"></i>
        Back
    </a>

</div>


<div class="card border-0 shadow-sm">

    <div class="card-body">

        <form
            method="POST"
            action="{{ route(
                'student-enrollments.store',
                $student
            ) }}"
        >

            @csrf


            <div class="row g-3">


                {{-- ACADEMIC YEAR --}}

                <div class="col-md-6">

                    <label class="form-label">
                        Academic Year
                        <span class="text-danger">*</span>
                    </label>

                    <select
                        name="academic_year_id"
                        id="academic_year_id"
                        class="form-select @error('academic_year_id') is-invalid @enderror"
                        required
                    >

                        <option value="">
                            Select Academic Year
                        </option>

                        @foreach($academicYears as $year)

                            <option
                                value="{{ $year->id }}"
                                @selected(
                                    old('academic_year_id')
                                    == $year->id
                                )
                            >
                                {{ $year->name }}
                            </option>

                        @endforeach

                    </select>

                    @error('academic_year_id')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>



                {{-- CLASS --}}

                <div class="col-md-6">

                    <label class="form-label">
                        Class
                        <span class="text-danger">*</span>
                    </label>

                    <select
                        name="school_class_id"
                        id="school_class_id"
                        class="form-select @error('school_class_id') is-invalid @enderror"
                        required
                    >

                        <option value="">
                            Select Class
                        </option>

                        @foreach($classes as $class)

                            <option
                                value="{{ $class->id }}"
                                @selected(
                                    old('school_class_id')
                                    == $class->id
                                )
                            >
                                {{ $class->name }}
                            </option>

                        @endforeach

                    </select>

                    @error('school_class_id')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>



                {{-- SECTION --}}

                <div class="col-md-6">

                    <label class="form-label">
                        Section
                        <span class="text-danger">*</span>
                    </label>

                    <select
                        name="section_id"
                        id="section_id"
                        class="form-select @error('section_id') is-invalid @enderror"
                        required
                    >

                        <option value="">
                            Select Academic Year and Class
                        </option>

                    </select>

                    @error('section_id')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>



                {{-- ROLL NUMBER --}}

                <div class="col-md-3">

                    <label class="form-label">
                        Roll Number
                    </label>

                    <input
                        type="text"
                        name="roll_no"
                        class="form-control @error('roll_no') is-invalid @enderror"
                        value="{{ old('roll_no') }}"
                    >

                    @error('roll_no')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>



                {{-- ENROLLMENT DATE --}}

                <div class="col-md-3">

                    <label class="form-label">
                        Enrollment Date
                        <span class="text-danger">*</span>
                    </label>

                    <input
                        type="date"
                        name="enrollment_date"
                        class="form-control"
                        value="{{ old(
                            'enrollment_date',
                            now()->format('Y-m-d')
                        ) }}"
                        required
                    >

                </div>

            </div>


            <hr class="my-4">


            <button
                type="submit"
                class="btn btn-primary"
            >
                <i class="bi bi-plus-circle"></i>
                Create Enrollment
            </button>


            <a
                href="{{ route('students.show', $student) }}"
                class="btn btn-outline-secondary"
            >
                Cancel
            </a>

        </form>

    </div>

</div>


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


        function loadSections() {

            const yearId =
                year.value;

            const classId =
                schoolClass.value;


            section.innerHTML =
                '<option value="">Select Section</option>';


            if (!yearId || !classId) {
                return;
            }


            const url =
                "{{ route('student-enrollments.sections') }}"
                + '?academic_year_id='
                + encodeURIComponent(yearId)
                + '&school_class_id='
                + encodeURIComponent(classId);


            fetch(url, {
                headers: {
                    'Accept':
                        'application/json'
                }
            })
            .then(response => {

                if (!response.ok) {
                    throw new Error(
                        'Unable to load sections.'
                    );
                }

                return response.json();

            })
            .then(data => {

                section.innerHTML =
                    '<option value="">Select Section</option>';


                data.forEach(item => {

                    const option =
                        document.createElement(
                            'option'
                        );

                    option.value =
                        item.id;


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


                    @if(old('section_id'))

                        if (
                            String(item.id)
                            ===
                            "{{ old('section_id') }}"
                        ) {

                            option.selected =
                                true;

                        }

                    @endif


                    section.appendChild(
                        option
                    );

                });

            })
            .catch(error => {

                console.error(error);

                section.innerHTML =
                    '<option value="">Unable to load sections</option>';

            });

        }


        year.addEventListener(
            'change',
            loadSections
        );


        schoolClass.addEventListener(
            'change',
            loadSections
        );


        if (
            year.value
            &&
            schoolClass.value
        ) {

            loadSections();

        }

    }
);

</script>

@endsection