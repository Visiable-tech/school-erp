@extends('layouts.admin')

@section('title', 'Composite Concessions')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h4 class="mb-1">
                Composite Concessions
            </h4>

            <small class="text-muted">
                Manage combined concession and waiver requests.
            </small>

        </div>


        @can('composite-concession.create')

            <a href="{{ route('composite-concessions.create') }}"
               class="btn btn-primary">

                <i class="bi bi-plus-circle"></i>
                Add Composite Concession

            </a>

        @endcan

    </div>


    {{-- Filters --}}

    <div class="card shadow-sm mb-4">

        <div class="card-body">

            <form method="GET"
                  action="{{ route('composite-concessions.index') }}">

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
                               placeholder="Name / Admission No."
                               value="{{ request('student') }}">

                    </div>


                    <div class="col-md-3">

                        <label class="form-label">
                            Status
                        </label>

                        <select name="approval_status"
                                class="form-select">

                            <option value="">
                                All Status
                            </option>

                            <option value="pending"
                                {{ request('approval_status') == 'pending' ? 'selected' : '' }}>
                                Pending
                            </option>

                            <option value="approved"
                                {{ request('approval_status') == 'approved' ? 'selected' : '' }}>
                                Approved
                            </option>

                            <option value="rejected"
                                {{ request('approval_status') == 'rejected' ? 'selected' : '' }}>
                                Rejected
                            </option>

                        </select>

                    </div>


                    <div class="col-md-3 d-flex align-items-end gap-2">

                        <button type="submit"
                                class="btn btn-primary">

                            <i class="bi bi-search"></i>
                            Filter

                        </button>


                        <a href="{{ route('composite-concessions.index') }}"
                           class="btn btn-light">

                            Reset

                        </a>

                    </div>

                </div>

            </form>

        </div>

    </div>


    {{-- Table --}}

    <div class="card shadow-sm">

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover table-bordered mb-0 align-middle">

                    <thead class="table-light">

                        <tr>

                            <th width="60">
                                #
                            </th>

                            <th>
                                Student
                            </th>

                            <th>
                                Class
                            </th>

                            <th>
                                Academic Year
                            </th>

                            <th>
                                Benefits
                            </th>

                            <th>
                                Date
                            </th>

                            <th>
                                Status
                            </th>

                            <th width="240">
                                Action
                            </th>

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
                                        {{ $assignment->student->student_name ?? $assignment->student->name ?? '-' }}
                                    </strong>

                                    @if($assignment->student->admission_no ?? null)

                                        <br>

                                        <small class="text-muted">
                                            {{ $assignment->student->admission_no }}
                                        </small>

                                    @endif

                                </td>


                                <td>

                                    {{ $assignment->enrollment->schoolClass->name ?? '-' }}

                                    @if($assignment->enrollment->section ?? null)

                                        -
                                        {{ $assignment->enrollment->section->name }}

                                    @endif

                                </td>


                                <td>
                                    {{ $assignment->academicYear->name ?? '-' }}
                                </td>


                                <td>

                                    @if($assignment->concession_mode != 'none')

                                        <span class="badge bg-primary mb-1">

                                            Concession:

                                            @if($assignment->concession_mode == 'percentage')

                                                {{ number_format($assignment->concession_value, 2) }}%

                                            @else

                                                ₹{{ number_format($assignment->concession_value, 2) }}

                                            @endif

                                        </span>

                                    @endif


                                    @if($assignment->fee_waiver_mode != 'none')

                                        <span class="badge bg-success mb-1">

                                            Fee Waiver:

                                            @if($assignment->fee_waiver_mode == 'full')

                                                Full

                                            @elseif($assignment->fee_waiver_mode == 'percentage')

                                                {{ number_format($assignment->fee_waiver_value, 2) }}%

                                            @else

                                                ₹{{ number_format($assignment->fee_waiver_value, 2) }}

                                            @endif

                                        </span>

                                    @endif


                                    @if($assignment->fine_waiver_mode != 'none')

                                        <span class="badge bg-warning text-dark mb-1">

                                            Fine Waiver:

                                            @if($assignment->fine_waiver_mode == 'full')

                                                Full

                                            @elseif($assignment->fine_waiver_mode == 'percentage')

                                                {{ number_format($assignment->fine_waiver_value, 2) }}%

                                            @else

                                                ₹{{ number_format($assignment->fine_waiver_value, 2) }}

                                            @endif

                                        </span>

                                    @endif

                                </td>


                                <td>

                                    {{ optional($assignment->assigned_date)->format('d-m-Y') }}

                                </td>


                                <td>

                                    @if($assignment->approval_status == 'approved')

                                        <span class="badge bg-success">
                                            Approved
                                        </span>

                                    @elseif($assignment->approval_status == 'rejected')

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

                                    <div class="d-flex flex-wrap gap-1">


                                        {{-- APPROVE --}}

                                        @if($assignment->approval_status == 'pending')

                                            @can('composite-concession.approve')

                                                <form method="POST"
                                                      action="{{ route('composite-concessions.approve', $assignment) }}">

                                                    @csrf

                                                    <button type="submit"
                                                            class="btn btn-sm btn-success"
                                                            onclick="return confirm('Approve and apply this composite concession?')">

                                                        <i class="bi bi-check-circle"></i>
                                                        Approve

                                                    </button>

                                                </form>


                                                <form method="POST"
                                                      action="{{ route('composite-concessions.reject', $assignment) }}">

                                                    @csrf

                                                    <button type="submit"
                                                            class="btn btn-sm btn-warning"
                                                            onclick="return confirm('Reject this composite concession?')">

                                                        <i class="bi bi-x-circle"></i>
                                                        Reject

                                                    </button>

                                                </form>

                                            @endcan

                                        @endif


                                        {{-- DELETE / REVERSE --}}

                                        @can('composite-concession.delete')

                                            <form method="POST"
                                                  action="{{ route('composite-concessions.destroy', $assignment) }}">

                                                @csrf
                                                @method('DELETE')


                                                @if($assignment->approval_status == 'approved')

                                                    <button type="submit"
                                                            class="btn btn-sm btn-danger"
                                                            onclick="return confirm('This will reverse the concession and waiver amounts. Continue?')">

                                                        <i class="bi bi-arrow-counterclockwise"></i>
                                                        Reverse

                                                    </button>

                                                @else

                                                    <button type="submit"
                                                            class="btn btn-sm btn-outline-danger"
                                                            onclick="return confirm('Delete this request?')">

                                                        <i class="bi bi-trash"></i>

                                                    </button>

                                                @endif

                                            </form>

                                        @endcan

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="8"
                                    class="text-center text-muted py-5">

                                    No composite concession records found.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        @if($assignments->hasPages())

            <div class="card-footer">

                {{ $assignments->links() }}

            </div>

        @endif

    </div>

</div>

@endsection