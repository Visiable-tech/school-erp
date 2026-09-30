@extends('layouts.admin')

@section('title', 'Assign Roll No.')

@section('content')

<div class="container-fluid">

    {{-- =========================================================
        PAGE HEADER
    ========================================================== --}}

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h4 class="mb-1">
                Assign Roll No.
            </h4>

            <div class="text-muted">
                Assign or update student roll numbers
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
        ERROR MESSAGE
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
                Select Class & Section
            </strong>

        </div>


        <div class="card-body">

            <form
                method="GET"
                action="{{
                    route(
                        'student-management.assign-roll-no'
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
                                        request('academic_year_id') == $year->id
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
                                        request('school_class_id') == $class->id
                                    )
                                >
                                    {{ $class->name }}
                                </option>

                            @endforeach
                        </select>

                    </div>


                    {{-- SECTION --}}

                    <div class="col-lg-3 col-md-6">

                        <label class="form-label">
                            Section
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
                                        request('section_id') == $section->id
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
                            <span class="spinner-border spinner-border-sm"></span>
                            Loading sections...
                        </div>

                    </div>


                    {{-- SEARCH --}}

                    <div class="col-lg-3 col-md-6">

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


                    {{-- BUTTONS --}}

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
                                    'student-management.assign-roll-no'
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


    {{-- =========================================================
        RESULT
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
                    class="d-flex justify-content-between align-items-center flex-wrap gap-2"
                >

                    <div>

                        <strong>
                            <i class="bi bi-list-ol me-1"></i>
                            Student Roll Numbers
                        </strong>

                        <div class="small text-muted mt-1">

                            Total Students:

                            <strong>
                                {{ $enrollments->count() }}
                            </strong>

                        </div>

                    </div>


                    @if($enrollments->count())

                        <button
                            type="button"
                            class="btn btn-outline-primary btn-sm"
                            id="autoRollButton"
                        >
                            <i class="bi bi-sort-numeric-down me-1"></i>
                            Auto Generate Roll No.
                        </button>

                    @endif

                </div>

            </div>


            @if($enrollments->count())

                <form
                    method="POST"
                    action="{{
                        route(
                            'student-management.assign-roll-no.update'
                        )
                    }}"
                    id="rollNumberForm"
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

                    <input
                        type="hidden"
                        name="search"
                        value="{{ request('search') }}"
                    >


                    <div class="card-body p-0">

                        {{-- =============================================
                            FIXED HEIGHT TABLE
                        ============================================== --}}

                        <div class="roll-table-wrapper">

                            <table
                                class="table table-bordered table-striped table-hover align-middle mb-0"
                            >

                                <thead class="table-dark">

                                    <tr>

                                        <th
                                            class="text-center"
                                            style="width:70px;"
                                        >
                                            #
                                        </th>

                                        <th style="width:170px;">
                                            Admission No.
                                        </th>

                                        <th>
                                            Student Name
                                        </th>

                                        <th style="width:130px;">
                                            Gender
                                        </th>

                                        <th style="width:160px;">
                                            Class
                                        </th>

                                        <th style="width:130px;">
                                            Section
                                        </th>

                                        <th style="width:180px;">
                                            Roll No.
                                        </th>

                                    </tr>

                                </thead>


                                <tbody>

                                    @foreach(
                                        $enrollments
                                        as $enrollment
                                    )

                                        <tr>

                                            {{-- SERIAL --}}

                                            <td class="text-center">

                                                {{ $loop->iteration }}

                                            </td>


                                            {{-- ADMISSION NO --}}

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


                                            {{-- CLASS --}}

                                            <td>

                                                {{
                                                    $enrollment
                                                        ->schoolClass
                                                        ?->name
                                                    ?? '-'
                                                }}

                                            </td>


                                            {{-- SECTION --}}

                                            <td>

                                                {{
                                                    $enrollment
                                                        ->section
                                                        ?->name
                                                    ?? '-'
                                                }}

                                            </td>


                                            {{-- ROLL NUMBER --}}

                                            <td>

                                                <input
                                                    type="number"
                                                    name="roll_no[{{
                                                        $enrollment->id
                                                    }}]"
                                                    value="{{
                                                        old(
                                                            'roll_no.'
                                                            . $enrollment->id,
                                                            $enrollment->roll_no
                                                        )
                                                    }}"
                                                    min="1"
                                                    step="1"
                                                    class="form-control roll-number-input"
                                                    placeholder="Roll No."
                                                >

                                            </td>

                                        </tr>

                                    @endforeach

                                </tbody>

                            </table>

                        </div>

                    </div>


                    {{-- =============================================
                        SAVE AREA
                    ============================================== --}}

                    <div class="card-footer bg-white">

                        <div
                            class="d-flex justify-content-between align-items-center flex-wrap gap-2"
                        >

                            <div class="small text-muted">

                                <i class="bi bi-info-circle me-1"></i>

                                Roll numbers must be unique within
                                the selected class and section.

                            </div>


                            <button
                                type="submit"
                                class="btn btn-success"
                            >

                                <i class="bi bi-floppy me-1"></i>

                                Update Roll Numbers

                            </button>

                        </div>

                    </div>

                </form>


            @else

                <div class="card-body text-center py-5">

                    <i
                        class="bi bi-people text-muted"
                        style="font-size:3rem;"
                    ></i>

                    <h5 class="mt-3">
                        No Students Found
                    </h5>

                    <p class="text-muted mb-0">

                        No active students were found in the
                        selected academic year, class and section.

                    </p>

                </div>

            @endif

        </div>

    @else

        {{-- =====================================================
            INITIAL MESSAGE
        ====================================================== --}}

        <div class="card border-0 shadow-sm">

            <div class="card-body text-center py-5">

                <i
                    class="bi bi-list-ol text-primary"
                    style="font-size:3rem;"
                ></i>

                <h5 class="mt-3">
                    Assign Student Roll Numbers
                </h5>

                <p class="text-muted mb-0">

                    Select Academic Year, Class and Section above
                    to display students.

                </p>

            </div>

        </div>

    @endif

