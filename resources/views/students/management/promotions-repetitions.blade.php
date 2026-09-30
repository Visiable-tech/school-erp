@extends('layouts.admin')

@section('title', 'Promotions / Repetitions')

@section('content')

<div class="container-fluid">

    {{-- =========================================================
        PAGE HEADER
    ========================================================== --}}

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h4 class="mb-1">
                Promotions / Repetitions
            </h4>

            <div class="text-muted">
                Promote, repeat or update students for the next academic year
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


    <form
        method="POST"
        action="{{ route('student-promotions.store') }}"
        id="promotionForm"
    >

        @csrf


        {{-- =====================================================
            SOURCE
        ====================================================== --}}

        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white py-3">

                <strong>

                    <i class="bi bi-search me-1"></i>

                    Current Student Details

                </strong>

            </div>


            <div class="card-body">

                <div class="row g-3">

                    {{-- FROM YEAR --}}

                    <div class="col-lg-4 col-md-6">

                        <label class="form-label">

                            Current Academic Year

                            <span class="text-danger">*</span>

                        </label>

                        <select
                            name="from_academic_year_id"
                            id="from_academic_year_id"
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
                                        old('from_academic_year_id')
                                        == $year->id
                                    )
                                >

                                    {{ $year->name }}

                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- FROM CLASS --}}

                    <div class="col-lg-4 col-md-6">

                        <label class="form-label">

                            Current Class

                            <span class="text-danger">*</span>

                        </label>

                        <select
                            name="from_school_class_id"
                            id="from_school_class_id"
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
                                        old('from_school_class_id')
                                        == $class->id
                                    )
                                >

                                    {{ $class->name }}

                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- FROM SECTION --}}

                    <div class="col-lg-4 col-md-6">

                        <label class="form-label">

                            Current Section

                            <span class="text-danger">*</span>

                        </label>

                        <select
                            name="from_section_id"
                            id="from_section_id"
                            class="form-select"
                            required
                        >

                            <option value="">
                                Select Section
                            </option>

                        </select>

                        <div
                            id="sourceSectionLoading"
                            class="small text-primary mt-1 d-none"
                        >
                            <span
                                class="spinner-border spinner-border-sm"
                            ></span>

                            Loading sections...
                        </div>

                    </div>


                    <div class="col-12">

                        <button
                            type="button"
                            id="loadStudentsBtn"
                            class="btn btn-primary"
                        >

                            <i class="bi bi-people me-1"></i>

                            Load Students

                        </button>

                    </div>

                </div>

            </div>

        </div>


        {{-- =====================================================
            STUDENTS
        ====================================================== --}}

        <div
            class="card border-0 shadow-sm mb-4"
            id="studentCard"
        >

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

                            Total:

                            <strong id="totalStudents">
                                0
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

                </div>

            </div>


            <div class="card-body p-0">

                <div class="promotion-table">

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

                                <th style="min-width:240px;">
                                    Student Name
                                </th>

                                <th style="width:120px;">
                                    Gender
                                </th>

                                <th style="width:100px;">
                                    Roll No.
                                </th>

                                <th style="width:180px;">
                                    Student Type
                                </th>

                            </tr>

                        </thead>


                        <tbody id="studentTableBody">

                            <tr>

                                <td
                                    colspan="7"
                                    class="text-center text-muted py-5"
                                >

                                    Select Academic Year,
                                    Class and Section,
                                    then click
                                    <strong>Load Students</strong>.

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>


        {{-- =====================================================
            PROMOTION / REPETITION DETAILS
        ====================================================== --}}

        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white py-3">

                <strong>

                    <i class="bi bi-arrow-up-right-circle me-1"></i>

                    Promotion / Repetition Details

                </strong>

            </div>


            <div class="card-body">

                <div class="row g-3">

                    {{-- PROMOTION STATUS --}}

                    <div class="col-lg-3 col-md-6">

                        <label class="form-label">

                            Promotion Status

                            <span class="text-danger">*</span>

                        </label>

                        <select
                            name="promotion_status_id"
                            id="promotion_status_id"
                            class="form-select"
                            required
                        >

                            <option value="">
                                Select Status
                            </option>

                            @foreach(
                                $promotionStatuses as $status
                            )

                                <option
                                    value="{{ $status->id }}"
                                    data-type="{{ $status->type }}"
                                    @selected(
                                        old('promotion_status_id')
                                        == $status->id
                                    )
                                >

                                    {{ $status->name }}

                                </option>

                            @endforeach

                        </select>

                        <div
                            id="promotionStatusHelp"
                            class="small text-muted mt-1"
                        ></div>

                    </div>


                    {{-- DESTINATION YEAR --}}

                    <div class="col-lg-3 col-md-6">

                        <label class="form-label">

                            New Academic Year

                            <span class="text-danger">*</span>

                        </label>

                        <select
                            name="to_academic_year_id"
                            id="to_academic_year_id"
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
                                        old('to_academic_year_id')
                                        == $year->id
                                    )
                                >

                                    {{ $year->name }}

                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- DESTINATION CLASS --}}

                    <div class="col-lg-3 col-md-6">

                        <label class="form-label">

                            New Class

                            <span class="text-danger">*</span>

                        </label>

                        <select
                            name="to_school_class_id"
                            id="to_school_class_id"
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
                                        old('to_school_class_id')
                                        == $class->id
                                    )
                                >

                                    {{ $class->name }}

                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- DESTINATION SECTION --}}

                    <div class="col-lg-3 col-md-6">

                        <label class="form-label">

                            New Section

                            <span class="text-danger">*</span>

                        </label>

                        <select
                            name="to_section_id"
                            id="to_section_id"
                            class="form-select"
                            required
                        >

                            <option value="">
                                Select Section
                            </option>

                        </select>

                        <div
                            id="destinationSectionLoading"
                            class="small text-primary mt-1 d-none"
                        >

                            <span
                                class="spinner-border spinner-border-sm"
                            ></span>

                            Loading sections...

                        </div>

                    </div>


                    {{-- DATE --}}

                    <div class="col-lg-3 col-md-6">

                        <label class="form-label">

                            Effective Date

                            <span class="text-danger">*</span>

                        </label>

                        <input
                            type="date"
                            name="promotion_date"
                            id="promotion_date"
                            class="form-control"
                            value="{{
                                old(
                                    'promotion_date',
                                    date('Y-m-d')
                                )
                            }}"
                            required
                        >

                    </div>

                </div>

            </div>


            {{-- =================================================
                FOOTER
            ================================================== --}}

            <div class="card-footer bg-white py-3">

                <div
                    class="d-flex justify-content-between align-items-center flex-wrap gap-3"
                >

                    <div class="small text-muted">

                        <i class="bi bi-info-circle me-1"></i>

                        Roll numbers will be cleared for the new
                        academic year and can be assigned later.

                    </div>


                    <button
                        type="submit"
                        class="btn btn-success"
                        id="processButton"
                    >

                        <i class="bi bi-check-circle me-1"></i>

                        Process Selected Students

                    </button>

                </div>

            </div>

        </div>

    </form>

