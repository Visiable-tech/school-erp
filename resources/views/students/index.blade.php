@extends('layouts.admin')

@section('title', 'Student List')

@section('content')

<div class="container-fluid">

    {{-- =========================================================
        PAGE HEADER
    ========================================================== --}}

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h4 class="mb-1">
                Student List
            </h4>

            <div class="text-muted">
                Student Information System
            </div>

        </div>


        <div class="d-flex gap-2">

            @can('student.create')

                <a
                    href="{{ route('students.import') }}"
                    class="btn btn-success"
                >
                    <i class="bi bi-file-earmark-excel me-1"></i>
                    Import
                </a>


                <a
                    href="{{ route('students.create') }}"
                    class="btn btn-primary"
                >
                    <i class="bi bi-person-plus me-1"></i>
                    Add Student
                </a>

            @endcan

        </div>

    </div>


    {{-- =========================================================
        SUCCESS MESSAGE
    ========================================================== --}}

    @if(session('success'))

        <div class="alert alert-success alert-dismissible fade show">

            {{ session('success') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>

        </div>

    @endif


    {{-- =========================================================
        FILTER AREA
    ========================================================== --}}

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white py-3">

            <strong>
                <i class="bi bi-funnel me-1"></i>
                Search Students
            </strong>

        </div>


        <div class="card-body">

            <form
                method="GET"
                action="{{ route('students.index') }}"
            >

                <div class="row g-3">


                    {{-- STATUS --}}

                    <div class="col-xl-2 col-lg-3 col-md-4">

                        <label class="form-label">
                            Status
                        </label>

                        <select
                            name="status"
                            class="form-select"
                        >

                            <option value="">
                                All
                            </option>

                            <option
                                value="1"
                                @selected(request('status') === '1')
                            >
                                Active
                            </option>

                            <option
                                value="0"
                                @selected(request('status') === '0')
                            >
                                Inactive
                            </option>

                        </select>

                    </div>


                    {{-- ACADEMIC YEAR --}}

                    <div class="col-xl-2 col-lg-3 col-md-4">

                        <label class="form-label">
                            Academic Year
                        </label>

                        <select
                            name="academic_year_id"
                            id="academic_year_id"
                            class="form-select"
                        >

                            <option value="">
                                All
                            </option>

                            @foreach($academicYears as $academicYear)

                                <option
                                    value="{{ $academicYear->id }}"
                                    @selected(
                                        request('academic_year_id')
                                        == $academicYear->id
                                    )
                                >
                                    {{ $academicYear->name }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- CLASS --}}

                    <div class="col-xl-2 col-lg-3 col-md-4">

                        <label class="form-label">
                            Class
                        </label>

                        <select
                            name="school_class_id"
                            id="school_class_id"
                            class="form-select"
                        >

                            <option value="">
                                All
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


                    {{-- SECTION --}}

                    <div class="col-xl-2 col-lg-3 col-md-4">

                        <label class="form-label">
                            Class-Sec
                        </label>

                        <select
                            name="section_id"
                            id="section_id"
                            class="form-select"
                        >

                            <option value="">
                                All
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

                    </div>


                    {{-- GENDER --}}

                    <div class="col-xl-2 col-lg-3 col-md-4">

                        <label class="form-label">
                            Gender
                        </label>

                        <select
                            name="gender"
                            class="form-select"
                        >

                            <option value="">
                                All
                            </option>

                            <option
                                value="male"
                                @selected(request('gender') === 'male')
                            >
                                Male
                            </option>

                            <option
                                value="female"
                                @selected(request('gender') === 'female')
                            >
                                Female
                            </option>

                            <option
                                value="other"
                                @selected(request('gender') === 'other')
                            >
                                Other
                            </option>

                        </select>

                    </div>


                    {{-- ENROLLMENT STATUS --}}

                    <div class="col-xl-2 col-lg-3 col-md-4">

                        <label class="form-label">
                            Enrollment Status
                        </label>

                        <select
                            name="enrollment_status"
                            class="form-select"
                        >

                            <option value="">
                                All
                            </option>

                            <option
                                value="enrolled"
                                @selected(
                                    request('enrollment_status')
                                    === 'enrolled'
                                )
                            >
                                Enrolled
                            </option>

                            <option
                                value="promoted"
                                @selected(
                                    request('enrollment_status')
                                    === 'promoted'
                                )
                            >
                                Promoted
                            </option>

                            <option
                                value="completed"
                                @selected(
                                    request('enrollment_status')
                                    === 'completed'
                                )
                            >
                                Completed
                            </option>

                            <option
                                value="withdrawn"
                                @selected(
                                    request('enrollment_status')
                                    === 'withdrawn'
                                )
                            >
                                Withdrawn
                            </option>

                        </select>

                    </div>


                    {{-- SEARCH --}}

                    <div class="col-xl-5 col-lg-6 col-md-8">

                        <label class="form-label">
                            Search
                        </label>

                        <div class="input-group">

                            <input
                                type="text"
                                name="search"
                                class="form-control"
                                value="{{ request('search') }}"
                                placeholder="Admission No, Student, Parent, Mobile, Roll No..."
                            >

                            <button
                                type="submit"
                                class="btn btn-primary"
                            >
                                <i class="bi bi-search"></i>
                                Search
                            </button>

                        </div>

                    </div>


                    {{-- BUTTONS --}}

                    <div class="col-xl-4 col-lg-6 col-md-4">

                        <label class="form-label d-block">
                            &nbsp;
                        </label>

                        <button
                            type="submit"
                            class="btn btn-dark"
                        >
                            <i class="bi bi-funnel"></i>
                            Apply Filters
                        </button>

                        <a
                            href="{{ route('students.index') }}"
                            class="btn btn-outline-secondary"
                        >
                            <i class="bi bi-arrow-counterclockwise"></i>
                            Reset
                        </a>

                    </div>

                </div>

            </form>

        </div>

    </div>


    {{-- =========================================================
        FILTER SUMMARY
    ========================================================== --}}

    <div class="d-flex justify-content-between align-items-center mb-3">

        <div>

            <span class="text-muted">
                Showing
            </span>

            <strong>
                {{ $students->firstItem() ?? 0 }}
                -
                {{ $students->lastItem() ?? 0 }}
            </strong>

            <span class="text-muted">
                of
            </span>

            <strong>
                {{ $students->total() }}
            </strong>

            <span class="text-muted">
                students
            </span>

        </div>

    </div>


    {{-- =========================================================
        STUDENT TABLE
    ========================================================== --}}

    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white py-3">

            <div class="d-flex justify-content-between align-items-center">

                <strong>

                    <i class="bi bi-people me-1"></i>

                    Students

                </strong>

                <span class="badge bg-primary">

                    {{ $students->total() }}

                </span>

            </div>

        </div>


        <div class="table-responsive">

            <table class="table table-bordered table-striped table-hover align-middle mb-0">

                <thead class="table-light">

                    <tr>

                        <th style="width:60px;">
                            #
                        </th>

                        <th>
                            Admission No.
                        </th>

                        <th>
                            Student
                        </th>

                        <th>
                            Gender
                        </th>

                        <th>
                            Academic Year
                        </th>

                        <th>
                            Class
                        </th>

                        <th>
                            Section
                        </th>

                        <th>
                            Roll
                        </th>

                        <th>
                            Parent
                        </th>

                        <th>
                            Status
                        </th>

                        <th
                            class="text-end"
                            style="width:120px;"
                        >
                            Action
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($students as $student)

                        <tr>


                            {{-- SERIAL --}}

                            <td>

                                {{
                                    $students->firstItem()
                                    + $loop->index
                                }}

                            </td>


                            {{-- ADMISSION NO --}}

                            <td>

                                <strong class="text-primary">

                                    {{ $student->admission_no }}

                                </strong>

                            </td>


                            {{-- STUDENT --}}

                            <td>

                                <div class="d-flex align-items-center gap-2">

                                    @if($student->student_photo)

                                        <img
                                            src="{{ asset('storage/' . $student->student_photo) }}"
                                            alt="{{ $student->student_name }}"
                                            class="rounded-circle border"
                                            style="
                                                width:42px;
                                                height:42px;
                                                object-fit:cover;
                                            "
                                        >

                                    @else

                                        <div
                                            class="rounded-circle bg-light border d-flex align-items-center justify-content-center"
                                            style="
                                                width:42px;
                                                height:42px;
                                                min-width:42px;
                                            "
                                        >

                                            <i class="bi bi-person text-muted"></i>

                                        </div>

                                    @endif


                                    <div>

                                        <strong>
                                            {{ $student->student_name }}
                                        </strong>

                                        @if($student->date_of_birth)

                                            <div class="small text-muted">

                                                DOB:
                                                {{
                                                    $student
                                                        ->date_of_birth
                                                        ->format('d M Y')
                                                }}

                                            </div>

                                        @endif

                                    </div>

                                </div>

                            </td>


                            {{-- GENDER --}}

                            <td>

                                @if($student->gender)

                                    {{ ucfirst($student->gender) }}

                                @else

                                    -

                                @endif

                            </td>


                            {{-- ACADEMIC YEAR --}}

                            <td>

                                {{
                                    $student
                                        ->currentEnrollment
                                        ?->academicYear
                                        ?->name
                                    ?? '-'
                                }}

                            </td>


                            {{-- CLASS --}}

                            <td>

                                {{
                                    $student
                                        ->currentEnrollment
                                        ?->schoolClass
                                        ?->name
                                    ?? '-'
                                }}

                            </td>


                            {{-- SECTION --}}

                            <td>

                                {{
                                    $student
                                        ->currentEnrollment
                                        ?->section
                                        ?->name
                                    ?? '-'
                                }}

                            </td>


                            {{-- ROLL --}}

                            <td>

                                {{
                                    $student
                                        ->currentEnrollment
                                        ?->roll_no
                                    ?? '-'
                                }}

                            </td>


                            {{-- PARENT --}}

                            <td>

                                <div>

                                    {{
                                        $student->father_name
                                        ?: $student->mother_name
                                        ?: $student->guardian_name
                                        ?: '-'
                                    }}

                                </div>


                                @php

                                    $parentMobile =
                                        $student->father_mobile
                                        ?: $student->mother_mobile
                                        ?: $student->guardian_mobile;

                                @endphp


                                @if($parentMobile)

                                    <small class="text-muted">

                                        <i class="bi bi-telephone"></i>

                                        {{ $parentMobile }}

                                    </small>

                                @endif

                            </td>


                            {{-- STATUS --}}

                            <td>

                                @if($student->status)

                                    <span class="badge bg-success">
                                        Active
                                    </span>

                                @else

                                    <span class="badge bg-danger">
                                        Inactive
                                    </span>

                                @endif

                            </td>


                            {{-- ACTION --}}

                            <td class="text-end">

                                @can('student.view')

                                    <a
                                        href="{{ route(
                                            'students.show',
                                            $student
                                        ) }}"
                                        class="btn btn-sm btn-outline-primary"
                                        title="View Student"
                                    >

                                        <i class="bi bi-eye"></i>

                                    </a>

                                @endcan


                                @can('student.edit')

                                    <a
                                        href="{{ route(
                                            'students.edit',
                                            $student
                                        ) }}"
                                        class="btn btn-sm btn-outline-warning"
                                        title="Edit Student"
                                    >

                                        <i class="bi bi-pencil"></i>

                                    </a>

                                @endcan

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="11"
                                class="text-center py-5"
                            >

                                <div class="text-muted">

                                    <i
                                        class="bi bi-search"
                                        style="font-size:2rem;"
                                    ></i>

                                    <div class="mt-2">

                                        No students found for the
                                        selected filters.

                                    </div>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        @if($students->hasPages())

            <div class="card-footer bg-white">

                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">

                    <div class="small text-muted">

                        Showing
                        <strong>{{ $students->firstItem() ?? 0 }}</strong>
                        to
                        <strong>{{ $students->lastItem() ?? 0 }}</strong>
                        of
                        <strong>{{ $students->total() }}</strong>
                        students

                    </div>

                    <div>
                        {{ $students->links() }}
                    </div>

                </div>

            </div>

        @endif

    </div>

</div>

@endsection