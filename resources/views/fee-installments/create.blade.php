@extends('layouts.admin')

@section('title', 'Add Fee Installment')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h4 class="mb-1">
            Add Fee Installment
        </h4>

        <div class="text-muted">

            {{ $feeStructureItem->feeHead?->name }}

            •

            {{ $feeStructure->schoolClass?->name }}

            •

            {{ $feeStructure->academicYear?->name }}

        </div>

    </div>


    <a
        href="{{ route(
            'fee-installments.index',
            $feeStructure
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
                'fee-installments.store',
                [
                    $feeStructure,
                    $feeStructureItem
                ]
            ) }}"
        >

            @csrf


            <div class="row g-3">

                <div class="col-md-4">

                    <label class="form-label">
                        Installment Name *
                    </label>

                    <input
                        type="text"
                        name="installment_name"
                        value="{{ old('installment_name') }}"
                        class="form-control @error('installment_name') is-invalid @enderror"
                        placeholder="Example: Apr 2026"
                        required
                    >

                    @error('installment_name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                <div class="col-md-4">

                    <label class="form-label">
                        Period Start
                    </label>

                    <input
                        type="date"
                        name="period_start"
                        value="{{ old('period_start') }}"
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
                        value="{{ old('period_end') }}"
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
                        value="{{ old('due_date') }}"
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
                                $feeStructureItem->amount
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
                        value="{{ old('sort_order', 0) }}"
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
                            checked
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
                    Save Installment
                </button>

            </div>

        </form>

    </div>

</div>

@endsection