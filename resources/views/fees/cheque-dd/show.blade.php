@extends('layouts.admin')

@section('title', 'Cheque / DD Details')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h4 class="mb-1">
                Cheque / DD Details
            </h4>

            <small class="text-muted">

                {{ strtoupper($chequeDdDetail->instrument_type) }}

                #

                {{ $chequeDdDetail->instrument_no }}

            </small>

        </div>


        <div class="d-flex gap-2">

            <a href="{{ route('cheque-dd-details.index') }}"
               class="btn btn-outline-secondary">

                <i class="bi bi-arrow-left"></i>
                Back

            </a>


            @if(in_array(
                $chequeDdDetail->clearance_status,
                ['pending', 'deposited']
            ))

                @can('cheque-dd.edit')

                    <a href="{{ route('cheque-dd-details.edit', $chequeDdDetail) }}"
                       class="btn btn-outline-primary">

                        <i class="bi bi-pencil"></i>
                        Edit

                    </a>

                @endcan

            @endif

        </div>

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


    @if($errors->any())

        <div class="alert alert-danger">

            <ul class="mb-0">

                @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- STATUS --}}
    <div class="card shadow-sm mb-4">

        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center">

                <div>

                    <small class="text-muted">
                        Current Status
                    </small>

                    <div class="mt-1">

                        @switch($chequeDdDetail->clearance_status)

                            @case('pending')

                                <span class="badge bg-warning text-dark fs-6">
                                    Pending
                                </span>

                                @break


                            @case('deposited')

                                <span class="badge bg-info text-dark fs-6">
                                    Deposited
                                </span>

                                @break


                            @case('cleared')

                                <span class="badge bg-success fs-6">
                                    Cleared
                                </span>

                                @break


                            @case('bounced')

                                <span class="badge bg-danger fs-6">
                                    Bounced
                                </span>

                                @break


                            @case('cancelled')

                                <span class="badge bg-secondary fs-6">
                                    Cancelled
                                </span>

                                @break

                        @endswitch

                    </div>

                </div>


                <div class="text-end">

                    <small class="text-muted">
                        Amount
                    </small>

                    <div class="fs-4 fw-bold">

                        ₹{{ number_format(
                            $chequeDdDetail->amount,
                            2
                        ) }}

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- RECEIPT + STUDENT --}}
    <div class="card shadow-sm mb-4">

        <div class="card-header bg-white">

            <strong>
                Receipt & Student Information
            </strong>

        </div>


        <div class="card-body">

            <div class="row g-4">

                <div class="col-md-3">

                    <small class="text-muted">
                        Receipt No.
                    </small>

                    <div class="fw-semibold">

                        {{ $chequeDdDetail->feeCollection->receipt_no ?? '-' }}

                    </div>

                </div>


                <div class="col-md-3">

                    <small class="text-muted">
                        Receipt Date
                    </small>

                    <div>

                        {{ optional(
                            $chequeDdDetail->feeCollection->payment_date ?? null
                        )->format('d-m-Y') }}

                    </div>

                </div>


                <div class="col-md-3">

                    <small class="text-muted">
                        Student
                    </small>

                    <div>

                        {{ $chequeDdDetail->feeCollection->student->student_name
                            ?? $chequeDdDetail->feeCollection->student->name
                            ?? '-' }}

                    </div>

                </div>


                <div class="col-md-3">

                    <small class="text-muted">
                        Admission No.
                    </small>

                    <div>

                        {{ $chequeDdDetail->feeCollection->student->admission_no ?? '-' }}

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- INSTRUMENT --}}
    <div class="card shadow-sm mb-4">

        <div class="card-header bg-white">

            <strong>
                Instrument Information
            </strong>

        </div>


        <div class="card-body">

            <div class="row g-4">

                <div class="col-md-3">

                    <small class="text-muted">
                        Type
                    </small>

                    <div class="fw-semibold text-uppercase">

                        {{ $chequeDdDetail->instrument_type }}

                    </div>

                </div>


                <div class="col-md-3">

                    <small class="text-muted">
                        Instrument No.
                    </small>

                    <div class="fw-semibold">

                        {{ $chequeDdDetail->instrument_no }}

                    </div>

                </div>


                <div class="col-md-3">

                    <small class="text-muted">
                        Instrument Date
                    </small>

                    <div>

                        {{ optional(
                            $chequeDdDetail->instrument_date
                        )->format('d-m-Y') }}

                    </div>

                </div>


                <div class="col-md-3">

                    <small class="text-muted">
                        Bank
                    </small>

                    <div>

                        {{ $chequeDdDetail->bank->bank_name
                            ?? $chequeDdDetail->bank_name
                            ?? '-' }}

                    </div>

                </div>


                <div class="col-md-3">

                    <small class="text-muted">
                        Branch
                    </small>

                    <div>

                        {{ $chequeDdDetail->branch_name ?? '-' }}

                    </div>

                </div>


                <div class="col-md-3">

                    <small class="text-muted">
                        Deposit Account
                    </small>

                    <div>

                        {{ $chequeDdDetail->schoolAccount->account_name ?? '-' }}

                    </div>

                </div>


                <div class="col-md-3">

                    <small class="text-muted">
                        Deposit Date
                    </small>

                    <div>

                        {{ optional(
                            $chequeDdDetail->deposit_date
                        )->format('d-m-Y') ?? '-' }}

                    </div>

                </div>


                <div class="col-md-3">

                    <small class="text-muted">
                        Clearance Date
                    </small>

                    <div>

                        {{ optional(
                            $chequeDdDetail->clearance_date
                        )->format('d-m-Y') ?? '-' }}

                    </div>

                </div>

            </div>


            @if($chequeDdDetail->remarks)

                <hr>

                <strong>Remarks:</strong>

                <div class="mt-1">

                    {{ $chequeDdDetail->remarks }}

                </div>

            @endif

        </div>

    </div>


    {{-- BOUNCE DETAILS --}}
    @if($chequeDdDetail->clearance_status === 'bounced')

        <div class="card border-danger mb-4">

            <div class="card-header bg-danger text-white">

                <strong>
                    Bounced Instrument
                </strong>

            </div>


            <div class="card-body">

                <div class="row g-3">

                    <div class="col-md-4">

                        <strong>
                            Bounce Date
                        </strong>

                        <div>

                            {{ optional(
                                $chequeDdDetail->bounce_date
                            )->format('d-m-Y') }}

                        </div>

                    </div>


                    <div class="col-md-4">

                        <strong>
                            Reason
                        </strong>

                        <div>

                            {{ $chequeDdDetail->bounceReason->name ?? '-' }}

                        </div>

                    </div>


                    <div class="col-md-4">

                        <strong>
                            Bounce Charge
                        </strong>

                        <div>

                            @if(
                                $chequeDdDetail->bounceReason
                                &&
                                $chequeDdDetail->bounceReason->apply_charge
                            )

                                ₹{{ number_format(
                                    $chequeDdDetail->bounceReason->bounce_charge,
                                    2
                                ) }}

                                <small class="text-muted">
                                    (configured)
                                </small>

                            @else

                                No Charge

                            @endif

                        </div>

                    </div>

                </div>


                @if($chequeDdDetail->bounce_remarks)

                    <hr>

                    {{ $chequeDdDetail->bounce_remarks }}

                @endif

            </div>

        </div>

    @endif


    {{-- ACTIONS --}}
    @if($chequeDdDetail->clearance_status === 'pending')

        <div class="card shadow-sm mb-4">

            <div class="card-header bg-white">

                <strong>
                    Pending Instrument Actions
                </strong>

            </div>


            <div class="card-body">

                <div class="row g-4">


                    @can('cheque-dd.deposit')

                        <div class="col-md-6">

                            <div class="border rounded p-3 h-100">

                                <h6>
                                    Mark Deposited
                                </h6>


                                <form method="POST"
                                      action="{{ route(
                                          'cheque-dd-details.deposit',
                                          $chequeDdDetail
                                      ) }}">

                                    @csrf


                                    <div class="mb-3">

                                        <label class="form-label">
                                            Deposit Date
                                        </label>

                                        <input type="date"
                                               name="deposit_date"
                                               value="{{ date('Y-m-d') }}"
                                               class="form-control"
                                               required>

                                    </div>


                                    <div class="mb-3">

                                        <label class="form-label">
                                            School Account
                                        </label>

                                        <select name="school_account_id"
                                                class="form-select"
                                                required>

                                            <option value="">
                                                Select Account
                                            </option>

                                            @foreach(
                                                \App\Models\SchoolAccount::where(
                                                    'school_id',
                                                    auth()->user()->school_id
                                                )
                                                ->where('status', 1)
                                                ->orderBy('sort_order')
                                                ->get()
                                                as $account
                                            )

                                                <option value="{{ $account->id }}">

                                                    {{ $account->account_name }}

                                                    @if($account->account_number)

                                                        -
                                                        {{ $account->account_number }}

                                                    @endif

                                                </option>

                                            @endforeach

                                        </select>

                                    </div>


                                    <button class="btn btn-info">

                                        <i class="bi bi-bank"></i>
                                        Mark Deposited

                                    </button>

                                </form>

                            </div>

                        </div>

                    @endcan


                    @can('cheque-dd.cancel')

                        <div class="col-md-6">

                            <div class="border border-danger rounded p-3 h-100">

                                <h6 class="text-danger">
                                    Cancel Tracking
                                </h6>


                                <form method="POST"
                                      action="{{ route(
                                          'cheque-dd-details.cancel',
                                          $chequeDdDetail
                                      ) }}">

                                    @csrf


                                    <div class="mb-3">

                                        <label class="form-label">
                                            Reason
                                        </label>

                                        <textarea name="remarks"
                                                  class="form-control"
                                                  rows="3"
                                                  required></textarea>

                                    </div>


                                    <button class="btn btn-outline-danger"
                                            onclick="return confirm(
                                                'Cancel this Cheque/DD tracking record?'
                                            )">

                                        Cancel Tracking

                                    </button>

                                </form>

                            </div>

                        </div>

                    @endcan

                </div>

            </div>

        </div>

    @endif


    {{-- DEPOSITED ACTIONS --}}
    @if($chequeDdDetail->clearance_status === 'deposited')

        <div class="card shadow-sm mb-4">

            <div class="card-header bg-white">

                <strong>
                    Clearance Actions
                </strong>

            </div>


            <div class="card-body">

                <div class="row g-4">


                    @can('cheque-dd.clear')

                        <div class="col-md-6">

                            <div class="border border-success rounded p-3 h-100">

                                <h6 class="text-success">
                                    Mark Cleared
                                </h6>


                                <form method="POST"
                                      action="{{ route(
                                          'cheque-dd-details.clear',
                                          $chequeDdDetail
                                      ) }}">

                                    @csrf


                                    <label class="form-label">
                                        Clearance Date
                                    </label>


                                    <input type="date"
                                           name="clearance_date"
                                           value="{{ date('Y-m-d') }}"
                                           class="form-control mb-3"
                                           required>


                                    <button class="btn btn-success"
                                            onclick="return confirm(
                                                'Confirm that this instrument has cleared?'
                                            )">

                                        <i class="bi bi-check-circle"></i>

                                        Mark Cleared

                                    </button>

                                </form>

                            </div>

                        </div>

                    @endcan


                    @can('cheque-dd.bounce')

                        <div class="col-md-6">

                            <div class="border border-danger rounded p-3 h-100">

                                <h6 class="text-danger">
                                    Mark Bounced
                                </h6>


                                <form method="POST"
                                      action="{{ route(
                                          'cheque-dd-details.bounce',
                                          $chequeDdDetail
                                      ) }}">

                                    @csrf


                                    <div class="mb-3">

                                        <label class="form-label">
                                            Bounce Date
                                        </label>

                                        <input type="date"
                                               name="bounce_date"
                                               value="{{ date('Y-m-d') }}"
                                               class="form-control"
                                               required>

                                    </div>


                                    <div class="mb-3">

                                        <label class="form-label">
                                            Bounce Reason
                                        </label>

                                        <select name="cheque_bounce_reason_id"
                                                class="form-select"
                                                required>

                                            <option value="">
                                                Select Reason
                                            </option>

                                            @foreach(
                                                \App\Models\ChequeBounceReason::where(
                                                    'school_id',
                                                    auth()->user()->school_id
                                                )
                                                ->where('status', 1)
                                                ->orderBy('sort_order')
                                                ->get()
                                                as $reason
                                            )

                                                <option value="{{ $reason->id }}">

                                                    {{ $reason->name }}

                                                    @if($reason->apply_charge)

                                                        -
                                                        Charge ₹{{ number_format(
                                                            $reason->bounce_charge,
                                                            2
                                                        ) }}

                                                    @endif

                                                </option>

                                            @endforeach

                                        </select>

                                    </div>


                                    <div class="mb-3">

                                        <label class="form-label">
                                            Remarks
                                        </label>

                                        <textarea name="bounce_remarks"
                                                  class="form-control"
                                                  rows="2"></textarea>

                                    </div>


                                    <div class="alert alert-warning small">

                                        Marking this instrument as bounced
                                        will reverse the payment from the
                                        student's fee dues.

                                    </div>


                                    <button class="btn btn-danger"
                                            onclick="return confirm(
                                                'Mark this instrument as bounced and reverse the fee payment?'
                                            )">

                                        <i class="bi bi-x-circle"></i>

                                        Mark Bounced

                                    </button>

                                </form>

                            </div>

                        </div>

                    @endcan

                </div>

            </div>

        </div>

    @endif

</div>

@endsection