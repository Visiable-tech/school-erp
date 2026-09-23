@extends('layouts.admin')

@section('title', 'Student Promotion')

@section('content')

<div class="mb-4">
    <h4 class="mb-1">Student Promotion</h4>

    <div class="text-muted">
        Promote students to the next academic year.
    </div>
</div>


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
            <strong>From</strong>
        </div>

        <div class="card-body">

            <div class="row g-3">

                <div class="col-md-4">

                    <label class="form-label">
                        Academic Year
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
                                    old(
                                        'from_academic_year_id'
                                    ) == $year->id
                                )
                            >
                                {{ $year->name }}
                            </option>

                        @endforeach

                    </select>

                </div>


                <div class="col-md-4">

                    <label class="form-label">
                        Class
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
                            >
                                {{ $class->name }}
                            </option>

                        @endforeach

                    </select>

                </div>


                <div class="col-md-4">

                    <label class="form-label">
                        Section
                    </label>

                    <select
                        name="from_section_id"
                        id="from_section_id"
                        class="form-select"
                        required
                    >

                        <option value="">
                            Select Year and Class
                        </option>

                    </select>

                </div>

            </div>


            <div class="mt-3">

                <button
                    type="button"
                    class="btn btn-outline-primary"
                    id="loadStudents"
                >
                    <i class="bi bi-search"></i>
                    Load Students
                </button>

            </div>

        </div>

    </div>



    {{-- =====================================================
         STUDENTS
    ====================================================== --}}

    <div
        class="card border-0 shadow-sm mb-4"
        id="studentCard"
        style="display:none;"
    >

        <div class="card-header bg-white py-3">

            <div class="d-flex justify-content-between">

                <strong>
                    Students
                </strong>

                <div>

                    <input
                        type="checkbox"
                        id="selectAll"
                        class="form-check-input"
                    >

                    <label
                        for="selectAll"
                        class="ms-1"
                    >
                        Select All
                    </label>

                </div>

            </div>

        </div>


        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover mb-0">

                    <thead class="table-light">

                        <tr>
                            <th width="60"></th>
                            <th>Roll</th>
                            <th>Admission No.</th>
                            <th>Student</th>
                        </tr>

                    </thead>

                    <tbody id="studentTableBody"></tbody>

                </table>

            </div>

        </div>

    </div>



    {{-- =====================================================
         DESTINATION
    ====================================================== --}}

    <div
        class="card border-0 shadow-sm mb-4"
        id="destinationCard"
        style="display:none;"
    >

        <div class="card-header bg-white py-3">
            <strong>Promote To</strong>
        </div>


        <div class="card-body">

            <div class="row g-3">


                <div class="col-md-3">

                    <label class="form-label">
                        Academic Year
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

                            <option value="{{ $year->id }}">
                                {{ $year->name }}
                            </option>

                        @endforeach

                    </select>

                </div>


                <div class="col-md-3">

                    <label class="form-label">
                        Class
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

                            <option value="{{ $class->id }}">
                                {{ $class->name }}
                            </option>

                        @endforeach

                    </select>

                </div>


                <div class="col-md-3">

                    <label class="form-label">
                        Section
                    </label>

                    <select
                        name="to_section_id"
                        id="to_section_id"
                        class="form-select"
                        required
                    >

                        <option value="">
                            Select Year and Class
                        </option>

                    </select>

                </div>


                <div class="col-md-3">

                    <label class="form-label">
                        Promotion Date
                    </label>

                    <input
                        type="date"
                        name="promotion_date"
                        class="form-control"
                        value="{{ old(
                            'promotion_date',
                            now()->format('Y-m-d')
                        ) }}"
                        required
                    >

                </div>

            </div>

        </div>

    </div>


    <div
        id="promotionButton"
        style="display:none;"
    >

        <button
            type="submit"
            class="btn btn-success"
            onclick="return confirm(
                'Promote selected students?'
            )"
        >
            <i class="bi bi-arrow-up-circle"></i>
            Promote Selected Students
        </button>

    </div>

</form>


