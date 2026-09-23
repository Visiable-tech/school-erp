@extends('layouts.admin')

@section('title', 'Student Profile')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h4 class="mb-1">
            {{ $student->student_name }}
        </h4>

        <div class="text-muted">

            Admission No:
            {{ $student->admission_no }}

        </div>

    </div>


    <div>

        @can('student.edit')

            <a
                href="{{ route('students.edit', $student) }}"
                class="btn btn-primary"
            >
                <i class="bi bi-pencil"></i>
                Edit Student
            </a>

        @endcan

       @can('student-document.view')

            <a
                href="{{ route(
                    'student-documents.student',
                    $student
                ) }}"
                class="btn btn-outline-primary"
            >
                <i class="bi bi-folder2-open"></i>
                Documents

                @if($student->documents_count > 0)

                    <span class="badge bg-primary ms-1">
                        {{ $student->documents_count }}
                    </span>

                @endif

            </a>

        @endcan


        <a
            href="{{ route('students.index') }}"
            class="btn btn-outline-secondary"
        >
            <i class="bi bi-arrow-left"></i>
            Back
        </a>

    </div>

</div>


<div class="row">


    <div class="col-md-8">


        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white py-3">

                <strong>
                    Student Information
                </strong>

            </div>


            <div class="card-body">

                <div class="row g-4">

                    <div class="col-md-4">

                        <small class="text-muted">
                            Admission Date
                        </small>

                        <div class="fw-semibold">

                            {{
                                $student
                                    ->admission_date
                                    ->format('d M Y')
                            }}

                        </div>

                    </div>


                    <div class="col-md-4">

                        <small class="text-muted">
                            Date of Birth
                        </small>

                        <div class="fw-semibold">

                            {{
                                $student
                                    ->date_of_birth
                                    ?->format('d M Y')
                                ?? '-'
                            }}

                        </div>

                    </div>


                    <div class="col-md-4">

                        <small class="text-muted">
                            Gender
                        </small>

                        <div class="fw-semibold">

                            {{
                                ucfirst(
                                    $student->gender
                                    ?? '-'
                                )
                            }}

                        </div>

                    </div>


                    <div class="col-md-4">

                        <small class="text-muted">
                            Blood Group
                        </small>

                        <div class="fw-semibold">

                            {{
                                $student->blood_group
                                ?: '-'
                            }}

                        </div>

                    </div>


                    <div class="col-md-4">

                        <small class="text-muted">
                            Father
                        </small>

                        <div class="fw-semibold">

                            {{
                                $student->father_name
                                ?: '-'
                            }}

                        </div>

                    </div>


                    <div class="col-md-4">

                        <small class="text-muted">
                            Father Mobile
                        </small>

                        <div class="fw-semibold">

                            {{
                                $student->father_mobile
                                ?: '-'
                            }}

                        </div>

                    </div>

                </div>

            </div>

        </div>


    </div>



    <div class="col-md-4">


        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white py-3">

                <strong>
                    Current Academic Details
                </strong>

            </div>


            <div class="card-body">

                <div class="mb-3">

                    <small class="text-muted">
                        Academic Year
                    </small>

                    <div class="fw-semibold">

                        {{
                            $student
                                ->currentEnrollment
                                ?->academicYear
                                ?->name
                            ?? '-'
                        }}

                    </div>

                </div>


                <div class="mb-3">

                    <small class="text-muted">
                        Class
                    </small>

                    <div class="fw-semibold">

                        {{
                            $student
                                ->currentEnrollment
                                ?->schoolClass
                                ?->name
                            ?? '-'
                        }}

                    </div>

                </div>


                <div class="mb-3">

                    <small class="text-muted">
                        Section
                    </small>

                    <div class="fw-semibold">

                        {{
                            $student
                                ->currentEnrollment
                                ?->section
                                ?->name
                            ?? '-'
                        }}

                    </div>

                </div>


                <div>

                    <small class="text-muted">
                        Roll Number
                    </small>

                    <div class="fw-semibold">

                        {{
                            $student
                                ->currentEnrollment
                                ?->roll_no
                            ?? '-'
                        }}

                    </div>

                </div>

            </div>

        </div>


    </div>


</div>

<div class="card border-0 shadow-sm mb-4">

    <div class="card-header bg-white py-3">

        <strong>
            <i class="bi bi-mortarboard me-1"></i>
            Academic History
        </strong>

        @can('student-enrollment.create')

            <a
                href="{{ route(
                    'student-enrollments.create',
                    $student
                ) }}"
                class="btn btn-sm btn-primary"
            >
                <i class="bi bi-plus-circle"></i>
                Add Enrollment
            </a>

        @endcan

    </div>


    <div class="card-body p-0">

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">

                    <tr>
                        <th class="ps-3">Academic Year</th>
                        <th>Class</th>
                        <th>Section</th>
                        <th>Roll No.</th>
                        <th>Enrollment Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>

                </thead>


                <tbody>

                    @forelse($student->enrollments as $enrollment)

                        <tr>

                            <td class="ps-3">

                                {{
                                    $enrollment
                                        ->academicYear
                                        ?->name
                                    ?? '-'
                                }}

                            </td>


                            <td>

                                {{
                                    $enrollment
                                        ->schoolClass
                                        ?->name
                                    ?? '-'
                                }}

                            </td>


                            <td>

                                {{
                                    $enrollment
                                        ->section
                                        ?->name
                                    ?? '-'
                                }}

                            </td>


                            <td>

                                {{
                                    $enrollment->roll_no
                                    ?: '-'
                                }}

                            </td>


                            <td>

                                {{
                                    $enrollment
                                        ->enrollment_date
                                        ?->format('d M Y')
                                    ?? '-'
                                }}

                            </td>


                            <td>

                                @if($enrollment->is_current)

                                    <span class="badge bg-success">
                                        Current
                                    </span>

                                @else

                                    <span class="badge bg-secondary">

                                        {{
                                            ucwords(
                                                str_replace(
                                                    '_',
                                                    ' ',
                                                    $enrollment
                                                        ->enrollment_status
                                                )
                                            )
                                        }}

                                    </span>

                                @endif

                            </td>

                            <td>

                                @can('student-enrollment.edit')

                                    <a
                                        href="{{ route(
                                            'student-enrollments.edit',
                                            $enrollment
                                        ) }}"
                                        class="btn btn-sm btn-outline-primary"
                                        title="Edit Enrollment"
                                    >
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                @endcan

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="7"
                                class="text-center py-4 text-muted"
                            >
                                No academic enrollment found.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>



@endsection
