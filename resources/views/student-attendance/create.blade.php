@extends('layouts.admin')

@section('title', 'Student Attendance')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h4 class="mb-1">Student Attendance</h4>

        <div class="text-muted">
            Select class and section to mark daily attendance.
        </div>
    </div>

</div>


<div class="card border-0 shadow-sm mb-4">

    <div class="card-header bg-white py-3">
        <strong>
            <i class="bi bi-calendar-check me-1"></i>
            Attendance Details
        </strong>
    </div>


    <div class="card-body">

        <div class="row g-3">


            {{-- ACADEMIC YEAR --}}

            <div class="col-md-3">

                <label class="form-label">
                    Academic Year
                    <span class="text-danger">*</span>
                </label>

                <select
                    id="academic_year_id"
                    class="form-select"
                >

                    <option value="">
                        Select Academic Year
                    </option>

                    @foreach($academicYears as $year)

                        <option
                            value="{{ $year->id }}"
                            @selected(
                                request('academic_year_id')
                                    ? request('academic_year_id') == $year->id
                                    : (
                                        $currentAcademicYear &&
                                        $currentAcademicYear->id == $year->id
                                    )
                            )
                        >

                            {{ $year->name }}

                            @if($year->is_current)
                                (Current)
                            @endif

                        </option>

                    @endforeach

                </select>

            </div>


            {{-- DATE --}}

            <div class="col-md-3">

                <label class="form-label">
                    Attendance Date
                    <span class="text-danger">*</span>
                </label>

                <input
                    type="date"
                    id="attendance_date"
                    class="form-control"
                    value="{{ request('attendance_date', date('Y-m-d')) }}"
                >

            </div>


            {{-- CLASS --}}

            <div class="col-md-3">

                <label class="form-label">
                    Class
                    <span class="text-danger">*</span>
                </label>

                <select
                    id="school_class_id"
                    class="form-select"
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


            {{-- SECTION --}}

            <div class="col-md-3">

                <label class="form-label">
                    Section
                    <span class="text-danger">*</span>
                </label>

                <select
                    id="section_id"
                    class="form-select"
                    disabled
                >

                    <option value="">
                        Select Section
                    </option>

                </select>

            </div>


            <div class="col-12">

                <button
                    type="button"
                    id="loadStudents"
                    class="btn btn-primary"
                >
                    <i class="bi bi-people me-1"></i>
                    Load Students
                </button>

            </div>

        </div>

    </div>

</div>



{{-- ATTENDANCE FORM --}}

<div
    id="attendanceCard"
    class="card border-0 shadow-sm d-none"
>

    <div class="card-header bg-white py-3">

        <div class="d-flex justify-content-between align-items-center">

            <strong>
                Student Attendance
            </strong>


            <div>

                <button
                    type="button"
                    id="markAllPresent"
                    class="btn btn-sm btn-success"
                >
                    Mark All Present
                </button>

                <button
                    type="button"
                    id="markAllAbsent"
                    class="btn btn-sm btn-outline-danger"
                >
                    Mark All Absent
                </button>

            </div>

        </div>

    </div>


    <div class="card-body">

        <div
            id="existingAlert"
            class="alert alert-warning d-none"
        >
            Attendance has already been marked for this
            class and date. Saving will update it.
        </div>


        <form
            method="POST"
            action="{{ route('student-attendance.store') }}"
            id="attendanceForm"
        >

            @csrf


            <input
                type="hidden"
                name="academic_year_id"
                id="form_academic_year_id"
            >

            <input
                type="hidden"
                name="school_class_id"
                id="form_school_class_id"
            >

            <input
                type="hidden"
                name="section_id"
                id="form_section_id"
            >

            <input
                type="hidden"
                name="attendance_date"
                id="form_attendance_date"
            >


            <div class="table-responsive">

                <table class="table table-bordered align-middle">

                    <thead class="table-light">

                        <tr>

                            <th width="80">
                                Roll
                            </th>

                            <th width="160">
                                Admission No.
                            </th>

                            <th>
                                Student
                            </th>

                            <th width="180">
                                Attendance
                            </th>

                            <th width="250">
                                Remarks
                            </th>

                        </tr>

                    </thead>


                    <tbody id="studentRows"></tbody>

                </table>

            </div>


            <div class="row mt-3">

                <div class="col-md-6">

                    <label class="form-label">
                        General Remarks
                    </label>

                    <textarea
                        name="session_remarks"
                        id="session_remarks"
                        class="form-control"
                        rows="2"
                    ></textarea>

                </div>

            </div>


            <div class="mt-4">

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    <i class="bi bi-check-circle me-1"></i>
                    Save Attendance
                </button>

            </div>

        </form>

    </div>

