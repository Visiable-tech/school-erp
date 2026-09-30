@extends('layouts.admin')

@section('title', 'Fee Receipts')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h4 class="mb-1">Fee Receipts</h4>
            <small class="text-muted">
                View collected and cancelled fee receipts.
            </small>
        </div>

        @can('fee-collection.create')

            <a href="{{ route('fee-receipts.create') }}"
               class="btn btn-primary">

                <i class="bi bi-plus-circle"></i>
                Collect Fee

            </a>

        @endcan

    </div>


    <div class="card shadow-sm mb-4">

        <div class="card-body">

            <form method="GET">

                <div class="row g-3">

                    <div class="col-md-3">

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

                        <input type="text"
                               name="student"
                               class="form-control"
                               placeholder="Student / Admission No."
                               value="{{ request('student') }}">

                    </div>


                    <div class="col-md-2">

                        <input type="text"
                               name="receipt_no"
                               class="form-control"
                               placeholder="Receipt No."
                               value="{{ request('receipt_no') }}">

                    </div>


                    <div class="col-md-2">

                        <select name="status"
                                class="form-select">

                            <option value="">
                                All Status
                            </option>

                            <option value="posted"
                                {{ request('status') == 'posted' ? 'selected' : '' }}>
                                Posted
                            </option>

                            <option value="cancelled"
                                {{ request('status') == 'cancelled' ? 'selected' : '' }}>
                                Cancelled
                            </option>

                        </select>

                    </div>


                    <div class="col-md-2">

                        <button class="btn btn-primary">
                            Filter
                        </button>

                        <a href="{{ route('fee-receipts.index') }}"
                           class="btn btn-light">
                            Reset
                        </a>

                    </div>

                </div>

            </form>

        </div>

    </div>


    <div class="card shadow-sm">

        <div class="table-responsive">

            <table class="table table-bordered table-hover mb-0 align-middle">

                <thead class="table-light">

                    <tr>
                        <th>#</th>
                        <th>Receipt No.</th>
                        <th>Date</th>
                        <th>Student</th>
                        <th>Academic Year</th>
                        <th>Mode</th>
                        <th class="text-end">Amount</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>

                </thead>


                <tbody>

                    @forelse($receipts as $receipt)

                        <tr>

                            <td>
                                {{ $receipts->firstItem() + $loop->index }}
                            </td>


                            <td>
                                <strong>
                                    {{ $receipt->receipt_no }}
                                </strong>
                            </td>


                            <td>
                                {{ optional($receipt->payment_date)->format('d-m-Y') }}
                            </td>


                            <td>

                                {{ $receipt->student->student_name
                                    ?? $receipt->student->name
                                    ?? '-' }}

                                <br>

                                <small class="text-muted">
                                    {{ $receipt->student->admission_no ?? '' }}
                                </small>

                            </td>


                            <td>
                                {{ $receipt->academicYear->name ?? '-' }}
                            </td>


                            <td>
                                {{ $receipt->paymentMode->name
                                    ?? ucfirst($receipt->payment_mode ?? '-') }}
                            </td>


                            <td class="text-end fw-semibold">

                                ₹{{ number_format($receipt->total_amount, 2) }}

                            </td>


                            <td>

                                @if($receipt->status === 'posted')

                                    <span class="badge bg-success">
                                        Posted
                                    </span>

                                @else

                                    <span class="badge bg-danger">
                                        Cancelled
                                    </span>

                                @endif

                            </td>


                            <td>

                                <a href="{{ route('fee-receipts.show', $receipt) }}"
                                   class="btn btn-sm btn-outline-primary">

                                    <i class="bi bi-eye"></i>

                                </a>


                                @can('fee-collection.receipt')

                                    <a href="{{ route('fee-receipts.print', $receipt) }}"
                                       target="_blank"
                                       class="btn btn-sm btn-outline-dark">

                                        <i class="bi bi-printer"></i>

                                    </a>

                                @endcan

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="9"
                                class="text-center text-muted py-5">

                                No fee receipts found.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        @if($receipts->hasPages())

            <div class="card-footer">
                {{ $receipts->links() }}
            </div>

        @endif

    </div>

</div>

@endsection