@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between
                align-items-center mb-3">

        <div>
            <h4 class="mb-1">
                Fee Dashboard
            </h4>

            <small class="text-muted">
                Fee collection, dues and payment overview
            </small>
        </div>

    </div>


    {{-- Filter --}}
    <div class="card border-0 shadow-sm mb-3">

        <div class="card-body">

            <form method="GET">

                <div class="row g-3 align-items-end">

                    <div class="col-md-3">

                        <label class="form-label">
                            Academic Year
                        </label>

                        <select name="academic_year_id"
                                class="form-select">

                            @foreach($academicYears as $year)

                                <option
                                    value="{{ $year->id }}"

                                    {{ $academicYearId == $year->id
                                        ? 'selected'
                                        : '' }}
                                >
                                    {{ $year->name }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    <div class="col-md-2">

                        <button class="btn btn-primary w-100">

                            <i class="bi bi-funnel"></i>
                            Apply

                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>


    {{-- Summary Cards --}}
    <div class="row g-3 mb-3">

        <div class="col-xl-2 col-md-4">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <small class="text-muted">
                        Actual Fee
                    </small>

                    <h5 class="mb-0 mt-1">
                        ₹{{ number_format(
                            $summary['actual'],
                            2
                        ) }}
                    </h5>

                </div>

            </div>

        </div>


        <div class="col-xl-2 col-md-4">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <small class="text-muted">
                        Concession
                    </small>

                    <h5 class="mb-0 mt-1 text-warning">

                        ₹{{ number_format(
                            $summary['discount'],
                            2
                        ) }}

                    </h5>

                </div>

            </div>

        </div>


        <div class="col-xl-2 col-md-4">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <small class="text-muted">
                        Payable
                    </small>

                    <h5 class="mb-0 mt-1">

                        ₹{{ number_format(
                            $summary['payable'],
                            2
                        ) }}

                    </h5>

                </div>

            </div>

        </div>


        <div class="col-xl-2 col-md-4">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <small class="text-muted">
                        Paid
                    </small>

                    <h5 class="mb-0 mt-1 text-success">

                        ₹{{ number_format(
                            $summary['paid'],
                            2
                        ) }}

                    </h5>

                </div>

            </div>

        </div>


        <div class="col-xl-2 col-md-4">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <small class="text-muted">
                        Pending
                    </small>

                    <h5 class="mb-0 mt-1 text-danger">

                        ₹{{ number_format(
                            $summary['pending'],
                            2
                        ) }}

                    </h5>

                </div>

            </div>

        </div>


        <div class="col-xl-2 col-md-4">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <small class="text-muted">
                        Today's Collection
                    </small>

                    <h5 class="mb-0 mt-1 text-primary">

                        ₹{{ number_format(
                            $todayCollection,
                            2
                        ) }}

                    </h5>

                    <small class="text-muted">
                        {{ $todayReceipts }} receipts
                    </small>

                </div>

            </div>

        </div>

    </div>


    {{-- Class Wise Chart --}}
    <div class="card border-0 shadow-sm mb-3">

        <div class="card-header bg-white">

            <strong>
                Class Wise Fee Summary
            </strong>

        </div>

        <div class="card-body">

            <div style="height:360px;">

                <canvas id="classWiseChart"></canvas>

            </div>

        </div>

    </div>


    <div class="row g-3 mb-3">

        {{-- Collection Summary --}}
        <div class="col-lg-6">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-header bg-white">

                    <strong>
                        Today's Collection Summary
                    </strong>

                </div>

                <div class="card-body">

                    <div style="height:300px;">

                        <canvas id="paymentModeChart"></canvas>

                    </div>

                </div>

            </div>

        </div>


        {{-- Paid Unpaid --}}
        <div class="col-lg-6">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-header bg-white">

                    <strong>
                        Paid / Unpaid Summary
                    </strong>

                </div>

                <div class="card-body">

                    <div style="height:300px;">

                        <canvas id="paidUnpaidChart"></canvas>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- Component Summary --}}
    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white">

            <strong>
                Component Wise Fee Summary
            </strong>

        </div>


        <div class="table-responsive">

            <table class="table table-hover
                          align-middle mb-0">

                <thead class="table-light">

                    <tr>

                        <th>
                            Component Name
                        </th>

                        <th class="text-end">
                            Actual Amount
                        </th>

                        <th class="text-end">
                            Concession
                        </th>

                        <th class="text-end">
                            Paid
                        </th>

                        <th class="text-end">
                            Balance
                        </th>

                    </tr>

                </thead>


                <tbody>

                @forelse(
                    $componentSummary
                    as $component
                )

                    <tr>

                        <td>
                            {{ $component->fee_head_name }}
                        </td>

                        <td class="text-end">

                            ₹{{ number_format(
                                $component->actual_amount,
                                2
                            ) }}

                        </td>

                        <td class="text-end">

                            ₹{{ number_format(
                                $component->concession,
                                2
                            ) }}

                        </td>

                        <td class="text-end text-success">

                            ₹{{ number_format(
                                $component->paid,
                                2
                            ) }}

                        </td>

                        <td class="text-end text-danger">

                            ₹{{ number_format(
                                $component->balance,
                                2
                            ) }}

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="5"
                            class="text-center
                                   text-muted py-4">

                            No fee data available.

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        /*
         * Class Wise Chart
         */
        const classWise =
            @json($classWise);


        new Chart(
            document.getElementById(
                'classWiseChart'
            ),
            {
                type: 'bar',

                data: {

                    labels:
                        classWise.map(
                            row => row.class
                        ),

                    datasets: [

                        {
                            label: 'Actual',

                            data:
                                classWise.map(
                                    row => row.actual
                                )
                        },

                        {
                            label: 'Adjustment',

                            data:
                                classWise.map(
                                    row => row.adjustment
                                )
                        },

                        {
                            label: 'Paid',

                            data:
                                classWise.map(
                                    row => row.paid
                                )
                        },

                        {
                            label: 'Pending',

                            data:
                                classWise.map(
                                    row => row.pending
                                )
                        }

                    ]
                },

                options: {

                    responsive: true,

                    maintainAspectRatio: false,

                    scales: {

                        y: {
                            beginAtZero: true
                        }

                    }

                }
            }
        );


        /*
         * Payment Modes
         */
        const paymentModes =
            @json($paymentModes);


        new Chart(
            document.getElementById(
                'paymentModeChart'
            ),
            {
                type: 'doughnut',

                data: {

                    labels:
                        Object.keys(
                            paymentModes
                        ),

                    datasets: [{
                        data:
                            Object.values(
                                paymentModes
                            )
                    }]
                },

                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            }
        );


        /*
         * Paid / Unpaid
         */
        new Chart(
            document.getElementById(
                'paidUnpaidChart'
            ),
            {
                type: 'pie',

                data: {

                    labels: [
                        'Paid',
                        'Pending'
                    ],

                    datasets: [{
                        data: [
                            {{ (float) $summary['paid'] }},
                            {{ (float) $summary['pending'] }}
                        ]
                    }]
                },

                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            }
        );

    }
);

</script>

@endsection