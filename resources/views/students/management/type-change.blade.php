@extends('layouts.admin')

@section('title', 'Type Change')

@section('content')

<div class="container-fluid">

    {{-- =========================================================
        PAGE HEADER
    ========================================================== --}}

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h4 class="mb-1">
                Type Change
            </h4>

            <div class="text-muted">
                Change student type for one or multiple students
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
        SUCCESS MESSAGE
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
        VALIDATION ERRORS
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
        FILTER CARD
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
                action="{{ route('student-management.type-change') }}"
            >

                <div class="row g-3">

                    {{-- =================================================
                        ACADEMIC YEAR
                    ================================================== --}}

                    <div class="col-xl-3 col-lg-3 col-md-6">

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


                    {{-- =================================================
                        CLASS
                    ================================================== --}}

                    <div class="col-xl-3 col-lg-3 col-md-6">

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


                    {{-- =================================================
                        SECTION
                    ================================================== --}}

                    <div class="col-xl-3 col-lg-3 col-md-6">

                        <label class="form-label">

                            Section

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


                    {{-- =================================================
                        SEARCH
                    ================================================== --}}

                    <div class="col-xl-3 col-lg-3 col-md-6">

                        <label class="form-label">
                            Search Student
                        </label>

                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            class="form-control"
                            placeholder="Name / Admission No / Mobile"
                        >

                    </div>


                    {{-- =================================================
                        FILTER BUTTONS
                    ================================================== --}}

                    <div class="col-12">

                        <button
                            type="submit"
                            class="btn btn-primary"
                        >

                            <i class="bi bi-search me-1"></i>

                            Show Students

                        </button>


                        <a
                            href="{{ route('student-management.type-change') }}"
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
        STUDENT RESULTS
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
            action="{{ route('student-management.type-change.update') }}"
            id="bulkTypeForm"
        >

            @csrf


            {{-- FILTER VALUES --}}

            <input
                type="hidden"
                name="academic_year_id"
                value="{{ request('academic_year_id') }}"
            >

            <input
                type="hidden"
                name="school_class_id"
                value="{{ request('school_class_id') }}"
            >

            <input
                type="hidden"
                name="section_id"
                value="{{ request('section_id') }}"
            >


            <div class="card border-0 shadow-sm">

                {{-- =====================================================
                    RESULT HEADER
                ====================================================== --}}

                <div class="card-header bg-white py-3">

                    <div
                        class="d-flex
                               justify-content-between
                               align-items-center
                               flex-wrap
                               gap-3"
                    >

                        {{-- STUDENT COUNT --}}

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


                        {{-- =================================================
                            NEW STUDENT TYPE
                        ================================================== --}}

                        @if($enrollments->count())

                            <div
                                class="d-flex
                                       align-items-center
                                       flex-wrap
                                       gap-2"
                            >

                                <label
                                    for="student_type_id"
                                    class="fw-semibold mb-0 text-nowrap"
                                >
                                    Change Type To:
                                </label>


                                <select
                                    name="student_type_id"
                                    id="student_type_id"
                                    class="form-select"
                                    style="min-width:220px;"
                                    required
                                >

                                    <option value="">
                                        Select Student Type
                                    </option>


                                    @foreach(
                                        $studentTypes as $type
                                    )

                                        <option
                                            value="{{ $type->id }}"
                                            @selected(
                                                old('student_type_id')
                                                == $type->id
                                            )
                                        >

                                            {{ $type->name }}

                                        </option>

                                    @endforeach

                                </select>

                            </div>

                        @endif

                    </div>

                </div>


                {{-- =====================================================
                    TABLE
                ====================================================== --}}

                <div class="card-body p-0">

                    <div class="type-change-table">

                        <table
                            class="table table-bordered table-striped table-hover align-middle mb-0"
                        >

                            <thead class="table-dark">

                                <tr>

                                    {{-- SELECT ALL --}}

                                    <th
                                        class="text-center"
                                        style="width:60px;"
                                    >

                                        <input
                                            type="checkbox"
                                            id="selectAll"
                                            class="form-check-input"
                                            title="Select All Students"
                                        >

                                    </th>


                                    <th
                                        class="text-center"
                                        style="width:60px;"
                                    >
                                        #
                                    </th>


                                    <th style="width:160px;">
                                        Admission No.
                                    </th>


                                    <th style="min-width:250px;">
                                        Student Name
                                    </th>


                                    <th style="width:120px;">
                                        Gender
                                    </th>


                                    <th style="width:100px;">
                                        Roll No.
                                    </th>


                                    <th style="width:150px;">
                                        Class
                                    </th>


                                    <th style="width:130px;">
                                        Section
                                    </th>


                                    <th style="width:180px;">
                                        Current Type
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                @forelse(
                                    $enrollments as $enrollment
                                )

                                    <tr>

                                        {{-- =================================
                                            SELECT
                                        ================================== --}}

                                        <td class="text-center">

                                            <input
                                                type="checkbox"
                                                name="enrollment_ids[]"
                                                value="{{ $enrollment->id }}"
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


                                        {{-- =================================
                                            SERIAL
                                        ================================== --}}

                                        <td class="text-center">

                                            {{ $loop->iteration }}

                                        </td>


                                        {{-- =================================
                                            ADMISSION NUMBER
                                        ================================== --}}

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


                                        {{-- =================================
                                            STUDENT
                                        ================================== --}}

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


                                            @if(
                                                $enrollment
                                                    ->student
                                                    ?->father_mobile
                                            )

                                                <div
                                                    class="small text-muted"
                                                >

                                                    <i
                                                        class="bi bi-telephone me-1"
                                                    ></i>

                                                    {{
                                                        $enrollment
                                                            ->student
                                                            ->father_mobile
                                                    }}

                                                </div>

                                            @endif

                                        </td>


                                        {{-- =================================
                                            GENDER
                                        ================================== --}}

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


                                        {{-- =================================
                                            ROLL NUMBER
                                        ================================== --}}

                                        <td class="text-center">

                                            @if(
                                                $enrollment->roll_no
                                            )

                                                <span
                                                    class="badge bg-secondary"
                                                >

                                                    {{
                                                        $enrollment
                                                            ->roll_no
                                                    }}

                                                </span>

                                            @else

                                                <span
                                                    class="text-muted"
                                                >
                                                    -
                                                </span>

                                            @endif

                                        </td>


                                        {{-- =================================
                                            CLASS
                                        ================================== --}}

                                        <td>

                                            {{
                                                $enrollment
                                                    ->schoolClass
                                                    ?->name
                                                ?? '-'
                                            }}

                                        </td>


                                        {{-- =================================
                                            SECTION
                                        ================================== --}}

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


                                        {{-- =================================
                                            CURRENT STUDENT TYPE
                                        ================================== --}}

                                        <td>

                                            @if(
                                                $enrollment
                                                    ->studentType
                                            )

                                                <span
                                                    class="badge bg-success"
                                                >

                                                    {{
                                                        $enrollment
                                                            ->studentType
                                                            ->name
                                                    }}

                                                </span>

                                            @else

                                                <span
                                                    class="badge bg-secondary"
                                                >

                                                    Not Assigned

                                                </span>

                                            @endif

                                        </td>

                                    </tr>


                                @empty

                                    <tr>

                                        <td
                                            colspan="9"
                                            class="text-center py-5 text-muted"
                                        >

                                            <i
                                                class="bi bi-people"
                                                style="font-size:2rem;"
                                            ></i>


                                            <div class="mt-2">

                                                No students found.

                                            </div>


                                            <div class="small mt-1">

                                                Try changing the
                                                Academic Year,
                                                Class or Section.

                                            </div>

                                        </td>

                                    </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>


                {{-- =====================================================
                    BULK ACTION FOOTER
                ====================================================== --}}

                @if($enrollments->count())

                    <div class="card-footer bg-white py-3">

                        <div
                            class="d-flex
                                   justify-content-between
                                   align-items-center
                                   flex-wrap
                                   gap-3"
                        >

                            <div class="small text-muted">

                                <i
                                    class="bi bi-info-circle me-1"
                                ></i>

                                Select one or more students,
                                choose the new student type,
                                then click Change Selected Students.

                            </div>


                            <button
                                type="submit"
                                class="btn btn-warning"
                            >

                                <i
                                    class="bi bi-arrow-repeat me-1"
                                ></i>

                                Change Selected Students

                            </button>

                        </div>

                    </div>

                @endif

            </div>

        </form>

    @endif

