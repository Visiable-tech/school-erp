@extends('layouts.admin')


@section('title', 'Dashboard')


@section('content')

<div class="mb-4">

    <h4 class="mb-1">
        Dashboard
    </h4>

    <div class="text-muted">
        Welcome back, {{ auth()->user()->name }}
    </div>

</div>


<div class="row g-3">

    <div class="col-md-6 col-xl-3">

        <div class="card border-0 shadow-sm">

            <div class="card-body">

                <div class="d-flex justify-content-between">

                    <div>

                        <div class="text-muted">
                            Students
                        </div>

                        <h3 class="mt-2 mb-0">
                            0
                        </h3>

                    </div>

                    <div class="fs-2 text-primary">
                        <i class="bi bi-people"></i>
                    </div>

                </div>

            </div>

        </div>

    </div>


    <div class="col-md-6 col-xl-3">

        <div class="card border-0 shadow-sm">

            <div class="card-body">

                <div class="d-flex justify-content-between">

                    <div>

                        <div class="text-muted">
                            Teachers
                        </div>

                        <h3 class="mt-2 mb-0">
                            0
                        </h3>

                    </div>

                    <div class="fs-2 text-success">
                        <i class="bi bi-person-badge"></i>
                    </div>

                </div>

            </div>

        </div>

    </div>


    <div class="col-md-6 col-xl-3">

        <div class="card border-0 shadow-sm">

            <div class="card-body">

                <div class="d-flex justify-content-between">

                    <div>

                        <div class="text-muted">
                            Classes
                        </div>

                        <h3 class="mt-2 mb-0">
                            0
                        </h3>

                    </div>

                    <div class="fs-2 text-warning">
                        <i class="bi bi-building"></i>
                    </div>

                </div>

            </div>

        </div>

    </div>


    <div class="col-md-6 col-xl-3">

        <div class="card border-0 shadow-sm">

            <div class="card-body">

                <div class="d-flex justify-content-between">

                    <div>

                        <div class="text-muted">
                            Fee Collection
                        </div>

                        <h3 class="mt-2 mb-0">
                            ₹0
                        </h3>

                    </div>

                    <div class="fs-2 text-danger">
                        <i class="bi bi-cash-stack"></i>
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection