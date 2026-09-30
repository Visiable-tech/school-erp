@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <div>
            <h4 class="mb-1">
                Student Fee Assignments
            </h4>

            <small class="text-muted">
                Manage student fee structures
            </small>
        </div>

        <div class="d-flex gap-2">

            @can('student-fee-assignment.bulk-create')

                <a href="{{ route('student-fee-assignments.bulk') }}"
                class="btn btn-success">

                    <i class="bi bi-people"></i>
                    Bulk Assign Fees

                </a>

            @endcan


            @can('student-fee-assignment.create')

                <a href="{{ route('student-fee-assignments.create') }}"
                class="btn btn-primary">

                    <i class="bi bi-plus-circle"></i>
                    Assign Individual Fee

                </a>

            @endcan

        </div>

    </div>


    <div class="card border-0 shadow-sm mb-3">

        <div class="card-body">

            <form method="GET">

                <div class="row g-2">

                    <div class="col-md-4">

                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               class="form-control"
                               placeholder="Student / Admission No">

                    </div>


                    <div class="col-md-3">

                        <select name="academic_year_id"
                                class="form-select">

                            <option value="">
                                All Academic Years
                            </option>

                            @foreach($academicYears as $year)

                                <option value="{{ $year->id }}"
                                    {{ request('academic_year_id') == $year->id
                                        ? 'selected'
                                        : '' }}>

                                    {{ $year->name }}

                                </option>

                            @endforeach

                        </select>

                    </div>


                    <div class="col-md-2">

                        <button class="btn btn-primary w-100">
                            <i class="bi bi-search"></i>
                            Search
                        </button>

                    </div>


                    <div class="col-md-2">

                        <a href="{{ route('student-fee-assignments.index') }}"
                           class="btn btn-light w-100">
                            Reset
                        </a>

                    </div>

                </div>

            </form>

        </div>

    </div>


    <div class="card border-0 shadow-sm">

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">

                    <tr>
                        <th>#</th>
                        <th>Student</th>
                        <th>Class</th>
                        <th>Academic Year</th>
                        <th>Fee Structure</th>
                        <th>Discount</th>
                        <th>Assigned</th>
                        <th>Status</th>
                        <th width="80">Action</th>
                    </tr>

                </thead>

                <tbody>

                @forelse($assignments as $assignment)

                    <tr>

                        <td>
                            {{ $assignments->firstItem() + $loop->index }}
                        </td>


                        <td>

                            <strong>
                                {{ $assignment->student->student_name }}
                            </strong>

                            <div class="small text-muted">
                                {{ $assignment->student->admission_no }}
                            </div>

                        </td>


                        <td>

                            {{ optional(
                                $assignment->enrollment->schoolClass
                            )->name }}

                            -

                            {{ optional(
                                $assignment->enrollment->section
                            )->name }}

                            @if($assignment->enrollment->roll_no)

                                <div class="small text-muted">
                                    Roll:
                                    {{ $assignment->enrollment->roll_no }}
                                </div>

                            @endif

                        </td>


                        <td>
                            {{ $assignment->academicYear->name }}
                        </td>


                        <td>
                            {{ $assignment->feeStructure->name }}
                        </td>


                        <td>

                            @if(
                                $assignment->discount_type === 'percentage'
                            )

                                {{ number_format(
                                    $assignment->discount_value,
                                    2
                                ) }}%

                            @elseif(
                                $assignment->discount_type === 'fixed'
                            )

                                ₹{{ number_format(
                                    $assignment->discount_value,
                                    2
                                ) }}

                            @else

                                —

                            @endif

                        </td>


                        <td>
                            {{ $assignment->assigned_date->format('d M Y') }}
                        </td>


                        <td>

                            @if($assignment->status)

                                <span class="badge bg-success">
                                    Active
                                </span>

                            @else

                                <span class="badge bg-secondary">
                                    Inactive
                                </span>

                            @endif

                        </td>


                        <td>

                            <a href="{{ route(
                                    'student-fee-assignments.show',
                                    $assignment
                                ) }}"
                               class="btn btn-sm btn-outline-primary">

                                <i class="bi bi-eye"></i>

                            </a>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="9"
                            class="text-center py-5 text-muted">

                            No student fee assignments found.

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>


        @if($assignments->hasPages())

            <div class="card-footer bg-white">

                {{ $assignments->links() }}

            </div>

        @endif

    </div>

</div>

@endsection