</div>


{{-- =============================================================
    CSS
============================================================= --}}

<style>

.type-change-table {
    height: 700px;
    max-height: 700px;
    overflow: auto;
    position: relative;
}

.type-change-table table {
    min-width: 1250px;
}

.type-change-table thead th {
    position: sticky;
    top: 0;
    z-index: 10;
    white-space: nowrap;
}

.type-change-table tbody tr:hover {
    background-color: rgba(0, 0, 0, 0.025);
}

.student-checkbox,
#selectAll {
    cursor: pointer;
}

</style>


{{-- =============================================================
    JAVASCRIPT
============================================================= --}}

<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        /*
        |--------------------------------------------------------------------------
        | FILTER SECTION AJAX
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


        /*
        |--------------------------------------------------------------------------
        | Load Sections
        |--------------------------------------------------------------------------
        */

        function loadSections(
            keepSelected = false
        ) {

            const yearId =
                academicYear.value;

            const classId =
                schoolClass.value;


            section.innerHTML =
                '<option value="">Select Section</option>';


            /*
            |--------------------------------------------------------------------------
            | Academic Year + Class Required
            |--------------------------------------------------------------------------
            */

            if (
                !yearId ||
                !classId
            ) {

                section.disabled =
                    false;

                return;

            }


            /*
            |--------------------------------------------------------------------------
            | Loading State
            |--------------------------------------------------------------------------
            */

            section.disabled =
                true;

            loading.classList.remove(
                'd-none'
            );


            /*
            |--------------------------------------------------------------------------
            | AJAX URL
            |--------------------------------------------------------------------------
            */

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


            /*
            |--------------------------------------------------------------------------
            | Fetch
            |--------------------------------------------------------------------------
            */

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
                response => {

                    if (!response.ok) {

                        throw new Error(
                            'Unable to load sections.'
                        );

                    }

                    return response.json();

                }
            )

            .then(
                data => {

                    section.innerHTML =
                        '<option value="">Select Section</option>';


                    /*
                    |--------------------------------------------------------------------------
                    | Sections Found
                    |--------------------------------------------------------------------------
                    */

                    if (
                        data.success &&
                        data.sections &&
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


                                /*
                                |--------------------------------------------------------------------------
                                | Keep Existing Selected Section
                                |--------------------------------------------------------------------------
                                */

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

                    }

                    /*
                    |--------------------------------------------------------------------------
                    | No Section
                    |--------------------------------------------------------------------------
                    */

                    else {

                        section.innerHTML =
                            '<option value="">No Section Found</option>';

                    }

                }
            )

            .catch(
                error => {

                    console.error(
                        error
                    );


                    section.innerHTML =
                        '<option value="">Unable to load sections</option>';

                }
            )

            .finally(
                () => {

                    section.disabled =
                        false;


                    loading.classList.add(
                        'd-none'
                    );

                }
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Academic Year Change
        |--------------------------------------------------------------------------
        */

        academicYear.addEventListener(
            'change',
            function () {

                loadSections(
                    false
                );

            }
        );


        /*
        |--------------------------------------------------------------------------
        | Class Change
        |--------------------------------------------------------------------------
        */

        schoolClass.addEventListener(
            'change',
            function () {

                loadSections(
                    false
                );

            }
        );


        /*
        |--------------------------------------------------------------------------
        | Initial Section Load
        |--------------------------------------------------------------------------
        */

        if (
            academicYear.value &&
            schoolClass.value
        ) {

            loadSections(
                true
            );

        }


        /*
        |--------------------------------------------------------------------------
        | BULK STUDENT SELECTION
        |--------------------------------------------------------------------------
        */

        const selectAll =
            document.getElementById(
                'selectAll'
            );


        const studentCheckboxes =
            document.querySelectorAll(
                '.student-checkbox'
            );


        const selectedCount =
            document.getElementById(
                'selectedCount'
            );


        /*
        |--------------------------------------------------------------------------
        | Update Selected Count
        |--------------------------------------------------------------------------
        */

        function updateSelectedCount()
        {

            const checked =
                document.querySelectorAll(
                    '.student-checkbox:checked'
                ).length;


            if (
                selectedCount
            ) {

                selectedCount.textContent =
                    checked;

            }


            /*
            |--------------------------------------------------------------------------
            | Select All State
            |--------------------------------------------------------------------------
            */

            if (
                selectAll
            ) {

                selectAll.checked =
                    studentCheckboxes.length > 0
                    &&
                    checked ===
                    studentCheckboxes.length;


                selectAll.indeterminate =
                    checked > 0
                    &&
                    checked <
                    studentCheckboxes.length;

            }

        }


        /*
        |--------------------------------------------------------------------------
        | Select All
        |--------------------------------------------------------------------------
        */

        if (
            selectAll
        ) {

            selectAll.addEventListener(
                'change',
                function () {

                    studentCheckboxes.forEach(
                        function (
                            checkbox
                        ) {

                            checkbox.checked =
                                selectAll.checked;

                        }
                    );


                    updateSelectedCount();

                }
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Individual Checkbox
        |--------------------------------------------------------------------------
        */

        studentCheckboxes.forEach(
            function (
                checkbox
            ) {

                checkbox.addEventListener(
                    'change',
                    function () {

                        updateSelectedCount();

                    }
                );

            }
        );


        /*
        |--------------------------------------------------------------------------
        | Initial Count
        |--------------------------------------------------------------------------
        */

        updateSelectedCount();


        /*
        |--------------------------------------------------------------------------
        | BULK TYPE CHANGE FORM
        |--------------------------------------------------------------------------
        */

        const bulkTypeForm =
            document.getElementById(
                'bulkTypeForm'
            );


        if (
            bulkTypeForm
        ) {

            bulkTypeForm.addEventListener(
                'submit',
                function (
                    event
                ) {

                    /*
                    |--------------------------------------------------------------------------
                    | Selected Students
                    |--------------------------------------------------------------------------
                    */

                    const selectedStudents =
                        document.querySelectorAll(
                            '.student-checkbox:checked'
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | Student Type
                    |--------------------------------------------------------------------------
                    */

                    const typeSelect =
                        document.getElementById(
                            'student_type_id'
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | No Student Selected
                    |--------------------------------------------------------------------------
                    */

                    if (
                        selectedStudents.length
                        ===
                        0
                    ) {

                        event.preventDefault();


                        alert(
                            'Please select at least one student.'
                        );


                        return;

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | No Type Selected
                    |--------------------------------------------------------------------------
                    */

                    if (
                        !typeSelect ||
                        !typeSelect.value
                    ) {

                        event.preventDefault();


                        alert(
                            'Please select the new student type.'
                        );


                        return;

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Selected Type Name
                    |--------------------------------------------------------------------------
                    */

                    const typeName =
                        typeSelect.options[
                            typeSelect.selectedIndex
                        ].text;


                    /*
                    |--------------------------------------------------------------------------
                    | Confirmation
                    |--------------------------------------------------------------------------
                    */

                    const confirmed =
                        confirm(
                            'Are you sure you want to change '
                            +
                            selectedStudents.length
                            +
                            ' selected student(s) to "'
                            +
                            typeName
                            +
                            '"?'
                        );


                    if (
                        !confirmed
                    ) {

                        event.preventDefault();

                    }

                }
            );

        }

    }
);

</script>

@endsection