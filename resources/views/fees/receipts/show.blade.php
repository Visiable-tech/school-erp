@extends('layouts.admin')

@section('title', 'Fee Receipt')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between mb-4">

        <div>
            <h4>Fee Receipt</h4>

            <strong>
                {{ $feeReceipt->receipt_no }}
            </strong>
        </div>


        <div class="d-flex gap-2">

            <a href="{{ route('fee-receipts.index') }}"
               class="btn btn-light">

                Back

            </a>


            @can('fee-collection.receipt')

                <a href="{{ route('fee-receipts.print', $feeReceipt) }}"
                   target="_blank"
                   class="btn btn-dark">

                    <i class="bi bi-printer"></i>
                    Print

                </a>

            @endcan

        </div>

    </div>


    <div class="card shadow-sm mb-4">

        <div class="card-body">

            <div class="row g-3">

                <div class="col-md-4">
                    <small class="text-muted">Student</small>
                    <div class="fw-semibold">
                        {{ $feeReceipt->student->student_name
                            ?? $feeReceipt->student->name
                            ?? '-' }}
                    </div>
                </div>


                <div class="col-md-4">
                    <small class="text-muted">Admission No.</small>
                    <div>
                        {{ $feeReceipt->student->admission_no ?? '-' }}
                    </div>
                </div>


                <div class="col-md-4">
                    <small class="text-muted">Academic Year</small>
                    <div>
                        {{ $feeReceipt->academicYear->name ?? '-' }}
                    </div>
                </div>


                <div class="col-md-4">
                    <small class="text-muted">Class / Section</small>

                    <div>
                        {{ $feeReceipt->enrollment->schoolClass->name ?? '-' }}

                        @if($feeReceipt->enrollment->section ?? null)
                            -
                            {{ $feeReceipt->enrollment->section->name }}
                        @endif
                    </div>
                </div>


                <div class="col-md-4">
                    <small class="text-muted">Payment Date</small>

                    <div>
                        {{ optional($feeReceipt->payment_date)->format('d-m-Y') }}
                    </div>
                </div>


                <div class="col-md-4">
                    <small class="text-muted">Payment Mode</small>

                    <div>
                        {{ $feeReceipt->paymentMode->name
                            ?? ucfirst($feeReceipt->payment_mode ?? '-') }}
                    </div>
                </div>

            </div>

        </div>

    </div>


    <div class="card shadow-sm mb-4">

        <div class="table-responsive">

            <table class="table table-bordered mb-0">

                <thead class="table-light">

                    <tr>
                        <th>Fee Component</th>
                        <th>Installment</th>
                        <th>Due Date</th>
                        <th class="text-end">Amount Paid</th>
                    </tr>

                </thead>


                <tbody>

                    @foreach($feeReceipt->items as $item)

                        <tr>

                            <td>
                                {{ $item->fee_head_name }}
                            </td>

                            <td>
                                {{ $item->installment_name }}
                            </td>

                            <td>
                                {{ optional($item->due_date)->format('d-m-Y') }}
                            </td>

                            <td class="text-end">

                                ₹{{ number_format($item->amount, 2) }}

                            </td>

                        </tr>

                    @endforeach

                </tbody>


                <tfoot>

                    <tr>

                        <th colspan="3"
                            class="text-end">

                            Total Received

                        </th>

                        <th class="text-end">

                            ₹{{ number_format($feeReceipt->total_amount, 2) }}

                        </th>

                    </tr>

                </tfoot>

            </table>

        </div>

    </div>


    @if($feeReceipt->transaction_no)

        <div class="alert alert-light border">
            <strong>Transaction:</strong>
            {{ $feeReceipt->transaction_no }}
        </div>

    @endif


    @if($feeReceipt->cheque_no)

        <div class="alert alert-light border">

            <strong>Cheque/DD:</strong>
            {{ $feeReceipt->cheque_no }}

            @if($feeReceipt->bank_name)
                | {{ $feeReceipt->bank_name }}
            @endif

        </div>

    @endif


    @if($feeReceipt->status === 'cancelled')

        <div class="alert alert-danger">

            <strong>Receipt Cancelled</strong>

            <br>

            {{ $feeReceipt->cancellation_reason }}

        </div>

    @endif


    @if($feeReceipt->status === 'posted')

        @can('fee-collection.cancel')

            <div class="card border-danger">

                <div class="card-header text-danger">
                    Cancel Receipt
                </div>

                <div class="card-body">

                    <form method="POST"
                          action="{{ route('fee-receipts.cancel', $feeReceipt) }}">

                        @csrf

                        <label class="form-label">
                            Cancellation Reason
                        </label>

                        <textarea name="cancellation_reason"
                                  class="form-control mb-3"
                                  rows="3"
                                  required></textarea>


                        <button type="submit"
                                class="btn btn-danger"
                                onclick="return confirm('Cancel this receipt and restore the student fee balances?')">

                            Cancel & Reverse Receipt

                        </button>

                    </form>

                </div>

            </div>

        @endcan

    @endif

</div>

@endsection