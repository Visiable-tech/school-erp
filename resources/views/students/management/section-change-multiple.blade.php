@extends('layouts.admin')

@section('title', 'Section Change (Multiple)')

@section('content')

<div class="container-fluid">

    {{-- =========================================================
        HEADER
    ========================================================== --}}

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h4 class="mb-1">
                Section Change (Multiple)
            </h4>

            <div class="text-muted">
                Move multiple students from one section to another
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

                Find Students

            </strong>

        </div>


        <div class="card-body">

            <form
                method="GET"
                action="{{
                    route(
                        'student-management.section-change-multiple'
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

                            @foreach(
                                $academicYears
                                as $year
                            )

                                <option
                                    value="{{ $year->id }}"
                                    @selected(
                                        request(
                                            'academic_year_id'
                                        )
                                        ==
                                        $year->id
                                    )
                                >

                                    {{ $year->name }}

                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- CLASS --}}

                    <div class="col-xl-3 col-md-6">

                        <label class="form-label">

                            Class

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

                            @foreach(
                                $classes
                                as $class
                            )

                                <option
                                    value="{{ $class->id }}"
                                    @selected(
                                        request(
                                            'school_class_id'
                                        )
                                        ==
                                        $class->id
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

                            @foreach(
                                $sections
                                as $section
                            )

                                <option
                                    value="{{ $section->id }}"
                                    @selected(
                                        request('section_id')
                                        ==
                                        $section->id
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
                                    'student-management.section-change-multiple'
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

        <form
            method="POST"
            action="{{
                route(
                    'student-management.section-change-multiple.update'
                )
            }}"
            id="multipleSectionForm"
        >

            @csrf


            <input
                type="hidden"
                name="academic_year_id"
                value="{{
                    request('academic_year_id')
                }}"
            >


            <input
                type="hidden"
                name="school_class_id"
                value="{{
                    request('school_class_id')
                }}"
            >


            <input
                type="hidden"
                name="current_section_id"
                value="{{
                    request('section_id')
                }}"
            >


            <div class="card border-0 shadow-sm">

                {{-- HEADER --}}

                <div class="card-header bg-white py-3">

                    <div
                        class="d-flex justify-content-between align-items-center flex-wrap gap-3"
                    >

                        <div>

                            <strong>

                                <i class="bi bi-people me-1"></i>

                                Students

                            </strong>


                            <div class="small text-muted mt-1">

                                Total:

                                <strong>
                                    {{ $enrollments->count() }}
                                </strong>

                                &nbsp; | &nbsp;

                                Selected:

                                <strong
                                    id="selectedCount"
                                    class="text-primary"
                                >
                                    0
                                </strong>

                            </div>

                        </div>


                        {{-- DESTINATION SECTION --}}

                        @if($enrollments->count())

                            <div
                                class="d-flex align-items-center gap-2"
                            >

                                <label
                                    for="new_section_id"
                                    class="fw-semibold mb-0 text-nowrap"
                                >
                                    Move To:
                                </label>


                                <select
                                    name="new_section_id"
                                    id="new_section_id"
                                    class="form-select"
                                    style="min-width:180px;"
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
                                            request('section_id')
                                        )

                                            <option
                                                value="{{
                                                    $section->id
                                                }}"
                                                @selected(
                                                    old(
                                                        'new_section_id'
                                                    )
                                                    ==
                                                    $section->id
                                                )
                                            >

                                                {{ $section->name }}

                                            </option>

                                        @endif

                                    @endforeach

                                </select>

                            </div>

                        @endif

                    </div>

                </div>


                {{-- TABLE --}}

                <div class="card-body p-0">

                    <div class="multiple-section-table">

                        <table
                            class="table table-bordered table-striped table-hover align-middle mb-0"
                        >

                            <thead class="table-dark">

                                <tr>

                                    <th
                                        class="text-center"
                                        style="width:60px;"
                                    >

                                        <input
                                            type="checkbox"
                                            id="selectAll"
                                            class="form-check-input"
                                            title="Select All"
                                        >

                                    </th>


                                    <th
                                        class="text-center"
                                        style="width:70px;"
                                    >
                                        #
                                    </th>


                                    <th style="width:160px;">
                                        Admission No.
                                    </th>


                                    <th>
                                        Student Name
                                    </th>


                                    <th style="width:120px;">
                                        Gender
                                    </th>


                                    <th style="width:120px;">
                                        Roll No.
                                    </th>


                                    <th style="width:150px;">
                                        Current Section
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                @forelse(
                                    $enrollments
                                    as $enrollment
                                )

                                    <tr>

                                        {{-- CHECKBOX --}}

                                        <td class="text-center">

                                            <input
                                                type="checkbox"
                                                name="enrollment_ids[]"
                                                value="{{
                                                    $enrollment->id
                                                }}"
                                                class="form-check-input student-checkbox"
                                                @checked(
                                                    in_array(
                                                        $enrollment->id,
                                                        old(
                                                            'enrollment_ids',
                                                            []
                                                        )
                                                    )
                                                )
                                            >

                                        </td>


                                        {{-- SERIAL --}}

                                        <td class="text-center">

                                            {{ $loop->iteration }}

                                        </td>


                                        {{-- ADMISSION --}}

                                        <td>

                                            <strong
                                                class="text-primary"
                                            >

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

                                                <div
                                                    class="small text-muted"
                                                >

                                                    Father:

                                                    {{
                                                        $enrollment
                                                            ->student
                                                            ->father_name
                                                    }}

                                                </div>

                                            @endif

                                        </td>


                                        {{-- GENDER --}}

                                        <td>

                                            {{
                                                ucfirst(
                                                    $enrollment
                                                        ->student
                                                        ?->gender
                                                    ?? '-'
                                                )
                                            }}

                                        </td>


                                        {{-- ROLL --}}

                                        <td>

                                            {{
                                                $enrollment
                                                    ->roll_no
                                                ?? '-'
                                            }}

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


                {{-- FOOTER --}}

                @if($enrollments->count())

                    <div class="card-footer bg-white">

                        <div
                            class="d-flex justify-content-between align-items-center flex-wrap gap-2"
                        >

                            <div class="text-muted small">

                                <i
                                    class="bi bi-info-circle me-1"
                                ></i>

                                Roll numbers will be cleared after
                                moving students to another section.

                            </div>


                            <button
                                type="submit"
                                class="btn btn-warning"
                            >

                                <i
                                    class="bi bi-arrow-left-right me-1"
                                ></i>

                                Move Selected Students

                            </button>

                        </div>

                    </div>

                @endif

            </div>

        </form>

    @endif

