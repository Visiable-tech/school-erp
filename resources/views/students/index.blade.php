@extends('layouts.admin')

@section('title', 'Students')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h4 class="mb-1">
            Students
        </h4>

        <div class="text-muted">
            Student Information Management
        </div>

    </div>


    <div class="d-flex gap-2">

        @can('student.create')

            <a
                href="{{ route('students.import') }}"
                class="btn btn-success"
            >
                <i class="bi bi-file-earmark-excel"></i>
                Import Excel
            </a>


            <a
                href="{{ route('students.create') }}"
                class="btn btn-primary"
            >
                <i class="bi bi-person-plus"></i>
                Add Student
            </a>

        @endcan

    </div>

</div>


<div class="card border-0 shadow-sm mb-4">

    <div class="card-body">

        <form method="GET">

            <div class="row">

                <div class="col-md-8">

                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        value="{{ request('search') }}"
                        placeholder="Admission No, student, father, mobile..."
                    >

                </div>


                <div class="col-md-4">

                    <button
                        class="btn btn-primary"
                    >
                        Filter
                    </button>


                    <a
                        href="{{ route('students.index') }}"
                        class="btn btn-light"
                    >
                        Reset
                    </a>

                </div>

            </div>

        </form>

    </div>

</div>


<div class="card border-0 shadow-sm">

    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-hover align-middle">

                <thead class="table-light">

                    <tr>

                        <th>Admission No</th>
                        <th>Student</th>
                        <th>Class</th>
                        <th>Section</th>
                        <th>Roll</th>
                        <th>Parent</th>
                        <th>Status</th>
                        <th>Action</th>

                    </tr>

                </thead>


                <tbody>

                @forelse($students as $student)

                    <tr>

                        <td>

                            <strong>
                                {{ $student->admission_no }}
                            </strong>

                        </td>


                        <td>

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

                        </td>


                        <td>

                            {{
                                $student
                                    ->currentEnrollment
                                    ?->schoolClass
                                    ?->name
                                ?? '-'
                            }}

                        </td>


                        <td>

                            {{
                                $student
                                    ->currentEnrollment
                                    ?->section
                                    ?->name
                                ?? '-'
                            }}

                        </td>


                        <td>

                            {{
                                $student
                                    ->currentEnrollment
                                    ?->roll_no
                                ?? '-'
                            }}

                        </td>


                        <td>

                            {{
                                $student->father_name
                                ?: '-'
                            }}

                            @if($student->father_mobile)

                                <div class="small text-muted">

                                    {{ $student->father_mobile }}

                                </div>

                            @endif

                        </td>


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


                        <td>

                            @can('student.view')

                                <a
                                    href="{{ route('students.show', $student) }}"
                                    class="btn btn-sm btn-outline-primary"
                                    title="View Student"
                                >
                                    <i class="bi bi-eye"></i>
                                </a>

                            @endcan


                            @can('student.edit')

                                <a
                                    href="{{ route('students.edit', $student) }}"
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
                            colspan="8"
                            class="text-center py-5 text-muted"
                        >
                            No students found.
                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>


        <div class="mt-3">

            {{ $students->links() }}

        </div>

    </div>

</div>

@endsection