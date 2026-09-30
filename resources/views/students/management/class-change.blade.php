@extends('layouts.admin')

@section('title', 'Class Change')

@section('content')

<div class="container-fluid">

    {{-- =========================================================
        HEADER
    ========================================================== --}}

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h4 class="mb-1">
                Class Change
            </h4>

            <div class="text-muted">
                Change a student's current class and section
            </div>

        </div>

        <a
            href="{{ route('students.index') }}"
            class="btn btn-outline-secondary"
        >
            <i class="bi bi-arrow-left me-1"></i>
            Student List
        </a>

    </div>


    {{-- =========================================================
        SUCCESS
    ========================================================== --}}

    @if(session('success'))

        <div class="alert alert-success alert-dismissible fade show">

            <i class="bi bi-check-circle me-1"></i>

            {{ session('success') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>

        </div>

    @endif


    {{-- =========================================================
        ERRORS
    ========================================================== --}}

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
        FILTER
    ========================================================== --}}

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white py-3">

            <strong>
                <i class="bi bi-funnel me-1"></i>
                Find Student
            </strong>

        </div>


        <div class="card-body">

            <form
                method="GET"
                action="{{
                    route(
                        'student-management.class-change'
                    )
                }}"
            >

                <div class="row g-3">

                    {{-- ACADEMIC YEAR --}}

                    <div class="col-xl-3 col-md-6">

                        <label class="form-label">

                            Academic Year

                            <span class="text-danger">
                                *
                            </span>

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


                    {{-- CURRENT CLASS --}}

                    <div class="col-xl-3 col-md-6">

                        <label class="form-label">

                            Current Class

                            <span class="text-danger">
                                *
                            </span>

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


                    {{-- CURRENT SECTION --}}

                    <div class="col-xl-3 col-md-6">

                        <label class="form-label">

                            Current Section

                            <span class="text-danger">
                                *
                            </span>

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

                            <span
                                class="spinner-border spinner-border-sm"
                            ></span>

                            Loading sections...

                        </div>

                    </div>


                    {{-- SEARCH --}}

                    <div class="col-xl-3 col-md-6">

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
                                    'student-management.class-change'
                                )
                            }}"
                            class="btn btn-outline-secondary"
                        >

                            <i
                                class="bi bi-arrow-counterclockwise me-1"
                            ></i>

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

        <div class="card border-0 shadow-sm">

            <div class="card-header bg-white py-3">

                <div
                    class="d-flex justify-content-between align-items-center"
                >

                    <div>

                        <strong>

                            <i class="bi bi-people me-1"></i>

                            Students

                        </strong>

                        <div class="small text-muted mt-1">

                            Select the destination class and section
                            for the required student.

                        </div>

                    </div>


                    <span class="badge bg-primary">

                        {{ $enrollments->count() }}

                    </span>

                </div>

            </div>


            <div class="card-body p-0">

                <div class="class-change-table">

                    <table
                        class="table table-bordered table-striped table-hover align-middle mb-0"
                    >

                        <thead class="table-dark">

                            <tr>

                                <th style="width:60px;">
                                    #
                                </th>

                                <th style="width:150px;">
                                    Admission No.
                                </th>

                                <th style="min-width:220px;">
                                    Student
                                </th>

                                <th style="width:100px;">
                                    Roll
                                </th>

                                <th style="width:150px;">
                                    Current Class
                                </th>

                                <th style="width:130px;">
                                    Section
                                </th>

                                <th style="min-width:200px;">
                                    New Class
                                </th>

                                <th style="min-width:200px;">
                                    New Section
                                </th>

                                <th style="width:140px;">
                                    Action
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                        @forelse(
                            $enrollments
                            as $enrollment
                        )

                            @php

                                $formId =
                                    'class-change-'
                                    . $enrollment->id;

                            @endphp


                            <tr>

                                {{-- SERIAL --}}

                                <td class="text-center">

                                    {{ $loop->iteration }}

                                </td>


                                {{-- ADMISSION --}}

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


                                {{-- STUDENT --}}

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


                                {{-- ROLL --}}

                                <td>

                                    {{
                                        $enrollment->roll_no
                                        ?? '-'
                                    }}

                                </td>


                                {{-- CURRENT CLASS --}}

                                <td>

                                    <span
                                        class="badge bg-primary"
                                    >

                                        {{
                                            $enrollment
                                                ->schoolClass
                                                ?->name
                                            ?? '-'
                                        }}

                                    </span>

                                </td>


                                {{-- CURRENT SECTION --}}

                                <td>

                                    <span
                                        class="badge bg-info text-dark"
                                    >

                                        {{
                                            $enrollment
                                                ->section
                                                ?->name
                                            ?? '-'
                                        }}

                                    </span>

                                </td>


                                {{-- NEW CLASS --}}

                                <td>

                                    <form
                                        id="{{ $formId }}"
                                        method="POST"
                                        action="{{
                                            route(
                                                'student-management.class-change.update'
                                            )
                                        }}"
                                    >

                                        @csrf


                                        <input
                                            type="hidden"
                                            name="enrollment_id"
                                            value="{{
                                                $enrollment->id
                                            }}"
                                        >


                                        <select
                                            name="new_class_id"
                                            class="form-select form-select-sm new-class-select"
                                            data-enrollment="{{
                                                $enrollment->id
                                            }}"
                                            data-year="{{
                                                $enrollment
                                                    ->academic_year_id
                                            }}"
                                            required
                                        >

                                            <option value="">
                                                Select New Class
                                            </option>


                                            @foreach(
                                                $classes
                                                as $class
                                            )

                                                @if(
                                                    $class->id
                                                    !=
                                                    $enrollment
                                                        ->school_class_id
                                                )

                                                    <option
                                                        value="{{
                                                            $class->id
                                                        }}"
                                                    >

                                                        {{ $class->name }}

                                                    </option>

                                                @endif

                                            @endforeach

                                        </select>

                                    </form>

                                </td>


                                {{-- NEW SECTION --}}

                                <td>

                                    <select
                                        form="{{ $formId }}"
                                        name="new_section_id"
                                        id="new-section-{{
                                            $enrollment->id
                                        }}"
                                        class="form-select form-select-sm"
                                        required
                                        disabled
                                    >

                                        <option value="">
                                            Select Class First
                                        </option>

                                    </select>


                                    <div
                                        id="new-section-loading-{{
                                            $enrollment->id
                                        }}"
                                        class="small text-primary mt-1 d-none"
                                    >

                                        <span
                                            class="spinner-border spinner-border-sm"
                                        ></span>

                                        Loading...

                                    </div>

                                </td>


                                {{-- ACTION --}}

                                <td>

                                    <button
                                        type="submit"
                                        form="{{ $formId }}"
                                        class="btn btn-warning btn-sm"
                                        onclick="
                                            return confirm(
                                                'Change this student class? Existing roll number will be cleared.'
                                            );
                                        "
                                    >

                                        <i
                                            class="bi bi-arrow-left-right me-1"
                                        ></i>

                                        Change

                                    </button>

                                </td>

                            </tr>


                        @empty

                            <tr>

                                <td
                                    colspan="9"
                                    class="text-center text-muted py-5"
                                >

                                    <i
                                        class="bi bi-people"
                                        style="font-size:2rem;"
                                    ></i>

                                    <div class="mt-2">

                                        No students found.

                                    </div>

                                </td>

                            </tr>

                        @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    @endif

