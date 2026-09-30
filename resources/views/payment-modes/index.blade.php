@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <div>
            <h4 class="mb-1">
                Payment Modes
            </h4>

            <small class="text-muted">
                Manage fee collection payment methods
            </small>
        </div>


        @can('payment-mode.create')

            <a href="{{ route(
                'payment-modes.create'
            ) }}"
               class="btn btn-primary">

                <i class="bi bi-plus-circle"></i>
                Add Payment Mode

            </a>

        @endcan

    </div>


    <div class="card border-0 shadow-sm mb-3">

        <div class="card-body">

            <form method="GET">

                <div class="row g-2">

                    <div class="col-md-5">

                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               class="form-control"
                               placeholder="Search name or code">

                    </div>


                    <div class="col-md-3">

                        <select name="mode_type"
                                class="form-select">

                            <option value="">
                                All Types
                            </option>

                            @foreach([
                                'cash' => 'Cash',
                                'cheque' => 'Cheque',
                                'dd' => 'Demand Draft',
                                'card' => 'Card',
                                'upi' => 'UPI',
                                'bank_transfer' => 'Bank Transfer',
                                'online' => 'Online Gateway',
                                'other' => 'Other',
                            ] as $key => $label)

                                <option value="{{ $key }}"
                                    {{ request('mode_type') === $key
                                        ? 'selected'
                                        : '' }}>

                                    {{ $label }}

                                </option>

                            @endforeach

                        </select>

                    </div>


                    <div class="col-md-2">

                        <select name="status"
                                class="form-select">

                            <option value="">
                                All Status
                            </option>

                            <option value="1"
                                {{ request('status') === '1'
                                    ? 'selected'
                                    : '' }}>
                                Active
                            </option>

                            <option value="0"
                                {{ request('status') === '0'
                                    ? 'selected'
                                    : '' }}>
                                Inactive
                            </option>

                        </select>

                    </div>


                    <div class="col-md-1">

                        <button class="btn btn-primary w-100">
                            <i class="bi bi-search"></i>
                        </button>

                    </div>


                    <div class="col-md-1">

                        <a href="{{ route(
                            'payment-modes.index'
                        ) }}"
                           class="btn btn-light w-100">

                            <i class="bi bi-arrow-clockwise"></i>

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
                        <th>Payment Mode</th>
                        <th>Type</th>
                        <th>Requirements</th>
                        <th>Default</th>
                        <th>Status</th>
                        <th width="120">Action</th>
                    </tr>

                </thead>

                <tbody>

                @forelse($paymentModes as $mode)

                    <tr>

                        <td>
                            {{ $paymentModes->firstItem()
                                + $loop->index }}
                        </td>


                        <td>

                            <strong>
                                {{ $mode->name }}
                            </strong>

                            @if($mode->code)
                                <div class="small text-muted">
                                    {{ $mode->code }}
                                </div>
                            @endif

                        </td>


                        <td>

                            {{ ucwords(
                                str_replace(
                                    '_',
                                    ' ',
                                    $mode->mode_type
                                )
                            ) }}

                        </td>


                        <td>

                            @if($mode->requires_reference)
                                <span class="badge bg-light text-dark border">
                                    Reference
                                </span>
                            @endif

                            @if($mode->requires_bank)
                                <span class="badge bg-light text-dark border">
                                    Bank
                                </span>
                            @endif

                            @if($mode->requires_instrument_date)
                                <span class="badge bg-light text-dark border">
                                    Date
                                </span>
                            @endif

                            @if($mode->is_online)
                                <span class="badge bg-info">
                                    Online
                                </span>
                            @endif

                            @if(
                                !$mode->requires_reference &&
                                !$mode->requires_bank &&
                                !$mode->requires_instrument_date &&
                                !$mode->is_online
                            )
                                —
                            @endif

                        </td>


                        <td>

                            @if($mode->is_default)

                                <span class="badge bg-primary">
                                    Default
                                </span>

                            @else
                                —
                            @endif

                        </td>


                        <td>

                            @if($mode->status)

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

                            <div class="d-flex gap-1">

                                @can('payment-mode.edit')

                                    <a href="{{ route(
                                        'payment-modes.edit',
                                        $mode
                                    ) }}"
                                       class="btn btn-sm btn-outline-primary">

                                        <i class="bi bi-pencil"></i>

                                    </a>

                                @endcan


                                @can('payment-mode.delete')

                                    <form method="POST"
                                          action="{{ route(
                                              'payment-modes.destroy',
                                              $mode
                                          ) }}"
                                          onsubmit="return confirm(
                                              'Delete this payment mode?'
                                          );">

                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="btn btn-sm btn-outline-danger">

                                            <i class="bi bi-trash"></i>

                                        </button>

                                    </form>

                                @endcan

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="7"
                            class="text-center text-muted py-5">

                            No Payment Modes found.

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>


        @if($paymentModes->hasPages())

            <div class="card-footer bg-white">
                {{ $paymentModes->links() }}
            </div>

        @endif

    </div>

</div>

@endsection