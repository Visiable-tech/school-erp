@extends('layouts.admin')

@section('content')

<style>
    .dashboard-page {
        background: #f7f9fc;
        padding: 20px;
    }

    .dash-card {
        background: #fff;
        border: 1px solid #e8edf3;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,.03);
        height: 100%;
    }

    .dash-card-body {
        padding: 18px;
    }

    .stat-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 21px;
    }

    .icon-orange {
        background: #fff4e6;
        color: #f59e0b;
    }

    .icon-blue {
        background: #eaf4ff;
        color: #1683ff;
    }

    .icon-purple {
        background: #f6eaff;
        color: #a855f7;
    }

    .icon-green {
        background: #e8fbef;
        color: #22b573;
    }

    .icon-red {
        background: #fff0f0;
        color: #ef4444;
    }

    .stat-title {
        font-size: 13px;
        color: #667085;
        margin-bottom: 4px;
    }

    .stat-value {
        font-size: 22px;
        font-weight: 700;
        color: #101828;
        line-height: 1.2;
    }

    .circle-progress {
        width: 58px;
        height: 58px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: conic-gradient(#47c98b var(--value), #edf1f5 0);
        position: relative;
    }

    .circle-progress::before {
        content: "";
        width: 48px;
        height: 48px;
        background: #fff;
        border-radius: 50%;
        position: absolute;
    }

    .circle-progress span {
        position: relative;
        font-size: 12px;
        font-weight: 600;
        color: #344054;
    }

    .chart-header {
        padding: 15px 18px;
        border-bottom: 1px solid #edf0f4;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .chart-header h6 {
        margin: 0;
        font-weight: 600;
        color: #1d2939;
    }

    .chart-header small {
        color: #98a2b3;
    }

    .chart-container {
        height: 270px;
        padding: 15px;
    }

    .mini-chart-container {
        height: 180px;
        padding: 15px;
    }

    .progress-title {
        font-size: 12px;
        color: #344054;
    }

    .progress {
        height: 9px;
        border-radius: 10px;
        background: #eef2f6;
    }

    .progress-bar {
        border-radius: 10px;
    }

    .schedule-table {
        width: 100%;
        min-width: 900px;
        border-collapse: collapse;
    }

    .schedule-table th,
    .schedule-table td {
        border: 1px solid #edf0f4;
        text-align: center;
        padding: 12px;
        font-size: 12px;
    }

    .schedule-table th {
        font-weight: 500;
        color: #475467;
    }

    .schedule-block {
        height: 45px;
        border-radius: 5px;
        background: linear-gradient(
            90deg,
            #62d492 0 45%,
            #a5ebc3 45% 70%,
            #c9c8ff 70% 100%
        );
    }

    @media(max-width: 767px) {
        .dashboard-page {
            padding: 10px;
        }

        .chart-container {
            height: 230px;
        }
    }
</style>


<div class="dashboard-page">

    {{-- TOP STATISTICS --}}
    <div class="row g-3 mb-3">

        <div class="col-xl-3 col-md-6">
            <div class="dash-card">
                <div class="dash-card-body d-flex justify-content-between align-items-center">

                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon icon-orange">
                            <i class="bi bi-wallet2"></i>
                        </div>

                        <div>
                            <div class="stat-title">Fees Awaiting Payment</div>
                            <div class="stat-value">
                                14 <span class="fw-normal text-muted fs-6">/ 238</span>
                            </div>
                        </div>
                    </div>

                    <div class="circle-progress" style="--value:5.9%">
                        <span>5.9%</span>
                    </div>

                </div>
            </div>
        </div>


        <div class="col-xl-3 col-md-6">
            <div class="dash-card">
                <div class="dash-card-body d-flex justify-content-between align-items-center">

                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon icon-blue">
                            <i class="bi bi-bar-chart"></i>
                        </div>

                        <div>
                            <div class="stat-title">Converted Leads</div>
                            <div class="stat-value">
                                20 <span class="fw-normal text-muted fs-6">/ 100</span>
                            </div>
                        </div>
                    </div>

                    <div class="circle-progress"
                         style="--value:50.4%; background:conic-gradient(#1683ff 50.4%,#edf1f5 0)">
                        <span>50.4%</span>
                    </div>

                </div>
            </div>
        </div>


        <div class="col-xl-3 col-md-6">
            <div class="dash-card">
                <div class="dash-card-body d-flex justify-content-between align-items-center">

                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon icon-purple">
                            <i class="bi bi-people"></i>
                        </div>

                        <div>
                            <div class="stat-title">Staff Present Today</div>
                            <div class="stat-value">
                                10 <span class="fw-normal text-muted fs-6">/ 80</span>
                            </div>
                        </div>
                    </div>

                    <div class="circle-progress"
                         style="--value:42%; background:conic-gradient(#a855f7 42%,#edf1f5 0)">
                        <span>42%</span>
                    </div>

                </div>
            </div>
        </div>


        <div class="col-xl-3 col-md-6">
            <div class="dash-card">
                <div class="dash-card-body d-flex justify-content-between align-items-center">

                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon icon-green">
                            <i class="bi bi-mortarboard"></i>
                        </div>

                        <div>
                            <div class="stat-title">Students Present Today</div>
                            <div class="stat-value">
                                114 <span class="fw-normal text-muted fs-6">/ 154</span>
                            </div>
                        </div>
                    </div>

                    <div class="circle-progress">
                        <span>47.8%</span>
                    </div>

                </div>
            </div>
        </div>

    </div>


    {{-- SECOND STATISTICS --}}
    <div class="row g-3 mb-3">

        <div class="col-xl-3 col-md-6">
            <div class="dash-card">
                <div class="dash-card-body d-flex align-items-center gap-3">

                    <div class="stat-icon icon-purple">
                        <i class="bi bi-people"></i>
                    </div>

                    <div>
                        <div class="stat-title">Student Count</div>
                        <div class="stat-value">154</div>
                    </div>

                </div>
            </div>
        </div>


        <div class="col-xl-3 col-md-6">
            <div class="dash-card">
                <div class="dash-card-body d-flex align-items-center gap-3">

                    <div class="stat-icon icon-green">
                        <i class="bi bi-cash-stack"></i>
                    </div>

                    <div>
                        <div class="stat-title">Monthly Fees Collection</div>
                        <div class="stat-value">₹1,93,500</div>
                    </div>

                </div>
            </div>
        </div>


        <div class="col-xl-3 col-md-6">
            <div class="dash-card">
                <div class="dash-card-body d-flex align-items-center gap-3">

                    <div class="stat-icon icon-red">
                        <i class="bi bi-receipt"></i>
                    </div>

                    <div>
                        <div class="stat-title">Monthly Expenses</div>
                        <div class="stat-value">₹1,25,000</div>
                    </div>

                </div>
            </div>
        </div>


        <div class="col-xl-3 col-md-6">
            <div class="dash-card">
                <div class="dash-card-body d-flex align-items-center gap-3">

                    <div class="stat-icon icon-orange">
                        <i class="bi bi-clock-history"></i>
                    </div>

                    <div>
                        <div class="stat-title">Pending Dues</div>
                        <div class="stat-value">₹23,400</div>
                    </div>

                </div>
            </div>
        </div>

    </div>


    {{-- MAIN CHARTS --}}
    <div class="row g-3 mb-3">

        <div class="col-xl-6">
            <div class="dash-card">

                <div class="chart-header">
                    <div>
                        <h6>
                            <i class="bi bi-currency-rupee me-1"></i>
                            Fees Collection & Expenses
                        </h6>
                        <small>Academic Year 2026-27</small>
                    </div>

                    <i class="bi bi-calendar3"></i>
                </div>

                <div class="chart-container">
                    <canvas id="collectionChart"></canvas>
                </div>

            </div>
        </div>


        <div class="col-xl-6">
            <div class="dash-card">

                <div class="chart-header">
                    <div>
                        <h6>
                            <i class="bi bi-graph-up me-1"></i>
                            Monthly Financial Overview
                        </h6>
                        <small>Session: 2026-27</small>
                    </div>

                    <i class="bi bi-calendar3"></i>
                </div>

                <div class="chart-container">
                    <canvas id="financialChart"></canvas>
                </div>

            </div>
        </div>

    </div>


    {{-- SMALL CHARTS --}}
    <div class="row g-3 mb-3">

        <div class="col-xl-3 col-md-6">

            <div class="dash-card">

                <div class="chart-header">
                    <h6>Income</h6>
                    <small>September 2026</small>
                </div>

                <div class="mini-chart-container">
                    <canvas id="incomeChart"></canvas>
                </div>

            </div>

        </div>


        <div class="col-xl-3 col-md-6">

            <div class="dash-card">

                <div class="chart-header">
                    <h6>Expense</h6>
                    <small>September 2026</small>
                </div>

                <div class="mini-chart-container">
                    <canvas id="expenseChart"></canvas>
                </div>

            </div>

        </div>


        {{-- FEES OVERVIEW --}}
        <div class="col-xl-3 col-md-6">

            <div class="dash-card">

                <div class="chart-header">
                    <h6>Fees Overview</h6>
                    <small>September 2026</small>
                </div>

                <div class="dash-card-body">

                    <div class="mb-4">

                        <div class="d-flex justify-content-between progress-title mb-2">
                            <span>216 UNPAID</span>
                            <span>90.76%</span>
                        </div>

                        <div class="progress">
                            <div class="progress-bar bg-success"
                                 style="width:90%"></div>
                        </div>

                    </div>


                    <div class="mb-4">

                        <div class="d-flex justify-content-between progress-title mb-2">
                            <span>8 PARTIAL</span>
                            <span>65.32%</span>
                        </div>

                        <div class="progress">
                            <div class="progress-bar bg-info"
                                 style="width:65%"></div>
                        </div>

                    </div>


                    <div>

                        <div class="d-flex justify-content-between progress-title mb-2">
                            <span>14 PAID</span>
                            <span>19.12%</span>
                        </div>

                        <div class="progress">
                            <div class="progress-bar bg-warning"
                                 style="width:19%"></div>
                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- ATTENDANCE OVERVIEW --}}
        <div class="col-xl-3 col-md-6">

            <div class="dash-card">

                <div class="chart-header">
                    <h6>Student Today Attendance</h6>
                </div>

                <div class="dash-card-body">

                    <div class="mb-4">

                        <div class="d-flex justify-content-between progress-title mb-2">
                            <span>114 PRESENT</span>
                            <span>74%</span>
                        </div>

                        <div class="progress">
                            <div class="progress-bar bg-success"
                                 style="width:74%"></div>
                        </div>

                    </div>


                    <div class="mb-4">

                        <div class="d-flex justify-content-between progress-title mb-2">
                            <span>8 LATE</span>
                            <span>5%</span>
                        </div>

                        <div class="progress">
                            <div class="progress-bar bg-info"
                                 style="width:5%"></div>
                        </div>

                    </div>


                    <div>

                        <div class="d-flex justify-content-between progress-title mb-2">
                            <span>32 ABSENT</span>
                            <span>21%</span>
                        </div>

                        <div class="progress">
                            <div class="progress-bar bg-danger"
                                 style="width:21%"></div>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- WEEKLY SCHEDULE --}}
    <div class="dash-card mb-3">

        <div class="chart-header">

            <div class="d-flex align-items-center gap-3">

                <button class="btn btn-sm btn-outline-secondary">
                    Today
                </button>

                <button class="btn btn-sm btn-light">
                    <i class="bi bi-chevron-left"></i>
                </button>

                <button class="btn btn-sm btn-light">
                    <i class="bi bi-chevron-right"></i>
                </button>

                <strong>September 23, 2026</strong>

                <span class="badge text-bg-light">
                    Week 39
                </span>

            </div>

            <select class="form-select form-select-sm"
                    style="width:100px;">
                <option>7 Days</option>
            </select>

        </div>


        <div class="table-responsive">

            <table class="schedule-table">

                <thead>
                    <tr>
                        <th style="width:80px"></th>
                        <th>Mon 21</th>
                        <th>Tue 22</th>
                        <th>Wed 23</th>
                        <th>Thu 24</th>
                        <th>Fri 25</th>
                        <th>Sat 26</th>
                        <th>Sun 27</th>
                    </tr>
                </thead>

                <tbody>

                    <tr>
                        <td>All Day</td>
                        <td colspan="7"></td>
                    </tr>

                    <tr>
                        <td>9 AM</td>

                        @for($i = 0; $i < 7; $i++)
                            <td>
                                <div class="schedule-block"></div>
                            </td>
                        @endfor
                    </tr>

                    <tr>
                        <td>10 AM</td>

                        @for($i = 0; $i < 7; $i++)
                            <td>
                                <div class="schedule-block"></div>
                            </td>
                        @endfor
                    </tr>

                    <tr>
                        <td>11 AM</td>

                        @for($i = 0; $i < 7; $i++)
                            <td>
                                <div class="schedule-block"></div>
                            </td>
                        @endfor
                    </tr>

                </tbody>

            </table>

        </div>

    </div>

