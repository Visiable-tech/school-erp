@extends('layouts.admin')

@section('title', 'Fee Schedule')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h4 class="mb-1">
            Fee Schedule
        </h4>

        <div class="text-muted">

            {{ $feeStructure->name }}

            •

            {{ $feeStructure->academicYear?->name }}

            •

            {{ $feeStructure->schoolClass?->name }}

        </div>

    </div>


    <a
        href="{{ route('fee-structures.index') }}"
        class="btn btn-outline-secondary"
    >
        <i class="bi bi-arrow-left me-1"></i>
        Fee Structures
    </a>

</div>


@foreach($feeStructure->items as $item)

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white py-3">

            <div class="d-flex justify-content-between align-items-center">

                <div>

                    <strong>
                        {{ $item->feeHead?->name }}
                    </strong>

                    <span class="badge bg-secondary ms-2">

                        {{ ucwords(
                            str_replace(
                                '_',
                                ' ',
                                $item->feeHead?->frequency
                            )
                        ) }}

                    </span>


                    <span class="ms-2 text-muted">

                        Base Amount:

                        ₹{{ number_format(
                            $item->amount,
                            2
                        ) }}

                    </span>

                </div>


                <div class="d-flex gap-2">

                    @can('fee-installment.create')

                        <a
                            href="{{ route(
                                'fee-installments.create',
                                [
                                    $feeStructure,
                                    $item
                                ]
                            ) }}"
                            class="btn btn-sm btn-outline-primary"
                        >
                            <i class="bi bi-plus-circle me-1"></i>
                            Add Manually
                        </a>

                    @endcan

                </div>

            </div>

        </div>


        <div class="card-body">


            {{-- AUTO GENERATE --}}

            @if(
                $item->installments->isEmpty()
                &&
                auth()->user()->can(
                    'fee-installment.create'
                )
            )

                <div class="border rounded p-3 mb-3">

                    <form
                        method="POST"
                        action="{{ route(
                            'fee-installments.generate',
                            [
                                $feeStructure,
                                $item
                            ]
                        ) }}"
                    >

                        @csrf


                        <div class="row align-items-end g-2">

                            <div class="col-md-3">

                                <label class="form-label">
                                    Due Day
                                </label>

                                <input
                                    type="number"
                                    name="due_day"
                                    min="1"
                                    max="28"
                                    value="10"
                                    class="form-control"
                                    required
                                >

                                <small class="text-muted">
                                    Example: 10 = 10th of month
                                </small>

                            </div>


                            <div class="col-md-5">

                                <button
                                    type="submit"
                                    class="btn btn-success"
                                    onclick="
                                        return confirm(
                                            'Generate fee schedule?'
                                        );
                                    "
                                >
                                    <i class="bi bi-calendar-plus me-1"></i>
                                    Auto Generate Schedule
                                </button>

                            </div>

                        </div>

                    </form>

                </div>

            @endif


            {{-- INSTALLMENTS --}}

            <div class="table-responsive">

                <table class="table table-bordered align-middle mb-0">

                    <thead class="table-light">

                        <tr>
                            <th width="80">#</th>
                            <th>Installment</th>
                            <th>Period</th>
                            <th>Due Date</th>
                            <th class="text-end">Amount</th>
                            <th>Status</th>
                            <th width="120">Action</th>
                        </tr>

                    </thead>


                    <tbody>

                        @forelse(
                            $item->installments
                            as $installment
                        )

                            <tr>

                                <td>
                                    {{ $loop->iteration }}
                                </td>


                                <td class="fw-semibold">

                                    {{ $installment->installment_name }}

                                </td>


                                <td>

                                    @if(
                                        $installment->period_start
                                        &&
                                        $installment->period_end
                                    )

                                        {{ $installment
                                            ->period_start
                                            ->format('d M Y') }}

                                        -

                                        {{ $installment
                                            ->period_end
                                            ->format('d M Y') }}

                                    @else

                                        -

                                    @endif

                                </td>


                                <td>

                                    {{ $installment
                                        ->due_date
                                        ->format('d M Y') }}

                                </td>


                                <td class="text-end fw-semibold">

                                    ₹{{ number_format(
                                        $installment->amount,
                                        2
                                    ) }}

                                </td>


                                <td>

                                    @if($installment->status)

                                        <span class="badge bg-success">
                                            Active
                                        </span>

                                    @else

                                        <span class="badge bg-danger">
                                            Inactive
                                        </span>

                                    @endif

                                </td>


                                <td>

                                    <div class="d-flex gap-1">

                                        @can('fee-installment.edit')

                                            <a
                                                href="{{ route(
                                                    'fee-installments.edit',
                                                    $installment
                                                ) }}"
                                                class="btn btn-sm btn-outline-primary"
                                            >
                                                <i class="bi bi-pencil"></i>
                                            </a>

                                        @endcan


                                        @can('fee-installment.delete')

                                            <form
                                                method="POST"
                                                action="{{ route(
                                                    'fee-installments.destroy',
                                                    $installment
                                                ) }}"
                                                onsubmit="
                                                    return confirm(
                                                        'Delete this installment?'
                                                    );
                                                "
                                            >

                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    class="btn btn-sm btn-outline-danger"
                                                >
                                                    <i class="bi bi-trash"></i>
                                                </button>

                                            </form>

                                        @endcan

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="7"
                                    class="text-center text-muted py-4"
                                >
                                    No installments configured.
                                </td>

                            </tr>

                        @endforelse

                    </tbody>


                    @if($item->installments->isNotEmpty())

                        <tfoot>

                            <tr>

                                <th colspan="4" class="text-end">
                                    Total
                                </th>

                                <th class="text-end">

                                    ₹{{ number_format(
                                        $item
                                            ->installments
                                            ->sum('amount'),
                                        2
                                    ) }}

                                </th>

                                <th colspan="2"></th>

                            </tr>

                        </tfoot>

                    @endif

                </table>

            </div>

        </div>

    </div>

@endforeach

@endsection