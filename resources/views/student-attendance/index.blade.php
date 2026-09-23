@extends('layouts.admin')

@section('title', 'Attendance Register')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h4 class="mb-1">Attendance Register</h4>

        <div class="text-muted">
            View daily student attendance and attendance summary.
        </div>
    </div>


    @can('student-attendance.create')

        <a
            href="{{ route('student-attendance.create') }}"
            class="btn btn-primary"
        >
            <i class="bi bi-calendar-check me-1"></i>
            Mark Attendance
        </a>

    @endcan

</div>


{{-- ========================================================= --}}
{{-- FILTER --}}
{{-- ========================================================= --}}

<div class="card border-0 shadow-sm mb-4">

    <div class="card-header bg-white py-3">

        <strong>
            <i class="bi bi-funnel me-1"></i>
            Search Attendance
        </strong>

    </div>


    <div class="card-body">

        <form
            method="GET"
            action="{{ route('student-attendance.index') }}"
            id="attendanceFilter"
        >

            <div class="row g-3">


                {{-- ACADEMIC YEAR --}}

                <div class="col-md-3">

                    <label class="form-label">
                        Academic Year
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
                    </label>

                    <input
                        type="date"
                        name="attendance_date"
                        id="attendance_date"
                        value="{{ request('attendance_date', date('Y-m-d')) }}"
                        class="form-control"
                        required
                    >

                </div>


                {{-- CLASS --}}

                <div class="col-md-3">

                    <label class="form-label">
                        Class
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

                <div class="col-md-3">

                    <label class="form-label">
                        Section
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

                    </select>

                </div>


                <div class="col-12">

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        <i class="bi bi-search me-1"></i>
                        View Attendance
                    </button>


                    <a
                        href="{{ route('student-attendance.index') }}"
                        class="btn btn-outline-secondary"
                    >
                        Reset
                    </a>

                </div>

            </div>

        </form>

    </div>

</div>


{{-- ========================================================= --}}
{{-- RESULT --}}
{{-- ========================================================= --}}