</div>


<script>

document.addEventListener('DOMContentLoaded', function () {

    const year =
        document.getElementById('academic_year_id');

    const classSelect =
        document.getElementById('school_class_id');

    const section =
        document.getElementById('section_id');

    const attendanceDate =
        document.getElementById('attendance_date');

    const loadButton =
        document.getElementById('loadStudents');

    const attendanceCard =
        document.getElementById('attendanceCard');

    const studentRows =
        document.getElementById('studentRows');

    const existingAlert =
        document.getElementById('existingAlert');

    const requestedSection =
    "{{ request('section_id') }}";

    const shouldAutoLoad =
        "{{ request()->filled('academic_year_id')
            && request()->filled('school_class_id')
            && request()->filled('section_id')
            && request()->filled('attendance_date')
                ? '1'
                : '0' }}";    


    /*
    |--------------------------------------------------------------------------
    | Load Sections
    |--------------------------------------------------------------------------
    */

    async function loadSections(autoLoadStudents = false) {

        section.innerHTML =
            '<option value="">Select Section</option>';

        section.disabled = true;

        attendanceCard.classList.add('d-none');


        if (!year.value || !classSelect.value) {
            return;
        }


        section.innerHTML =
            '<option value="">Loading...</option>';


        try {

            const url =
                "{{ route('student-attendance.sections') }}"
                + "?academic_year_id="
                + encodeURIComponent(year.value)
                + "&school_class_id="
                + encodeURIComponent(classSelect.value);


            const response =
                await fetch(url, {

                    headers: {
                        'Accept': 'application/json'
                    }

                });


            if (!response.ok) {

                if (response.status === 401) {
                    window.location.href = "{{ route('login') }}";
                    return;
                }

                throw new Error();
            }


            const data =
                await response.json();

            document.getElementById(
                'session_remarks'
            ).value = data.session_remarks || '';    

            section.innerHTML =
                '<option value="">Select Section</option>';


            data.forEach(function (item) {

                const option =
                    document.createElement('option');

                option.value =
                    item.id;

                option.textContent =
                    item.name;


                /*
                |--------------------------------------------------------------------------
                | Restore Section
                |--------------------------------------------------------------------------
                */

                if (
                    requestedSection &&
                    String(item.id) === String(requestedSection)
                ) {

                    option.selected = true;

                }


                section.appendChild(option);

            });


            section.disabled = false;


            if (data.length === 0) {

                section.innerHTML =
                    '<option value="">No Sections Found</option>';

                section.disabled = true;

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | Automatically load existing attendance
            |--------------------------------------------------------------------------
            */

            if (
                autoLoadStudents &&
                requestedSection &&
                section.value
            ) {

                loadButton.click();

            }

        }
        catch (error) {

            section.innerHTML =
                '<option value="">Unable to load sections</option>';

            section.disabled = true;

        }

    }


    year.addEventListener(
        'change',
        function () {

            loadSections(false);

        }
    );


    classSelect.addEventListener(
        'change',
        function () {

            loadSections(false);

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Load Students
    |--------------------------------------------------------------------------
    */

    loadButton.addEventListener(
        'click',
        async function () {

            if (
                !year.value ||
                !attendanceDate.value ||
                !classSelect.value ||
                !section.value
            ) {

                alert(
                    'Please select Academic Year, Date, Class and Section.'
                );

                return;
            }


            loadButton.disabled = true;

            loadButton.innerHTML =
                'Loading...';


            try {

                const url =
                    "{{ route('student-attendance.students') }}"
                    + "?academic_year_id="
                    + encodeURIComponent(year.value)
                    + "&school_class_id="
                    + encodeURIComponent(classSelect.value)
                    + "&section_id="
                    + encodeURIComponent(section.value)
                    + "&attendance_date="
                    + encodeURIComponent(attendanceDate.value);


                const response =
                    await fetch(url, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });


                if (!response.ok) {
                    throw new Error();
                }


                const data =
                    await response.json();


                studentRows.innerHTML = '';


                if (data.students.length === 0) {

                    studentRows.innerHTML = `
                        <tr>
                            <td
                                colspan="5"
                                class="text-center text-muted py-4"
                            >
                                No students found.
                            </td>
                        </tr>
                    `;

                    attendanceCard.classList.remove(
                        'd-none'
                    );

                    return;
                }


                data.students.forEach(
                    function (student, index) {

                        const tr =
                            document.createElement('tr');


                        tr.innerHTML = `

                            <td>
                                ${student.roll_no ?? '-'}
                            </td>

                            <td>
                                ${student.admission_no}
                            </td>

                            <td>
                                ${student.student_name}

                                <input
                                    type="hidden"
                                    name="students[${index}][student_id]"
                                    value="${student.student_id}"
                                >

                                <input
                                    type="hidden"
                                    name="students[${index}][student_enrollment_id]"
                                    value="${student.student_enrollment_id}"
                                >

                            </td>

                            <td>

                                <select
                                    name="students[${index}][attendance_status]"
                                    class="form-select attendance-status"
                                    required
                                >

                                    <option
                                        value="present"
                                        ${student.attendance_status === 'present' ? 'selected' : ''}
                                    >
                                        Present
                                    </option>

                                    <option
                                        value="absent"
                                        ${student.attendance_status === 'absent' ? 'selected' : ''}
                                    >
                                        Absent
                                    </option>

                                    <option
                                        value="late"
                                        ${student.attendance_status === 'late' ? 'selected' : ''}
                                    >
                                        Late
                                    </option>

                                    <option
                                        value="leave"
                                        ${student.attendance_status === 'leave' ? 'selected' : ''}
                                    >
                                        Leave
                                    </option>

                                    <option
                                        value="half_day"
                                        ${student.attendance_status === 'half_day' ? 'selected' : ''}
                                    >
                                        Half Day
                                    </option>

                                </select>

                            </td>

                            <td>

                                <input
                                    type="text"
                                    name="students[${index}][remarks]"
                                    class="form-control"
                                    value="${escapeHtml(student.remarks ?? '')}"
                                    placeholder="Optional"
                                >

                            </td>

                        `;


                        studentRows.appendChild(tr);

                    }
                );


                /*
                |--------------------------------------------------------------------------
                | Set hidden fields
                |--------------------------------------------------------------------------
                */

                document.getElementById(
                    'form_academic_year_id'
                ).value = year.value;


                document.getElementById(
                    'form_school_class_id'
                ).value = classSelect.value;


                document.getElementById(
                    'form_section_id'
                ).value = section.value;


                document.getElementById(
                    'form_attendance_date'
                ).value = attendanceDate.value;


                /*
                |--------------------------------------------------------------------------
                | Existing Attendance
                |--------------------------------------------------------------------------
                */

                if (data.already_marked) {

                    existingAlert.classList.remove(
                        'd-none'
                    );

                }
                else {

                    existingAlert.classList.add(
                        'd-none'
                    );

                }


                attendanceCard.classList.remove(
                    'd-none'
                );

            }
            catch (error) {

                alert(
                    'Unable to load students. Please try again.'
                );

            }
            finally {

                loadButton.disabled = false;

                loadButton.innerHTML =
                    '<i class="bi bi-people me-1"></i> Load Students';

            }

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Mark All Present
    |--------------------------------------------------------------------------
    */

    document.getElementById(
        'markAllPresent'
    ).addEventListener(
        'click',
        function () {

            document.querySelectorAll(
                '.attendance-status'
            ).forEach(function (select) {

                select.value = 'present';

            });

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Mark All Absent
    |--------------------------------------------------------------------------
    */

    document.getElementById(
        'markAllAbsent'
    ).addEventListener(
        'click',
        function () {

            document.querySelectorAll(
                '.attendance-status'
            ).forEach(function (select) {

                select.value = 'absent';

            });

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Escape HTML
    |--------------------------------------------------------------------------
    */

    function escapeHtml(value) {

        const div =
            document.createElement('div');

        div.textContent = value;

        return div.innerHTML;
    }

        /*
    |--------------------------------------------------------------------------
    | Restore Edit Attendance Selection
    |--------------------------------------------------------------------------
    */

    if (
        year.value &&
        classSelect.value
    ) {

        loadSections(
            shouldAutoLoad === '1'
        );

    }

});

</script>

@endsection