</div>


{{-- =========================================================
    CSS
========================================================== --}}

<style>

.multiple-section-table {
    height: 700px;
    max-height: 700px;
    overflow: auto;
    position: relative;
}

.multiple-section-table thead th {
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
        | SECTION AJAX
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

        const loading =
            document.getElementById(
                'sectionLoading'
            );

        const selectedSection =
            "{{ request('section_id') }}";


        function loadSections(
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


        /*
        |--------------------------------------------------------------------------
        | SELECT ALL
        |--------------------------------------------------------------------------
        */

        const selectAll =
            document.getElementById(
                'selectAll'
            );

        const checkboxes =
            document.querySelectorAll(
                '.student-checkbox'
            );

        const selectedCount =
            document.getElementById(
                'selectedCount'
            );


        function updateSelectedCount()
        {
            if (!selectedCount) {
                return;
            }

            const count =
                document.querySelectorAll(
                    '.student-checkbox:checked'
                ).length;

            selectedCount.textContent =
                count;
        }


        if (selectAll) {

            selectAll.addEventListener(
                'change',
                function () {

                    checkboxes.forEach(
                        function (checkbox) {

                            checkbox.checked =
                                selectAll.checked;

                        }
                    );

                    updateSelectedCount();

                }
            );

        }


        checkboxes.forEach(
            function (checkbox) {

                checkbox.addEventListener(
                    'change',
                    function () {

                        updateSelectedCount();


                        if (!selectAll) {
                            return;
                        }


                        const checked =
                            document.querySelectorAll(
                                '.student-checkbox:checked'
                            ).length;


                        selectAll.checked =
                            checked ===
                            checkboxes.length;


                        selectAll.indeterminate =
                            checked > 0 &&
                            checked <
                            checkboxes.length;

                    }
                );

            }
        );


        updateSelectedCount();


        /*
        |--------------------------------------------------------------------------
        | FORM VALIDATION
        |--------------------------------------------------------------------------
        */

        const form =
            document.getElementById(
                'multipleSectionForm'
            );


        if (form) {

            form.addEventListener(
                'submit',
                function (event) {

                    const selected =
                        document.querySelectorAll(
                            '.student-checkbox:checked'
                        );


                    const destination =
                        document.getElementById(
                            'new_section_id'
                        );


                    if (!selected.length) {

                        event.preventDefault();

                        alert(
                            'Please select at least one student.'
                        );

                        return;

                    }


                    if (
                        !destination ||
                        !destination.value
                    ) {

                        event.preventDefault();

                        alert(
                            'Please select the destination section.'
                        );

                        return;

                    }


                    const confirmed =
                        confirm(
                            'Move '
                            + selected.length
                            + ' selected student(s) to the new section? Their existing roll numbers will be cleared.'
                        );


                    if (!confirmed) {

                        event.preventDefault();

                    }

                }
            );

        }

    }
);

</script>

@endsection