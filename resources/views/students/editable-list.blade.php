@extends('layouts.admin')

@section('title', 'Editable Student List')

@section('content')

<div class="container-fluid">

    {{-- PAGE HEADER --}}

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h4 class="mb-1">
                Editable Student List
            </h4>

            <div class="text-muted">
                Update common student information directly from the list.
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


    {{-- VALIDATION --}}

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
        FILTERS
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
                action="{{ route('students.editable-list') }}"
            >

                <div class="row g-3">


                    {{-- STATUS --}}

                    <div class="col-lg-2 col-md-4">

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

                    <div class="col-lg-2 col-md-4">

                        <label class="form-label">
                            Academic Year
                        </label>

                        <select
                            name="academic_year_id"
                            class="form-select"
                        >

                            <option value="">
                                All
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

                    <div class="col-lg-2 col-md-4">

                        <label class="form-label">
                            Class
                        </label>

                        <select
                            name="school_class_id"
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

                    <div class="col-lg-2 col-md-4">

                        <label class="form-label">
                            Class-Sec
                        </label>

                        <select
                            name="section_id"
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

                    <div class="col-lg-2 col-md-4">

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


                    {{-- SEARCH --}}

                    <div class="col-lg-4 col-md-8">

                        <label class="form-label">
                            Search
                        </label>

                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            value="{{ request('search') }}"
                            placeholder="Admission No, Name, Mobile, Roll..."
                        >

                    </div>


                    <div class="col-lg-4 col-md-4">

                        <label class="form-label d-block">
                            &nbsp;
                        </label>

                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            <i class="bi bi-search"></i>
                            Search
                        </button>

                        <a
                            href="{{ route('students.editable-list') }}"
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
        STUDENTS
    ========================================================== --}}

    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white py-3">

            <div class="d-flex justify-content-between align-items-center">

                <strong>

                    <i class="bi bi-pencil-square me-1"></i>

                    Editable Students

                </strong>


                <span class="badge bg-primary">

                    {{ $students->total() }}

                </span>

            </div>

        </div>


        <div class="card-body p-0">

            <div class="table-responsive">

                <table
                    class="table table-bordered table-striped table-hover align-middle mb-0"
                >

                    <thead class="table-light">

                        <tr>

                            <th style="width:50px;">
                                #
                            </th>

                            <th style="min-width:130px;">
                                Admission No.
                            </th>

                            <th style="min-width:200px;">
                                Student Name
                            </th>

                            <th style="min-width:140px;">
                                DOB
                            </th>

                            <th style="min-width:130px;">
                                Gender
                            </th>

                            <th style="min-width:100px;">
                                Class
                            </th>

                            <th style="min-width:100px;">
                                Section
                            </th>

                            <th style="min-width:100px;">
                                Roll No.
                            </th>

                            <th style="min-width:150px;">
                                Father Mobile
                            </th>

                            <th style="min-width:130px;">
                                Status
                            </th>

                            <th style="min-width:100px;">
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($students as $student)

                            @php

                                $formId =
                                    'student-edit-form-'
                                    . $student->id;

                            @endphp


                            <tr>


                                {{-- SERIAL --}}

                                <td>

                                    {{
                                        $students->firstItem()
                                        + $loop->index
                                    }}

                                </td>


                                {{-- ADMISSION --}}

                                <td>

                                    <strong class="text-primary">

                                        {{ $student->admission_no }}

                                    </strong>

                                </td>


                                {{-- NAME --}}

                                <td>

                                    <input
                                        form="{{ $formId }}"
                                        type="text"
                                        name="student_name"
                                        class="form-control form-control-sm"
                                        value="{{ $student->student_name }}"
                                        required
                                    >

                                </td>


                                {{-- DOB --}}

                                <td>

                                    <input
                                        form="{{ $formId }}"
                                        type="date"
                                        name="date_of_birth"
                                        class="form-control form-control-sm"
                                        value="{{
                                            $student->date_of_birth
                                            ? $student
                                                ->date_of_birth
                                                ->format('Y-m-d')
                                            : ''
                                        }}"
                                    >

                                </td>


                                {{-- GENDER --}}

                                <td>

                                    <select
                                        form="{{ $formId }}"
                                        name="gender"
                                        class="form-select form-select-sm"
                                    >

                                        <option value="">
                                            Select
                                        </option>

                                        <option
                                            value="male"
                                            @selected(
                                                $student->gender
                                                === 'male'
                                            )
                                        >
                                            Male
                                        </option>

                                        <option
                                            value="female"
                                            @selected(
                                                $student->gender
                                                === 'female'
                                            )
                                        >
                                            Female
                                        </option>

                                        <option
                                            value="other"
                                            @selected(
                                                $student->gender
                                                === 'other'
                                            )
                                        >
                                            Other
                                        </option>

                                    </select>

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

                                    <input
                                        form="{{ $formId }}"
                                        type="text"
                                        name="roll_no"
                                        class="form-control form-control-sm"
                                        value="{{
                                            $student
                                                ->currentEnrollment
                                                ?->roll_no
                                        }}"
                                    >

                                </td>


                                {{-- FATHER MOBILE --}}

                                <td>

                                    <input
                                        form="{{ $formId }}"
                                        type="text"
                                        name="father_mobile"
                                        class="form-control form-control-sm"
                                        value="{{ $student->father_mobile }}"
                                    >

                                </td>


                                {{-- STATUS --}}

                                <td>

                                    <select
                                        form="{{ $formId }}"
                                        name="status"
                                        class="form-select form-select-sm"
                                    >

                                        <option
                                            value="1"
                                            @selected($student->status)
                                        >
                                            Active
                                        </option>

                                        <option
                                            value="0"
                                            @selected(!$student->status)
                                        >
                                            Inactive
                                        </option>

                                    </select>

                                </td>


                                {{-- ACTION --}}

                                <td>

                                    <form
                                        id="{{ $formId }}"
                                        method="POST"
                                        action="{{
                                            route(
                                                'students.editable-list.update',
                                                $student
                                            )
                                        }}"
                                    >

                                        @csrf
                                        @method('PUT')

                                    </form>


                                    <button
                                        form="{{ $formId }}"
                                        type="submit"
                                        class="btn btn-sm btn-success"
                                        title="Save"
                                    >
                                        <i class="bi bi-check-lg"></i>
                                    </button>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="11"
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


        {{-- BOOTSTRAP PAGINATION --}}

        @if($students->hasPages())

            <div class="card-footer bg-white">

                <div
                    class="d-flex justify-content-between align-items-center flex-wrap gap-2"
                >

                    <div class="small text-muted">

                        Showing

                        <strong>
                            {{ $students->firstItem() ?? 0 }}
                        </strong>

                        to

                        <strong>
                            {{ $students->lastItem() ?? 0 }}
                        </strong>

                        of

                        <strong>
                            {{ $students->total() }}
                        </strong>

                        students

                    </div>


                    <div>

                        {{
                            $students->links(
                                'pagination::bootstrap-5'
                            )
                        }}

                    </div>

                </div>

            </div>

        @endif

    </div>

</div>

@endsection