@if(
    request()->filled('academic_year_id') &&
    request()->filled('school_class_id') &&
    request()->filled('section_id') &&
    request()->filled('attendance_date')
)

    @if($attendanceSession)


        {{-- ========================================================= --}}
        {{-- SESSION INFORMATION --}}
        {{-- ========================================================= --}}

        <div class="card border-0 shadow-sm mb-4">

            <div class="card-body">

                <div class="row">


                    <div class="col-md-3">

                        <small class="text-muted">
                            Academic Year
                        </small>

                        <div class="fw-semibold">
                            {{ $attendanceSession->academicYear?->name }}
                        </div>

                    </div>


                    <div class="col-md-3">

                        <small class="text-muted">
                            Class / Section
                        </small>

                        <div class="fw-semibold">

                            {{ $attendanceSession->schoolClass?->name }}

                            /

                            {{ $attendanceSession->section?->name }}

                        </div>

                    </div>


                    <div class="col-md-3">

                        <small class="text-muted">
                            Attendance Date
                        </small>

                        <div class="fw-semibold">

                            {{ $attendanceSession->attendance_date->format('d M Y') }}

                        </div>

                    </div>


                    <div class="col-md-3">

                        <small class="text-muted">
                            Marked By
                        </small>

                        <div class="fw-semibold">

                            {{ $attendanceSession->marker?->name ?? '-' }}

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- SUMMARY --}}
        {{-- ========================================================= --}}

        <div class="row g-3 mb-4">


            <div class="col-md-2">

                <div class="card border-0 shadow-sm h-100">

                    <div class="card-body">

                        <small class="text-muted">
                            Total Students
                        </small>

                        <h3 class="mb-0 mt-2">
                            {{ $summary['total'] }}
                        </h3>

                    </div>

                </div>

            </div>


            <div class="col-md-2">

                <div class="card border-0 shadow-sm h-100">

                    <div class="card-body">

                        <small class="text-muted">
                            Present
                        </small>

                        <h3 class="mb-0 mt-2 text-success">
                            {{ $summary['present'] }}
                        </h3>

                    </div>

                </div>

            </div>


            <div class="col-md-2">

                <div class="card border-0 shadow-sm h-100">

                    <div class="card-body">

                        <small class="text-muted">
                            Absent
                        </small>

                        <h3 class="mb-0 mt-2 text-danger">
                            {{ $summary['absent'] }}
                        </h3>

                    </div>

                </div>

            </div>


            <div class="col-md-2">

                <div class="card border-0 shadow-sm h-100">

                    <div class="card-body">

                        <small class="text-muted">
                            Late
                        </small>

                        <h3 class="mb-0 mt-2 text-warning">
                            {{ $summary['late'] }}
                        </h3>

                    </div>

                </div>

            </div>


            <div class="col-md-2">

                <div class="card border-0 shadow-sm h-100">

                    <div class="card-body">

                        <small class="text-muted">
                            Leave
                        </small>

                        <h3 class="mb-0 mt-2 text-info">
                            {{ $summary['leave'] }}
                        </h3>

                    </div>

                </div>

            </div>


            <div class="col-md-2">

                <div class="card border-0 shadow-sm h-100">

                    <div class="card-body">

                        <small class="text-muted">
                            Half Day
                        </small>

                        <h3 class="mb-0 mt-2">
                            {{ $summary['half_day'] }}
                        </h3>

                    </div>

                </div>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- STUDENT LIST --}}
        {{-- ========================================================= --}}

        <div class="card border-0 shadow-sm">

            <div class="card-header bg-white py-3">

                <div class="d-flex justify-content-between align-items-center">

                    <strong>
                        Attendance Details
                    </strong>


                    @can('student-attendance.edit')

                        <a
                            href="{{ route('student-attendance.create', [
                                'academic_year_id' => $attendanceSession->academic_year_id,
                                'attendance_date' => $attendanceSession->attendance_date->format('Y-m-d'),
                                'school_class_id' => $attendanceSession->school_class_id,
                                'section_id' => $attendanceSession->section_id
                            ]) }}"
                            class="btn btn-sm btn-outline-primary"
                        >
                            <i class="bi bi-pencil-square me-1"></i>
                            Edit Attendance
                        </a>

                    @endcan

                </div>

            </div>


            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">

                        <tr>

                            <th width="80">
                                Roll
                            </th>

                            <th width="160">
                                Admission No.
                            </th>

                            <th>
                                Student Name
                            </th>

                            <th width="160">
                                Status
                            </th>

                            <th>
                                Remarks
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($attendances as $attendance)

                            <tr>

                                <td>
                                    {{ $attendance->enrollment?->roll_no ?? '-' }}
                                </td>


                                <td>

                                    <a
                                        href="{{ route(
                                            'students.show',
                                            $attendance->student_id
                                        ) }}"
                                        class="text-decoration-none"
                                    >
                                        {{ $attendance->student?->admission_no }}
                                    </a>

                                </td>


                                <td class="fw-semibold">
                                    {{ $attendance->student?->student_name }}
                                </td>


                                <td>

                                    @switch($attendance->attendance_status)

                                        @case('present')

                                            <span class="badge bg-success">
                                                Present
                                            </span>

                                            @break


                                        @case('absent')

                                            <span class="badge bg-danger">
                                                Absent
                                            </span>

                                            @break


                                        @case('late')

                                            <span class="badge bg-warning text-dark">
                                                Late
                                            </span>

                                            @break


                                        @case('leave')

                                            <span class="badge bg-info text-dark">
                                                Leave
                                            </span>

                                            @break


                                        @case('half_day')

                                            <span class="badge bg-secondary">
                                                Half Day
                                            </span>

                                            @break

                                    @endswitch

                                </td>


                                <td>
                                    {{ $attendance->remarks ?: '-' }}
                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="5"
                                    class="text-center text-muted py-4"
                                >
                                    No attendance records found.
                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


    @else

        <div class="alert alert-warning">

            <i class="bi bi-exclamation-triangle me-1"></i>

            Attendance has not been marked for the selected
            date, class and section.

            @can('student-attendance.create')

                <a
                    href="{{ route('student-attendance.create') }}"
                    class="alert-link ms-1"
                >
                    Mark Attendance
                </a>

            @endcan

        </div>

    @endif

@endif


<script>

document.addEventListener('DOMContentLoaded', function () {

    const year =
        document.getElementById('academic_year_id');

    const classSelect =
        document.getElementById('school_class_id');

    const section =
        document.getElementById('section_id');


    const selectedSection =
        "{{ request('section_id') }}";


    /*
    |--------------------------------------------------------------------------
    | Load Sections
    |--------------------------------------------------------------------------
    */

    async function loadSections(selectPrevious = false) {

        section.innerHTML =
            '<option value="">Select Section</option>';

        section.disabled = true;


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
                throw new Error();
            }


            const data =
                await response.json();


            section.innerHTML =
                '<option value="">Select Section</option>';


            data.forEach(function (item) {

                const option =
                    document.createElement('option');

                option.value =
                    item.id;

                option.textContent =
                    item.name;


                if (
                    selectPrevious &&
                    String(item.id) === String(selectedSection)
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

            }

        }
        catch (error) {

            section.innerHTML =
                '<option value="">Unable to load sections</option>';

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
    | Restore section after search
    |--------------------------------------------------------------------------
    */

    if (
        year.value &&
        classSelect.value
    ) {

        loadSections(true);

    }

});

</script>

@endsection