</div>


<style>

.promotion-table {
    height: 600px;
    max-height: 600px;
    overflow: auto;
    position: relative;
}

.promotion-table table {
    min-width: 1000px;
}

.promotion-table thead th {
    position: sticky;
    top: 0;
    z-index: 10;
    white-space: nowrap;
}

.student-checkbox,
#selectAll {
    cursor: pointer;
}

</style>


<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        /*
        |--------------------------------------------------------------------------
        | ELEMENTS
        |--------------------------------------------------------------------------
        */

        const fromYear =
            document.getElementById(
                'from_academic_year_id'
            );

        const fromClass =
            document.getElementById(
                'from_school_class_id'
            );

        const fromSection =
            document.getElementById(
                'from_section_id'
            );


        const toYear =
            document.getElementById(
                'to_academic_year_id'
            );

        const toClass =
            document.getElementById(
                'to_school_class_id'
            );

        const toSection =
            document.getElementById(
                'to_section_id'
            );


        const promotionStatus =
            document.getElementById(
                'promotion_status_id'
            );


        const promotionStatusHelp =
            document.getElementById(
                'promotionStatusHelp'
            );


        const loadStudentsBtn =
            document.getElementById(
                'loadStudentsBtn'
            );


        const studentTableBody =
            document.getElementById(
                'studentTableBody'
            );


        const totalStudents =
            document.getElementById(
                'totalStudents'
            );


        const selectedCount =
            document.getElementById(
                'selectedCount'
            );


        const selectAll =
            document.getElementById(
                'selectAll'
            );


        const promotionForm =
            document.getElementById(
                'promotionForm'
            );


        const sourceLoading =
            document.getElementById(
                'sourceSectionLoading'
            );


        const destinationLoading =
            document.getElementById(
                'destinationSectionLoading'
            );


        /*
        |--------------------------------------------------------------------------
        | ESCAPE HTML
        |--------------------------------------------------------------------------
        */

        function escapeHtml(value)
        {
            if (
                value === null ||
                value === undefined
            ) {
                return '';
            }

            return String(value)
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }


        /*
        |--------------------------------------------------------------------------
        | GENERIC SECTION LOADER
        |--------------------------------------------------------------------------
        */

        function loadSections(
            yearId,
            classId,
            sectionElement,
            loadingElement
        ) {

            sectionElement.innerHTML =
                '<option value="">Select Section</option>';


            if (
                !yearId ||
                !classId
            ) {

                return;

            }


            sectionElement.disabled =
                true;


            loadingElement.classList.remove(
                'd-none'
            );


            const url =
                "{{ route('student-promotions.get-sections') }}"
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

                    sectionElement.innerHTML =
                        '<option value="">Select Section</option>';


                    if (
                        Array.isArray(data)
                        &&
                        data.length
                    ) {

                        data.forEach(
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
                                    item.capacity
                                ) {

                                    option.textContent +=
                                        ' (Capacity: '
                                        +
                                        item.capacity
                                        +
                                        ')';

                                }


                                sectionElement.appendChild(
                                    option
                                );

                            }
                        );

                    } else {

                        sectionElement.innerHTML =
                            '<option value="">No Section Found</option>';

                    }

                }
            )

            .catch(
                error => {

                    console.error(
                        error
                    );


                    sectionElement.innerHTML =
                        '<option value="">Unable to load sections</option>';

                }
            )

            .finally(
                () => {

                    sectionElement.disabled =
                        false;


                    loadingElement.classList.add(
                        'd-none'
                    );

                }
            );

        }


        /*
        |--------------------------------------------------------------------------
        | SOURCE SECTION
        |--------------------------------------------------------------------------
        */

        function loadSourceSections()
        {

            loadSections(

                fromYear.value,

                fromClass.value,

                fromSection,

                sourceLoading

            );


            clearStudents();

        }


        fromYear.addEventListener(
            'change',
            loadSourceSections
        );


        fromClass.addEventListener(
            'change',
            loadSourceSections
        );


        fromSection.addEventListener(
            'change',
            function () {

                clearStudents();

            }
        );


        /*
        |--------------------------------------------------------------------------
        | DESTINATION SECTION
        |--------------------------------------------------------------------------
        */

        function loadDestinationSections()
        {

            loadSections(

                toYear.value,

                toClass.value,

                toSection,

                destinationLoading

            );

        }


        toYear.addEventListener(
            'change',
            loadDestinationSections
        );


        toClass.addEventListener(
            'change',
            loadDestinationSections
        );


        /*
        |--------------------------------------------------------------------------
        | PROMOTION STATUS LOGIC
        |--------------------------------------------------------------------------
        */

        promotionStatus.addEventListener(
            'change',
            function () {

                const option =
                    this.options[
                        this.selectedIndex
                    ];


                const type =
                    option.dataset.type || '';


                promotionStatusHelp.innerHTML =
                    '';


                /*
                |--------------------------------------------------------------------------
                | Repeated
                |--------------------------------------------------------------------------
                */

                if (
                    type === 'repeated'
                ) {

                    promotionStatusHelp.innerHTML =
                        '<span class="text-warning">'
                        +
                        '<i class="bi bi-info-circle me-1"></i>'
                        +
                        'Repeated students must remain in the same class.'
                        +
                        '</span>';


                    if (
                        fromClass.value
                    ) {

                        toClass.value =
                            fromClass.value;


                        loadDestinationSections();

                    }

                }


                /*
                |--------------------------------------------------------------------------
                | Promoted
                |--------------------------------------------------------------------------
                */

                else if (
                    type === 'promoted'
                ) {

                    promotionStatusHelp.innerHTML =
                        '<span class="text-success">'
                        +
                        '<i class="bi bi-arrow-up-circle me-1"></i>'
                        +
                        'Select the next class for promoted students.'
                        +
                        '</span>';

                }


                /*
                |--------------------------------------------------------------------------
                | Conditional
                |--------------------------------------------------------------------------
                */

                else if (
                    type === 'conditional'
                ) {

                    promotionStatusHelp.innerHTML =
                        '<span class="text-info">'
                        +
                        'Conditional promotion selected.'
                        +
                        '</span>';

                }


                /*
                |--------------------------------------------------------------------------
                | Detained
                |--------------------------------------------------------------------------
                */

                else if (
                    type === 'detained'
                ) {

                    promotionStatusHelp.innerHTML =
                        '<span class="text-danger">'
                        +
                        'Detained status selected.'
                        +
                        '</span>';

                }

            }
        );


        /*
        |--------------------------------------------------------------------------
        | CLEAR STUDENTS
        |--------------------------------------------------------------------------
        */

        function clearStudents()
        {

            studentTableBody.innerHTML =
                '<tr>'
                +
                '<td colspan="7" '
                +
                'class="text-center text-muted py-5">'
                +
                'Click <strong>Load Students</strong> '
                +
                'to display students.'
                +
                '</td>'
                +
                '</tr>';


            totalStudents.textContent =
                '0';


            selectedCount.textContent =
                '0';


            selectAll.checked =
                false;


            selectAll.indeterminate =
                false;

        }


        /*
        |--------------------------------------------------------------------------
        | LOAD STUDENTS
        |--------------------------------------------------------------------------
        */

        loadStudentsBtn.addEventListener(
            'click',
            function () {

                if (
                    !fromYear.value
                    ||
                    !fromClass.value
                    ||
                    !fromSection.value
                ) {

                    alert(
                        'Please select Current Academic Year, Class and Section.'
                    );

                    return;

                }


                loadStudentsBtn.disabled =
                    true;


                loadStudentsBtn.innerHTML =
                    '<span class="spinner-border spinner-border-sm me-1"></span>'
                    +
                    'Loading...';


                studentTableBody.innerHTML =
                    '<tr>'
                    +
                    '<td colspan="7" '
                    +
                    'class="text-center py-5">'
                    +
                    '<span class="spinner-border"></span>'
                    +
                    '<div class="mt-2">'
                    +
                    'Loading students...'
                    +
                    '</div>'
                    +
                    '</td>'
                    +
                    '</tr>';


                const url =
                    "{{ route('student-promotions.get-students') }}"
                    +
                    '?academic_year_id='
                    +
                    encodeURIComponent(
                        fromYear.value
                    )
                    +
                    '&school_class_id='
                    +
                    encodeURIComponent(
                        fromClass.value
                    )
                    +
                    '&section_id='
                    +
                    encodeURIComponent(
                        fromSection.value
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

                .then(
                    response => {

                        if (!response.ok) {

                            throw new Error(
                                'Unable to load students.'
                            );

                        }

                        return response.json();

                    }
                )

                .then(
                    students => {

                        studentTableBody.innerHTML =
                            '';


                        totalStudents.textContent =
                            students.length;


                        selectedCount.textContent =
                            '0';


                        selectAll.checked =
                            false;


                        selectAll.indeterminate =
                            false;


                        if (
                            !students.length
                        ) {

                            studentTableBody.innerHTML =
                                '<tr>'
                                +
                                '<td colspan="7" '
                                +
                                'class="text-center text-muted py-5">'
                                +
                                'No eligible students found.'
                                +
                                '</td>'
                                +
                                '</tr>';


                            return;

                        }


                        students.forEach(
                            function (
                                student,
                                index
                            ) {

                                const tr =
                                    document.createElement(
                                        'tr'
                                    );


                                let typeBadge;


                                if (
                                    student.student_type
                                ) {

                                    typeBadge =
                                        '<span class="badge bg-info text-dark">'
                                        +
                                        escapeHtml(
                                            student.student_type
                                        )
                                        +
                                        '</span>';

                                } else {

                                    typeBadge =
                                        '<span class="badge bg-secondary">'
                                        +
                                        'Not Assigned'
                                        +
                                        '</span>';

                                }


                                tr.innerHTML =
                                    '<td class="text-center">'
                                    +
                                    '<input '
                                    +
                                    'type="checkbox" '
                                    +
                                    'name="enrollment_ids[]" '
                                    +
                                    'value="'
                                    +
                                    student.enrollment_id
                                    +
                                    '" '
                                    +
                                    'class="form-check-input student-checkbox">'
                                    +
                                    '</td>'

                                    +

                                    '<td class="text-center">'
                                    +
                                    (index + 1)
                                    +
                                    '</td>'

                                    +

                                    '<td>'
                                    +
                                    '<strong class="text-primary">'
                                    +
                                    escapeHtml(
                                        student.admission_no
                                    )
                                    +
                                    '</strong>'
                                    +
                                    '</td>'

                                    +

                                    '<td>'
                                    +
                                    '<strong>'
                                    +
                                    escapeHtml(
                                        student.student_name
                                    )
                                    +
                                    '</strong>'
                                    +
                                    (
                                        student.father_name
                                        ?
                                        '<div class="small text-muted">'
                                        +
                                        'Father: '
                                        +
                                        escapeHtml(
                                            student.father_name
                                        )
                                        +
                                        '</div>'
                                        :
                                        ''
                                    )
                                    +
                                    '</td>'

                                    +

                                    '<td>'
                                    +
                                    escapeHtml(
                                        student.gender
                                        ?
                                        student.gender
                                        :
                                        '-'
                                    )
                                    +
                                    '</td>'

                                    +

                                    '<td class="text-center">'
                                    +
                                    escapeHtml(
                                        student.roll_no
                                        ?
                                        student.roll_no
                                        :
                                        '-'
                                    )
                                    +
                                    '</td>'

                                    +

                                    '<td>'
                                    +
                                    typeBadge
                                    +
                                    '</td>';


                                studentTableBody.appendChild(
                                    tr
                                );

                            }
                        );


                        bindStudentCheckboxes();

                    }
                )

                .catch(
                    error => {

                        console.error(
                            error
                        );


                        studentTableBody.innerHTML =
                            '<tr>'
                            +
                            '<td colspan="7" '
                            +
                            'class="text-center text-danger py-5">'
                            +
                            'Unable to load students.'
                            +
                            '</td>'
                            +
                            '</tr>';

                    }
                )

                .finally(
                    () => {

                        loadStudentsBtn.disabled =
                            false;


                        loadStudentsBtn.innerHTML =
                            '<i class="bi bi-people me-1"></i>'
                            +
                            'Load Students';

                    }
                );

            }
        );


        /*
        |--------------------------------------------------------------------------
        | SELECT ALL
        |--------------------------------------------------------------------------
        */

        selectAll.addEventListener(
            'change',
            function () {

                const checkboxes =
                    document.querySelectorAll(
                        '.student-checkbox'
                    );


                checkboxes.forEach(
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


        /*
        |--------------------------------------------------------------------------
        | BIND CHECKBOXES
        |--------------------------------------------------------------------------
        */

        function bindStudentCheckboxes()
        {

            const checkboxes =
                document.querySelectorAll(
                    '.student-checkbox'
                );


            checkboxes.forEach(
                function (
                    checkbox
                ) {

                    checkbox.addEventListener(
                        'change',
                        updateSelectedCount
                    );

                }
            );

        }


        /*
        |--------------------------------------------------------------------------
        | SELECTED COUNT
        |--------------------------------------------------------------------------
        */

        function updateSelectedCount()
        {

            const all =
                document.querySelectorAll(
                    '.student-checkbox'
                );


            const checked =
                document.querySelectorAll(
                    '.student-checkbox:checked'
                );


            selectedCount.textContent =
                checked.length;


            selectAll.checked =
                all.length > 0
                &&
                checked.length
                ===
                all.length;


            selectAll.indeterminate =
                checked.length > 0
                &&
                checked.length
                <
                all.length;

        }


        /*
        |--------------------------------------------------------------------------
        | FORM VALIDATION
        |--------------------------------------------------------------------------
        */

        promotionForm.addEventListener(
            'submit',
            function (
                event
            ) {

                const selected =
                    document.querySelectorAll(
                        '.student-checkbox:checked'
                    );


                if (
                    selected.length === 0
                ) {

                    event.preventDefault();


                    alert(
                        'Please select at least one student.'
                    );


                    return;

                }


                if (
                    !promotionStatus.value
                ) {

                    event.preventDefault();


                    alert(
                        'Please select Promotion Status.'
                    );


                    return;

                }


                if (
                    !toYear.value
                    ||
                    !toClass.value
                    ||
                    !toSection.value
                ) {

                    event.preventDefault();


                    alert(
                        'Please select destination Academic Year, Class and Section.'
                    );


                    return;

                }


                const selectedOption =
                    promotionStatus.options[
                        promotionStatus.selectedIndex
                    ];


                const statusName =
                    selectedOption.text;


                const confirmation =
                    confirm(
                        'Process '
                        +
                        selected.length
                        +
                        ' selected student(s) as "'
                        +
                        statusName
                        +
                        '"?\n\n'
                        +
                        'This will close their current enrollment '
                        +
                        'and create a new enrollment.'
                    );


                if (
                    !confirmation
                ) {

                    event.preventDefault();

                }

            }
        );

    }
);

</script>

@endsection