</div>


{{-- Chart JS --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const months = [
        'Jan','Feb','Mar','Apr','May','Jun',
        'Jul','Aug','Sep','Oct','Nov','Dec'
    ];


    // Fees Collection Bar Chart
    new Chart(
        document.getElementById('collectionChart'),
        {
            type: 'bar',

            data: {
                labels: months,

                datasets: [
                    {
                        label: 'Collection',
                        data: [
                            4100,3900,4200,4300,
                            4400,4600,4700,4500,
                            4300,4400,4200,2500
                        ],
                        backgroundColor: '#65d39a',
                        borderRadius: 5
                    },
                    {
                        label: 'Expenses',
                        data: [
                            3200,3100,3000,3300,
                            3400,3200,3500,3300,
                            3400,3200,3100,2800
                        ],
                        backgroundColor: '#ff9990',
                        borderRadius: 5
                    }
                ]
            },

            options: {
                responsive: true,
                maintainAspectRatio: false,

                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },

                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },

                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#edf0f4'
                        }
                    }
                }
            }
        }
    );


    // Financial Line Chart
    new Chart(
        document.getElementById('financialChart'),
        {
            type: 'line',

            data: {
                labels: months,

                datasets: [
                    {
                        label: 'Collection',
                        data: [
                            1.4,1.8,2.4,3.8,
                            3.1,4.4,3.6,4.5,
                            3.0,1.5,2.1,1.6
                        ],
                        borderColor: '#22b573',
                        backgroundColor: '#22b573',
                        tension: .3,
                        pointRadius: 0,
                        borderWidth: 2
                    },
                    {
                        label: 'Expenses',
                        data: [
                            1.2,1.5,1.0,2.0,
                            2.1,2.7,2.2,2.8,
                            2.1,2.5,1.6,1.8
                        ],
                        borderColor: '#ff8b83',
                        backgroundColor: '#ff8b83',
                        tension: .3,
                        pointRadius: 0,
                        borderWidth: 2
                    }
                ]
            },

            options: {
                responsive: true,
                maintainAspectRatio: false,

                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },

                scales: {
                    x: {
                        grid: {
                            color: '#f0f2f5'
                        }
                    },

                    y: {
                        grid: {
                            color: '#f0f2f5'
                        }
                    }
                }
            }
        }
    );


    // Income
    new Chart(
        document.getElementById('incomeChart'),
        {
            type: 'doughnut',

            data: {
                labels: [
                    'Donation',
                    'Rent',
                    'Miscellaneous',
                    'Uniform Sale',
                    'Book Sale'
                ],

                datasets: [{
                    data: [30,20,15,20,15],

                    backgroundColor: [
                        '#69d39a',
                        '#55b7f3',
                        '#e977e8',
                        '#24b4d8',
                        '#9a7df4'
                    ],

                    borderWidth: 0
                }]
            },

            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',

                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            boxWidth: 10,
                            font: {
                                size: 10
                            }
                        }
                    }
                }
            }
        }
    );


    // Expense
    new Chart(
        document.getElementById('expenseChart'),
        {
            type: 'doughnut',

            data: {
                labels: [
                    'Stationery',
                    'Electricity',
                    'Telephone',
                    'Miscellaneous',
                    'Other'
                ],

                datasets: [{
                    data: [25,20,20,15,20],

                    backgroundColor: [
                        '#69d39a',
                        '#728cf4',
                        '#d567e9',
                        '#ffac61',
                        '#ec78b3'
                    ],

                    borderWidth: 0
                }]
            },

            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',

                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            boxWidth: 10,
                            font: {
                                size: 10
                            }
                        }
                    }
                }
            }
        }
    );

});
</script>

@endsection