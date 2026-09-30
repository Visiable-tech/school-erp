@extends('layouts.admin')

@section('title', 'Cheque / DD Details')

@section('content')

<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h4 class="mb-1">
                Cheque / DD Details
            </h4>

            <small class="text-muted">
                Track cheque and demand draft payments, deposits,
                clearances and bounced instruments.
            </small>
        </div>

        @can('cheque-dd.create')

            <a href="{{ route('cheque-dd-details.create') }}"
               class="btn btn-primary">

                <i class="bi bi-plus-circle me-1"></i>
                Add Cheque / DD

            </a>

        @endcan

    </div>


    {{-- SUCCESS --}}
    @if(session('success'))

        <div class="alert alert-success alert-dismissible fade show">

            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>

        </div>

    @endif


    {{-- ERROR --}}
    @if(session('error'))

        <div class="alert alert-danger alert-dismissible fade show">

            {{ session('error') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>

        </div>

    @endif


    {{-- FILTER --}}
    <div class="card shadow-sm mb-4">

        <div class="card-header bg-white">

            <strong>
                <i class="bi bi-funnel me-1"></i>
                Filter
            </strong>

        </div>

        <div class="card-body">

            <form method="GET"
                  action="{{ route('cheque-dd-details.index') }}">

                <div class="row g-3">


                    <div class="col-md-2">

                        <label class="form-label">
                            Type
                        </label>

                        <select name="instrument_type"
                                class="form-select">

                            <option value="">
                                All
                            </option>

                            <option value="cheque"
                                {{ request('instrument_type') == 'cheque' ? 'selected' : '' }}>

                                Cheque

                            </option>

                            <option value="dd"
                                {{ request('instrument_type') == 'dd' ? 'selected' : '' }}>

                                Demand Draft

                            </option>

                        </select>

                    </div>


                    <div class="col-md-2">

                        <label class="form-label">
                            Status
                        </label>

                        <select name="clearance_status"
                                class="form-select">

                            <option value="">
                                All
                            </option>

                            @foreach([
                                'pending'   => 'Pending',
                                'deposited' => 'Deposited',
                                'cleared'   => 'Cleared',
                                'bounced'   => 'Bounced',
                                'cancelled' => 'Cancelled'
                            ] as $key => $label)

                                <option value="{{ $key }}"
                                    {{ request('clearance_status') == $key ? 'selected' : '' }}>

                                    {{ $label }}

                                </option>

                            @endforeach

                        </select>

                    </div>


                    <div class="col-md-2">

                        <label class="form-label">
                            Instrument No.
                        </label>

                        <input type="text"
                               name="instrument_no"
                               class="form-control"
                               value="{{ request('instrument_no') }}"
                               placeholder="Cheque / DD No.">

                    </div>


                    <div class="col-md-2">

                        <label class="form-label">
                            Receipt No.
                        </label>

                        <input type="text"
                               name="receipt_no"
                               class="form-control"
                               value="{{ request('receipt_no') }}"
                               placeholder="Receipt No.">

                    </div>


                    <div class="col-md-2">

                        <label class="form-label">
                            Student
                        </label>

                        <input type="text"
                               name="student"
                               class="form-control"
                               value="{{ request('student') }}"
                               placeholder="Name / Admission No.">

                    </div>


                    <div class="col-md-2">

                        <label class="form-label">
                            From Date
                        </label>

                        <input type="date"
                               name="from_date"
                               class="form-control"
                               value="{{ request('from_date') }}">

                    </div>


                    <div class="col-md-2">

                        <label class="form-label">
                            To Date
                        </label>

                        <input type="date"
                               name="to_date"
                               class="form-control"
                               value="{{ request('to_date') }}">

                    </div>


                    <div class="col-md-4 d-flex align-items-end gap-2">

                        <button type="submit"
                                class="btn btn-primary">

                            <i class="bi bi-search"></i>
                            Search

                        </button>

                        <a href="{{ route('cheque-dd-details.index') }}"
                           class="btn btn-light">

                            Reset

                        </a>

                    </div>

                </div>

            </form>

        </div>

    </div>


    {{-- TABLE --}}
    <div class="card shadow-sm">

        <div class="card-header bg-white">

            <strong>
                Cheque / DD Records
            </strong>

        </div>


        <div class="table-responsive">

            <table class="table table-bordered table-hover align-middle mb-0">

                <thead class="table-light">

                    <tr>

                        <th>#</th>

                        <th>
                            Receipt
                        </th>

                        <th>
                            Student
                        </th>

                        <th>
                            Type
                        </th>

                        <th>
                            Instrument No.
                        </th>

                        <th>
                            Instrument Date
                        </th>

                        <th>
                            Bank
                        </th>

                        <th class="text-end">
                            Amount
                        </th>

                        <th>
                            Status
                        </th>

                        <th width="130">
                            Action
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($details as $detail)

                        <tr>

                            <td>
                                {{ $details->firstItem() + $loop->index }}
                            </td>


                            <td>

                                <strong>
                                    {{ $detail->feeCollection->receipt_no ?? '-' }}
                                </strong>

                                <br>

                                <small class="text-muted">

                                    {{ optional(
                                        $detail->feeCollection->payment_date ?? null
                                    )->format('d-m-Y') }}

                                </small>

                            </td>


                            <td>

                                {{ $detail->feeCollection->student->student_name
                                    ?? $detail->feeCollection->student->name
                                    ?? '-' }}

                                <br>

                                <small class="text-muted">

                                    {{ $detail->feeCollection->student->admission_no ?? '' }}

                                </small>

                            </td>


                            <td>

                                @if($detail->instrument_type === 'cheque')

                                    <span class="badge bg-primary">
                                        CHEQUE
                                    </span>

                                @else

                                    <span class="badge bg-info text-dark">
                                        DD
                                    </span>

                                @endif

                            </td>


                            <td>

                                <strong>
                                    {{ $detail->instrument_no }}
                                </strong>

                            </td>


                            <td>

                                {{ optional($detail->instrument_date)->format('d-m-Y') }}

                            </td>


                            <td>

                                {{ $detail->bank->bank_name
                                    ?? $detail->bank_name
                                    ?? '-' }}

                            </td>


                            <td class="text-end fw-semibold">

                                ₹{{ number_format($detail->amount, 2) }}

                            </td>


                            <td>

                                @switch($detail->clearance_status)

                                    @case('pending')

                                        <span class="badge bg-warning text-dark">
                                            Pending
                                        </span>

                                        @break


                                    @case('deposited')

                                        <span class="badge bg-info text-dark">
                                            Deposited
                                        </span>

                                        @break


                                    @case('cleared')

                                        <span class="badge bg-success">
                                            Cleared
                                        </span>

                                        @break


                                    @case('bounced')

                                        <span class="badge bg-danger">
                                            Bounced
                                        </span>

                                        @break


                                    @case('cancelled')

                                        <span class="badge bg-secondary">
                                            Cancelled
                                        </span>

                                        @break

                                @endswitch

                            </td>


                            <td>

                                <div class="btn-group">

                                    <a href="{{ route('cheque-dd-details.show', $detail) }}"
                                       class="btn btn-sm btn-outline-primary"
                                       title="View">

                                        <i class="bi bi-eye"></i>

                                    </a>


                                    @if(in_array(
                                        $detail->clearance_status,
                                        ['pending', 'deposited']
                                    ))

                                        @can('cheque-dd.edit')

                                            <a href="{{ route('cheque-dd-details.edit', $detail) }}"
                                               class="btn btn-sm btn-outline-secondary"
                                               title="Edit">

                                                <i class="bi bi-pencil"></i>

                                            </a>

                                        @endcan

                                    @endif

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="10"
                                class="text-center text-muted py-5">

                                <i class="bi bi-inbox fs-3 d-block mb-2"></i>

                                No Cheque/DD records found.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        @if($details->hasPages())

            <div class="card-footer bg-white">

                {{ $details->links() }}

            </div>

        @endif

    </div>

</div>

@endsection