</div>


{{-- =========================================================
    CSS
========================================================== --}}

<style>

.class-change-table {
    height: 700px;
    max-height: 700px;
    overflow: auto;
    position: relative;
}

.class-change-table table {
    min-width: 1450px;
}

.class-change-table thead th {
    position: sticky;
    top: 0;
    z-index: 10;
    white-space: nowrap;
}

</style>


{{-- =========================================================
    JAVASCRIPT
========================================================== --}}

<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        /*
        |--------------------------------------------------------------------------
        | CURRENT SECTION FILTER
        |--------------------------------------------------------------------------
        */

        const academicYear =
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

        const sectionLoading =
            document.getElementById(
                'sectionLoading'
            );

        const selectedSection =
            "{{ request('section_id') }}";


        function loadCurrentSections(
            keepSelected = false
        ) {

            const yearId =
                academicYear.value;

            const classId =
                schoolClass.value;


            section.innerHTML =
                '<option value="">Select Section</option>';


            if (!yearId || !classId) {
                return;
            }


            section.disabled = true;

            sectionLoading.classList.remove(
                'd-none'
            );


            const url =
                "{{ route('student-management.get-sections') }}"
                +
                '?academic_year_id='
                +
                encodeURIComponent(yearId)
                +
                '&school_class_id='
                +
                encodeURIComponent(classId);


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


                if (
                    data.success &&
                    data.sections.length
                ) {

                    data.sections.forEach(
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
                                keepSelected &&
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

                } else {

                    section.innerHTML =
                        '<option value="">No Section Found</option>';

                }

            })
            .catch(error => {

                console.error(error);

                section.innerHTML =
                    '<option value="">Unable to load sections</option>';

            })
            .finally(() => {

                section.disabled =
                    false;

                sectionLoading.classList.add(
                    'd-none'
                );

            });

        }


        academicYear.addEventListener(
            'change',
            function () {

                loadCurrentSections(false);

            }
        );


        schoolClass.addEventListener(
            'change',
            function () {

                loadCurrentSections(false);

            }
        );


        if (
            academicYear.value &&
            schoolClass.value
        ) {

            loadCurrentSections(true);

        }


        /*
        |--------------------------------------------------------------------------
        | DESTINATION SECTION
        |--------------------------------------------------------------------------
        */

        document
            .querySelectorAll(
                '.new-class-select'
            )
            .forEach(
                function (classSelect) {

                    classSelect.addEventListener(
                        'change',
                        function () {

                            const enrollmentId =
                                this.dataset.enrollment;

                            const yearId =
                                this.dataset.year;

                            const classId =
                                this.value;


                            const sectionSelect =
                                document.getElementById(
                                    'new-section-'
                                    + enrollmentId
                                );


                            const loading =
                                document.getElementById(
                                    'new-section-loading-'
                                    + enrollmentId
                                );


                            sectionSelect.innerHTML =
                                '<option value="">Select Section</option>';


                            if (!classId) {

                                sectionSelect.innerHTML =
                                    '<option value="">Select Class First</option>';

                                sectionSelect.disabled =
                                    true;

                                return;

                            }


                            sectionSelect.disabled =
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
                                    yearId
                                )
                                +
                                '&school_class_id='
                                +
                                encodeURIComponent(
                                    classId
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
                            .then(response => {

                                if (!response.ok) {

                                    throw new Error(
                                        'Unable to load sections.'
                                    );

                                }

                                return response.json();

                            })
                            .then(data => {

                                sectionSelect.innerHTML =
                                    '<option value="">Select Section</option>';


                                if (
                                    data.success &&
                                    data.sections.length
                                ) {

                                    data.sections.forEach(
                                        function (item) {

                                            const option =
                                                document.createElement(
                                                    'option'
                                                );

                                            option.value =
                                                item.id;

                                            option.textContent =
                                                item.name;

                                            sectionSelect
                                                .appendChild(
                                                    option
                                                );

                                        }
                                    );


                                    sectionSelect.disabled =
                                        false;

                                } else {

                                    sectionSelect.innerHTML =
                                        '<option value="">No Section Available</option>';

                                }

                            })
                            .catch(error => {

                                console.error(error);

                                sectionSelect.innerHTML =
                                    '<option value="">Unable to load sections</option>';

                            })
                            .finally(() => {

                                loading.classList.add(
                                    'd-none'
                                );

                            });

                        }
                    );

                }
            );

    }
);

</script>

@endsection