</div>


{{-- =========================================================
    CSS
========================================================== --}}

<style>

    /*
    |--------------------------------------------------------------------------
    | Fixed 700px Table Height
    |--------------------------------------------------------------------------
    */

    .roll-table-wrapper {
        height: 700px;
        max-height: 700px;
        overflow: auto;
        position: relative;
    }


    /*
    |--------------------------------------------------------------------------
    | Sticky Header
    |--------------------------------------------------------------------------
    */

    .roll-table-wrapper thead th {
        position: sticky;
        top: 0;
        z-index: 10;
        white-space: nowrap;
        vertical-align: middle;
    }


    /*
    |--------------------------------------------------------------------------
    | Roll Input
    |--------------------------------------------------------------------------
    */

    .roll-number-input {
        min-width: 100px;
    }

</style>


{{-- =========================================================
    JAVASCRIPT
========================================================== --}}

<script>

document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | Section AJAX
    |--------------------------------------------------------------------------
    */

    const academicYearSelect =
        document.getElementById('academic_year_id');

    const classSelect =
        document.getElementById('school_class_id');

    const sectionSelect =
        document.getElementById('section_id');

    const sectionLoading =
        document.getElementById('sectionLoading');

    const selectedSectionId =
        "{{ request('section_id') }}";


    function loadSections(keepSelected = false)
    {
        const academicYearId =
            academicYearSelect.value;

        const schoolClassId =
            classSelect.value;


        sectionSelect.innerHTML =
            '<option value="">Select Section</option>';


        if (!academicYearId || !schoolClassId) {
            return;
        }


        sectionSelect.disabled = true;

        sectionLoading.classList.remove('d-none');


        const url =
            "{{ route('student-management.get-sections') }}"
            + '?academic_year_id='
            + encodeURIComponent(academicYearId)
            + '&school_class_id='
            + encodeURIComponent(schoolClassId);


        fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
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

            sectionSelect.innerHTML =
                '<option value="">Select Section</option>';


            if (
                data.success &&
                data.sections.length > 0
            ) {

                data.sections.forEach(function (section) {

                    const option =
                        document.createElement('option');

                    option.value =
                        section.id;

                    option.textContent =
                        section.name;


                    if (
                        keepSelected &&
                        String(section.id) ===
                        String(selectedSectionId)
                    ) {
                        option.selected = true;
                    }


                    sectionSelect.appendChild(option);

                });

            } else {

                sectionSelect.innerHTML =
                    '<option value="">No Section Found</option>';

            }

        })
        .catch(error => {

            console.error(error);

            sectionSelect.innerHTML =
                '<option value="">Unable to load sections</option>';

        })
        .finally(() => {

            sectionSelect.disabled = false;

            sectionLoading.classList.add('d-none');

        });
    }


    /*
    |--------------------------------------------------------------------------
    | Academic Year Changed
    |--------------------------------------------------------------------------
    */

    academicYearSelect.addEventListener(
        'change',
        function () {

            loadSections(false);

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Class Changed
    |--------------------------------------------------------------------------
    */

    classSelect.addEventListener(
        'change',
        function () {

            loadSections(false);

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Load Existing Selection
    |--------------------------------------------------------------------------
    */

    if (
        academicYearSelect.value &&
        classSelect.value
    ) {

        loadSections(true);

    }


    /*
    |--------------------------------------------------------------------------
    | Auto Generate Roll Number
    |--------------------------------------------------------------------------
    */

    const autoRollButton =
        document.getElementById('autoRollButton');


    if (autoRollButton) {

        autoRollButton.addEventListener(
            'click',
            function () {

                const inputs =
                    document.querySelectorAll(
                        '.roll-number-input'
                    );


                if (!inputs.length) {
                    return;
                }


                if (
                    !confirm(
                        'This will assign roll numbers from 1 to '
                        + inputs.length
                        + '. Continue?'
                    )
                ) {
                    return;
                }


                inputs.forEach(
                    function (input, index) {

                        input.value =
                            index + 1;

                    }
                );

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Duplicate Roll Number Check
    |--------------------------------------------------------------------------
    */

    const rollForm =
        document.getElementById('rollNumberForm');


    if (rollForm) {

        rollForm.addEventListener(
            'submit',
            function (event) {

                const inputs =
                    document.querySelectorAll(
                        '.roll-number-input'
                    );

                const usedRollNumbers =
                    new Set();

                let duplicateRoll =
                    null;


                inputs.forEach(function (input) {

                    input.classList.remove(
                        'is-invalid'
                    );

                    const value =
                        input.value.trim();


                    if (value === '') {
                        return;
                    }


                    if (
                        usedRollNumbers.has(value)
                    ) {

                        duplicateRoll =
                            value;

                        input.classList.add(
                            'is-invalid'
                        );

                    } else {

                        usedRollNumbers.add(
                            value
                        );

                    }

                });


                if (duplicateRoll !== null) {

                    event.preventDefault();

                    alert(
                        'Duplicate Roll No. '
                        + duplicateRoll
                        + ' found.'
                    );

                }

            }
        );

    }

});

</script>

@endsection