<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const sectionUrl =
            "{{ route('student-promotions.sections') }}";

        const studentUrl =
            "{{ route('student-promotions.students') }}";


        /*
        |--------------------------------------------------------------------------
        | Elements
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


        /*
        |--------------------------------------------------------------------------
        | Section Loader
        |--------------------------------------------------------------------------
        */

        function loadSections(
            yearElement,
            classElement,
            sectionElement
        ) {

            sectionElement.innerHTML =
                '<option value="">Select Section</option>';


            if (
                !yearElement.value
                ||
                !classElement.value
            ) {
                return;
            }


            const url =
                sectionUrl
                + '?academic_year_id='
                + encodeURIComponent(
                    yearElement.value
                )
                + '&school_class_id='
                + encodeURIComponent(
                    classElement.value
                );


            fetch(url, {
                headers: {
                    'Accept':
                        'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {

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

                    sectionElement.appendChild(
                        option
                    );

                });

            });

        }


        fromYear.addEventListener(
            'change',
            () => loadSections(
                fromYear,
                fromClass,
                fromSection
            )
        );


        fromClass.addEventListener(
            'change',
            () => loadSections(
                fromYear,
                fromClass,
                fromSection
            )
        );


        toYear.addEventListener(
            'change',
            () => loadSections(
                toYear,
                toClass,
                toSection
            )
        );


        toClass.addEventListener(
            'change',
            () => loadSections(
                toYear,
                toClass,
                toSection
            )
        );


        /*
        |--------------------------------------------------------------------------
        | Load Students
        |--------------------------------------------------------------------------
        */

        document
            .getElementById('loadStudents')
            .addEventListener(
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
                            'Please select academic year, class and section.'
                        );

                        return;

                    }


                    const url =
                        studentUrl
                        + '?academic_year_id='
                        + encodeURIComponent(
                            fromYear.value
                        )
                        + '&school_class_id='
                        + encodeURIComponent(
                            fromClass.value
                        )
                        + '&section_id='
                        + encodeURIComponent(
                            fromSection.value
                        );


                    fetch(url, {
                        headers: {
                            'Accept':
                                'application/json'
                        }
                    })
                    .then(
                        response =>
                            response.json()
                    )
                    .then(data => {

                        const tbody =
                            document.getElementById(
                                'studentTableBody'
                            );


                        tbody.innerHTML = '';


                        if (!data.length) {

                            tbody.innerHTML =
                                '<tr>'
                                + '<td colspan="4" '
                                + 'class="text-center py-4 text-muted">'
                                + 'No active students found.'
                                + '</td>'
                                + '</tr>';

                            document
                                .getElementById(
                                    'studentCard'
                                )
                                .style.display =
                                    'block';

                            document
                                .getElementById(
                                    'destinationCard'
                                )
                                .style.display =
                                    'none';

                            document
                                .getElementById(
                                    'promotionButton'
                                )
                                .style.display =
                                    'none';

                            return;

                        }


                        data.forEach(item => {

                            const row =
                                document.createElement(
                                    'tr'
                                );


                            row.innerHTML = `
                                <td>
                                    <input
                                        type="checkbox"
                                        class="form-check-input student-checkbox"
                                        name="enrollment_ids[]"
                                        value="${item.enrollment_id}"
                                    >
                                </td>

                                <td>
                                    ${item.roll_no ?? '-'}
                                </td>

                                <td>
                                    ${item.admission_no}
                                </td>

                                <td>
                                    ${item.student_name}
                                </td>
                            `;


                            tbody.appendChild(
                                row
                            );

                        });


                        document
                            .getElementById(
                                'studentCard'
                            )
                            .style.display =
                                'block';


                        document
                            .getElementById(
                                'destinationCard'
                            )
                            .style.display =
                                'block';


                        document
                            .getElementById(
                                'promotionButton'
                            )
                            .style.display =
                                'block';

                    });

                }
            );


        /*
        |--------------------------------------------------------------------------
        | Select All
        |--------------------------------------------------------------------------
        */

        document
            .getElementById('selectAll')
            .addEventListener(
                'change',
                function () {

                    document
                        .querySelectorAll(
                            '.student-checkbox'
                        )
                        .forEach(
                            checkbox => {

                                checkbox.checked =
                                    this.checked;

                            }
                        );

                }
            );

    }
);

</script>

@endsection