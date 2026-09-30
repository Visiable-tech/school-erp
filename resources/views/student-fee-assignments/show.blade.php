@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <div>
            <h4 class="mb-1">
                Student Fee Details
            </h4>

            <small class="text-muted">
                Fee assignment and generated dues
            </small>
        </div>

        <a href="{{ route('student-fee-assignments.index') }}"
           class="btn btn-outline-secondary">

            <i class="bi bi-arrow-left"></i>
            Back

        </a>

    </div>


    <div class="card border-0 shadow-sm mb-3">

        <div class="card-body">

            <div class="row g-3">

                <div class="col-md-3">

                    <small class="text-muted">
                        Student
                    </small>

                    <div class="fw-semibold">
                        {{ $studentFeeAssignment->student->student_name }}
                    </div>

                    <small>
                        {{ $studentFeeAssignment->student->admission_no }}
                    </small>

                </div>


                <div class="col-md-2">

                    <small class="text-muted">
                        Class
                    </small>

                    <div class="fw-semibold">

                        {{ optional(
                            $studentFeeAssignment
                                ->enrollment
                                ->schoolClass
                        )->name }}

                        -

                        {{ optional(
                            $studentFeeAssignment
                                ->enrollment
                                ->section
                        )->name }}

                    </div>

                </div>


                <div class="col-md-2">

                    <small class="text-muted">
                        Roll No
                    </small>

                    <div class="fw-semibold">
                        {{ $studentFeeAssignment
                            ->enrollment
                            ->roll_no ?? '—' }}
                    </div>

                </div>


                <div class="col-md-2">

                    <small class="text-muted">
                        Academic Year
                    </small>

                    <div class="fw-semibold">
                        {{ $studentFeeAssignment
                            ->academicYear
                            ->name }}
                    </div>

                </div>


                <div class="col-md-3">

                    <small class="text-muted">
                        Fee Structure
                    </small>

                    <div class="fw-semibold">
                        {{ $studentFeeAssignment
                            ->feeStructure
                            ->name }}
                    </div>

                </div>

            </div>

        </div>

    </div>


    @php

        $baseTotal =
            $studentFeeAssignment
                ->dues
                ->sum('base_amount');

        $discountTotal =
            $studentFeeAssignment
                ->dues
                ->sum('discount_amount');

        $fineTotal =
            $studentFeeAssignment
                ->dues
                ->sum('fine_amount');

        $payableTotal =
            $studentFeeAssignment
                ->dues
                ->sum('payable_amount');

        $paidTotal =
            $studentFeeAssignment
                ->dues
                ->sum('paid_amount');

        $balanceTotal =
            $studentFeeAssignment
                ->dues
                ->sum('balance_amount');

    @endphp


    <div class="row g-3 mb-3">

        <div class="col-md-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <small class="text-muted">
                        Base Fee
                    </small>
                    <h5 class="mb-0">
                        ₹{{ number_format($baseTotal, 2) }}
                    </h5>
                </div>
            </div>
        </div>


        <div class="col-md-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <small class="text-muted">
                        Discount
                    </small>
                    <h5 class="mb-0 text-success">
                        ₹{{ number_format($discountTotal, 2) }}
                    </h5>
                </div>
            </div>
        </div>


        <div class="col-md-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <small class="text-muted">
                        Fine
                    </small>
                    <h5 class="mb-0 text-danger">
                        ₹{{ number_format($fineTotal, 2) }}
                    </h5>
                </div>
            </div>
        </div>


        <div class="col-md-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <small class="text-muted">
                        Payable
                    </small>
                    <h5 class="mb-0">
                        ₹{{ number_format($payableTotal, 2) }}
                    </h5>
                </div>
            </div>
        </div>


        <div class="col-md-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <small class="text-muted">
                        Paid
                    </small>
                    <h5 class="mb-0 text-primary">
                        ₹{{ number_format($paidTotal, 2) }}
                    </h5>
                </div>
            </div>
        </div>


        <div class="col-md-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <small class="text-muted">
                        Balance
                    </small>
                    <h5 class="mb-0 text-danger">
                        ₹{{ number_format($balanceTotal, 2) }}
                    </h5>
                </div>
            </div>
        </div>

    </div>


    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white">
            <strong>Fee Dues</strong>
        </div>

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">

                    <tr>
                        <th>#</th>
                        <th>Fee Head</th>
                        <th>Installment</th>
                        <th>Due Date</th>
                        <th class="text-end">Base</th>
                        <th class="text-end">Discount</th>
                        <th class="text-end">Fine</th>
                        <th class="text-end">Payable</th>
                        <th class="text-end">Paid</th>
                        <th class="text-end">Balance</th>
                        <th>Status</th>
                    </tr>

                </thead>

                <tbody>

                @foreach(
                    $studentFeeAssignment->dues
                    as $due
                )

                    <tr>

                        <td>{{ $loop->iteration }}</td>

                        <td>
                            {{ $due->fee_head_name }}
                        </td>

                        <td>
                            {{ $due->installment_name }}
                        </td>

                        <td>
                            {{ $due->due_date->format('d M Y') }}
                        </td>

                        <td class="text-end">
                            ₹{{ number_format(
                                $due->base_amount,
                                2
                            ) }}
                        </td>

                        <td class="text-end text-success">
                            ₹{{ number_format(
                                $due->discount_amount,
                                2
                            ) }}
                        </td>

                        <td class="text-end text-danger">
                            ₹{{ number_format(
                                $due->fine_amount,
                                2
                            ) }}
                        </td>

                        <td class="text-end fw-semibold">
                            ₹{{ number_format(
                                $due->payable_amount,
                                2
                            ) }}
                        </td>

                        <td class="text-end">
                            ₹{{ number_format(
                                $due->paid_amount,
                                2
                            ) }}
                        </td>

                        <td class="text-end fw-semibold">
                            ₹{{ number_format(
                                $due->balance_amount,
                                2
                            ) }}
                        </td>

                        <td>

                            @if(
                                $due->payment_status === 'paid'
                            )

                                <span class="badge bg-success">
                                    Paid
                                </span>

                            @elseif(
                                $due->payment_status === 'partial'
                            )

                                <span class="badge bg-warning text-dark">
                                    Partial
                                </span>

                            @else

                                <span class="badge bg-danger">
                                    Unpaid
                                </span>

                            @endif

                        </td>

                    </tr>

                @endforeach

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection