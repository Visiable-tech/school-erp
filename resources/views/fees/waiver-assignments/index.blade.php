@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h4 class="mb-1">
                Fee Waiver Assignments
            </h4>

            <small class="text-muted">
                Manage student fee waiver requests and approvals.
            </small>
        </div>

        @can('fee-waiver-assignment.create')

            <a href="{{ route('fee-waiver-assignments.create') }}"
               class="btn btn-primary">

                <i class="bi bi-plus-circle"></i>
                New Fee Waiver

            </a>

        @endcan

    </div>


    @if(session('success'))

        <div class="alert alert-success">
            {{ session('success') }}
        </div>

    @endif


    @if(session('error'))

        <div class="alert alert-danger">
            {{ session('error') }}
        </div>

    @endif


    <div class="card shadow-sm mb-4">

        <div class="card-body">

            <form method="GET">

                <div class="row g-3">

                    <div class="col-md-3">

                        <label class="form-label">
                            Academic Year
                        </label>

                        <select name="academic_year_id"
                                class="form-select">

                            <option value="">
                                All Academic Years
                            </option>

                            @foreach($academicYears as $year)

                                <option value="{{ $year->id }}"
                                    {{ request('academic_year_id') == $year->id ? 'selected' : '' }}>

                                    {{ $year->name }}

                                </option>

                            @endforeach

                        </select>

                    </div>


                    <div class="col-md-3">

                        <label class="form-label">
                            Student
                        </label>

                        <input type="text"
                               name="student"
                               class="form-control"
                               value="{{ request('student') }}"
                               placeholder="Name / Admission No.">

                    </div>


                    <div class="col-md-3">

                        <label class="form-label">
                            Approval Status
                        </label>

                        <select name="approval_status"
                                class="form-select">

                            <option value="">
                                All
                            </option>

                            <option value="pending"
                                {{ request('approval_status') === 'pending' ? 'selected' : '' }}>
                                Pending
                            </option>

                            <option value="approved"
                                {{ request('approval_status') === 'approved' ? 'selected' : '' }}>
                                Approved
                            </option>

                            <option value="rejected"
                                {{ request('approval_status') === 'rejected' ? 'selected' : '' }}>
                                Rejected
                            </option>

                        </select>

                    </div>


                    <div class="col-md-3 d-flex align-items-end gap-2">

                        <button class="btn btn-primary">
                            Filter
                        </button>

                        <a href="{{ route('fee-waiver-assignments.index') }}"
                           class="btn btn-outline-secondary">
                            Reset
                        </a>

                    </div>

                </div>

            </form>

        </div>

    </div>


    <div class="card shadow-sm">

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">

                        <tr>

                            <th>#</th>

                            <th>Student</th>

                            <th>Class</th>

                            <th>Waiver</th>

                            <th>Selected Dues</th>

                            <th>Date</th>

                            <th>Status</th>

                            <th width="220">
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                    @forelse($assignments as $assignment)

                        <tr>

                            <td>
                                {{ $assignment->id }}
                            </td>


                            <td>

                                <strong>
                                    {{ $assignment->student->student_name ?? '-' }}
                                </strong>

                                <br>

                                <small class="text-muted">
                                    {{ $assignment->student->admission_no ?? '-' }}
                                </small>

                            </td>


                            <td>

                                {{ $assignment->enrollment->schoolClass->name ?? '-' }}

                                @if($assignment->enrollment?->section)

                                    /
                                    {{ $assignment->enrollment->section->name }}

                                @endif

                            </td>


                            <td>

                                @if($assignment->waiver_mode === 'full')

                                    <span class="badge bg-dark">
                                        Full
                                    </span>

                                @elseif($assignment->waiver_mode === 'percentage')

                                    {{ number_format($assignment->waiver_value, 2) }}%

                                @else

                                    ₹{{ number_format($assignment->waiver_value, 2) }}

                                @endif

                            </td>


                            <td>

                                {{ count($assignment->selected_due_ids ?? []) }}

                            </td>


                            <td>

                                {{ optional($assignment->assigned_date)->format('d-m-Y') }}

                            </td>


                            <td>

                                @if($assignment->approval_status === 'approved')

                                    <span class="badge bg-success">
                                        Approved
                                    </span>

                                @elseif($assignment->approval_status === 'rejected')

                                    <span class="badge bg-danger">
                                        Rejected
                                    </span>

                                @else

                                    <span class="badge bg-warning text-dark">
                                        Pending
                                    </span>

                                @endif

                            </td>


                            <td>

                                @if($assignment->approval_status === 'pending')

                                    @can('fee-waiver-assignment.approve')

                                        <form method="POST"
                                              action="{{ route('fee-waiver-assignments.approve', $assignment) }}"
                                              class="d-inline">

                                            @csrf

                                            <button type="submit"
                                                    class="btn btn-sm btn-success"
                                                    onclick="return confirm('Approve and apply this fee waiver?')">

                                                Approve

                                            </button>

                                        </form>


                                        <form method="POST"
                                              action="{{ route('fee-waiver-assignments.reject', $assignment) }}"
                                              class="d-inline">

                                            @csrf

                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('Reject this fee waiver request?')">

                                                Reject

                                            </button>

                                        </form>

                                    @endcan

                                @endif


                                @can('fee-waiver-assignment.delete')

                                    <form method="POST"
                                          action="{{ route('fee-waiver-assignments.destroy', $assignment) }}"
                                          class="d-inline">

                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="btn btn-sm btn-outline-secondary"
                                                onclick="return confirm('Delete/reverse this fee waiver?')">

                                            Delete

                                        </button>

                                    </form>

                                @endcan

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="8"
                                class="text-center py-5 text-muted">

                                No fee waiver assignments found.

                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        @if($assignments->hasPages())

            <div class="card-footer bg-white">

                {{ $assignments->links() }}

            </div>

        @endif

    </div>

</div>

@endsection