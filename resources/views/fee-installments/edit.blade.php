@extends('layouts.admin')

@section('title', 'Edit Fee Installment')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h4 class="mb-1">
            Edit Fee Installment
        </h4>

        <div class="text-muted">

            {{ $feeInstallment
                ->feeStructureItem
                ?->feeHead
                ?->name }}

            •

            {{ $feeInstallment
                ->feeStructure
                ?->schoolClass
                ?->name }}

        </div>

    </div>


    <a
        href="{{ route(
            'fee-installments.index',
            $feeInstallment->fee_structure_id
        ) }}"
        class="btn btn-outline-secondary"
    >
        Back
    </a>

</div>


<div class="card border-0 shadow-sm">

    <div class="card-body">

        <form
            method="POST"
            action="{{ route(
                'fee-installments.update',
                $feeInstallment
            ) }}"
        >

            @csrf
            @method('PUT')


            <div class="row g-3">

                <div class="col-md-4">

                    <label class="form-label">
                        Installment Name *
                    </label>

                    <input
                        type="text"
                        name="installment_name"
                        value="{{ old(
                            'installment_name',
                            $feeInstallment->installment_name
                        ) }}"
                        class="form-control"
                        required
                    >

                </div>


                <div class="col-md-4">

                    <label class="form-label">
                        Period Start
                    </label>

                    <input
                        type="date"
                        name="period_start"
                        value="{{ old(
                            'period_start',
                            $feeInstallment
                                ->period_start
                                ?->format('Y-m-d')
                        ) }}"
                        class="form-control"
                    >

                </div>


                <div class="col-md-4">

                    <label class="form-label">
                        Period End
                    </label>

                    <input
                        type="date"
                        name="period_end"
                        value="{{ old(
                            'period_end',
                            $feeInstallment
                                ->period_end
                                ?->format('Y-m-d')
                        ) }}"
                        class="form-control"
                    >

                </div>


                <div class="col-md-4">

                    <label class="form-label">
                        Due Date *
                    </label>

                    <input
                        type="date"
                        name="due_date"
                        value="{{ old(
                            'due_date',
                            $feeInstallment
                                ->due_date
                                ->format('Y-m-d')
                        ) }}"
                        class="form-control"
                        required
                    >

                </div>


                <div class="col-md-4">

                    <label class="form-label">
                        Amount *
                    </label>

                    <div class="input-group">

                        <span class="input-group-text">
                            ₹
                        </span>

                        <input
                            type="number"
                            name="amount"
                            step="0.01"
                            min="0"
                            value="{{ old(
                                'amount',
                                $feeInstallment->amount
                            ) }}"
                            class="form-control"
                            required
                        >

                    </div>

                </div>


                <div class="col-md-2">

                    <label class="form-label">
                        Sort Order
                    </label>

                    <input
                        type="number"
                        name="sort_order"
                        min="0"
                        value="{{ old(
                            'sort_order',
                            $feeInstallment->sort_order
                        ) }}"
                        class="form-control"
                    >

                </div>


                <div class="col-md-2">

                    <label class="form-label d-block">
                        Status
                    </label>

                    <input
                        type="hidden"
                        name="status"
                        value="0"
                    >

                    <div class="form-check form-switch mt-2">

                        <input
                            type="checkbox"
                            name="status"
                            value="1"
                            id="status"
                            class="form-check-input"
                            @checked(
                                old(
                                    'status',
                                    $feeInstallment->status
                                )
                            )
                        >

                        <label
                            for="status"
                            class="form-check-label"
                        >
                            Active
                        </label>

                    </div>

                </div>

            </div>


            <div class="mt-4">

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Update Installment
                </button>

            </div>

        </form>

    </div>

</div>

@endsection