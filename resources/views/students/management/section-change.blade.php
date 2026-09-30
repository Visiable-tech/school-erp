@extends('layouts.admin')

@section('title', 'Section Change')

@section('content')

<div class="container-fluid">

    {{-- PAGE HEADER --}}

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h4 class="mb-1">
                Section Change
            </h4>

            <div class="text-muted">
                Change an individual student's section
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


    {{-- SUCCESS --}}

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


    {{-- ERRORS --}}

    @if($errors->any())

        <div class="alert alert-danger">

            <ul class="mb-0">

                @foreach($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- =====================================================
        FILTER
    ====================================================== --}}

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white py-3">

            <strong>
                <i class="bi bi-funnel me-1"></i>
                Find Students
            </strong>

        </div>


        <div class="card-body">

            <form
                method="GET"
                action="{{
                    route(
                        'student-management.section-change'
                    )
                }}"
            >

                <div class="row g-3">

                    {{-- ACADEMIC YEAR --}}

                    <div class="col-lg-3 col-md-6">

                        <label class="form-label">
                            Academic Year
                            <span class="text-danger">*</span>
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


                    {{-- CLASS --}}

                    <div class="col-lg-3 col-md-6">

                        <label class="form-label">
                            Class
                            <span class="text-danger">*</span>
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

                    <div class="col-lg-3 col-md-6">

                        <label class="form-label">
                            Current Section
                            <span class="text-danger">*</span>
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
                            <i class="bi bi-search me-1"></i>
                            Show Students
                        </button>


                        <a
                            href="{{
                                route(
                                    'student-management.section-change'
                                )
                            }}"
                            class="btn btn-outline-secondary"
                        >
                            <i class="bi bi-arrow-counterclockwise me-1"></i>
                            Reset
                        </a>

                    </div>

                </div>

            </form>

        </div>

    </div>


    {{-- =====================================================
        STUDENT LIST
    ====================================================== --}}

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

                    <strong>

                        <i class="bi bi-people me-1"></i>
                        Students

                    </strong>


                    <span class="badge bg-primary">

                        {{ $enrollments->count() }}

                    </span>

                </div>

            </div>


            <div class="card-body p-0">

                <div class="section-change-table">

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

                                <th>
                                    Student
                                </th>

                                <th style="width:100px;">
                                    Roll No.
                                </th>

                                <th style="width:130px;">
                                    Current Section
                                </th>

                                <th style="width:230px;">
                                    New Section
                                </th>

                                <th style="width:150px;">
                                    Action
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @forelse(
                                $enrollments
                                as $enrollment
                            )

                                <tr>

                                    <td class="text-center">

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


                                    <td>

                                        {{
                                            $enrollment->roll_no
                                            ?? '-'
                                        }}

                                    </td>


                                    <td>

                                        <span class="badge bg-info text-dark">

                                            {{
                                                $enrollment
                                                    ->section
                                                    ?->name
                                                ?? '-'
                                            }}

                                        </span>

                                    </td>


                                    <td>

                                        <form
                                            id="section-change-{{
                                                $enrollment->id
                                            }}"
                                            method="POST"
                                            action="{{
                                                route(
                                                    'student-management.section-change.update'
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
                                                name="new_section_id"
                                                class="form-select form-select-sm"
                                                required
                                            >

                                                <option value="">
                                                    Select New Section
                                                </option>


                                                @foreach(
                                                    $sections
                                                    as $section
                                                )

                                                    @if(
                                                        $section->id
                                                        !=
                                                        $enrollment
                                                            ->section_id
                                                    )

                                                        <option
                                                            value="{{
                                                                $section->id
                                                            }}"
                                                        >
                                                            {{
                                                                $section->name
                                                            }}
                                                        </option>

                                                    @endif

                                                @endforeach

                                            </select>

                                        </form>

                                    </td>


                                    <td>

                                        <button
                                            type="submit"
                                            form="section-change-{{
                                                $enrollment->id
                                            }}"
                                            class="btn btn-warning btn-sm"
                                            onclick="
                                                return confirm(
                                                    'Are you sure you want to change this student section? Existing roll number will be cleared.'
                                                );
                                            "
                                        >

                                            <i class="bi bi-arrow-left-right me-1"></i>

                                            Change

                                        </button>

                                    </td>

                                </tr>


                            @empty

                                <tr>

                                    <td
                                        colspan="7"
                                        class="text-center py-5 text-muted"
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


<style>

.section-change-table {
    height: 700px;
    max-height: 700px;
    overflow: auto;
    position: relative;
}

.section-change-table thead th {
    position: sticky;
    top: 0;
    z-index: 10;
    white-space: nowrap;
}

</style>


<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

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

        const loading =
            document.getElementById(
                'sectionLoading'
            );

        const selectedSection =
            "{{ request('section_id') }}";


        function loadSections(
            keepSelected = false
        ) {

            const academicYearId =
                academicYear.value;

            const classId =
                schoolClass.value;


            section.innerHTML =
                '<option value="">Select Section</option>';


            if (
                !academicYearId ||
                !classId
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
                    academicYearId
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
                                String(
                                    selectedSection
                                )
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

                loading.classList.add(
                    'd-none'
                );

            });

        }


        academicYear.addEventListener(
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
            academicYear.value &&
            schoolClass.value
        ) {

            loadSections(true);

        }

    }
);

</script>

@endsection