@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between
                align-items-center mb-3">

        <div>

            <h4 class="mb-1">
                Compile Job #{{ $feeCompileQueue->id }}
            </h4>

            <small class="text-muted">
                Fee Compile Queue Details
            </small>

        </div>


        <a href="{{ route(
            'fee-compile-queues.index'
        ) }}"
           class="btn btn-outline-secondary">

            <i class="bi bi-arrow-left"></i>
            Back

        </a>

    </div>


    <div class="row g-3 mb-3">

        <div class="col-md-3">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <small class="text-muted">
                        Total Students
                    </small>

                    <h3>
                        {{ $feeCompileQueue->total_students }}
                    </h3>

                </div>

            </div>

        </div>


        <div class="col-md-3">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <small class="text-muted">
                        Compiled
                    </small>

                    <h3 class="text-success">
                        {{ $feeCompileQueue->compiled_students }}
                    </h3>

                </div>

            </div>

        </div>


        <div class="col-md-3">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <small class="text-muted">
                        Skipped
                    </small>

                    <h3 class="text-warning">
                        {{ $feeCompileQueue->skipped_students }}
                    </h3>

                </div>

            </div>

        </div>


        <div class="col-md-3">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <small class="text-muted">
                        Failed
                    </small>

                    <h3 class="text-danger">
                        {{ $feeCompileQueue->failed_students }}
                    </h3>

                </div>

            </div>

        </div>

    </div>


    <div class="card border-0 shadow-sm mb-3">

        <div class="card-body">

            <div class="row g-3">

                <div class="col-md-3">

                    <small class="text-muted">
                        Academic Year
                    </small>

                    <div class="fw-semibold">
                        {{
                            $feeCompileQueue
                                ->academicYear
                                ->name ?? '—'
                        }}
                    </div>

                </div>


                <div class="col-md-3">

                    <small class="text-muted">
                        Class
                    </small>

                    <div class="fw-semibold">

                        {{
                            $feeCompileQueue
                                ->schoolClass
                                ->name ?? '—'
                        }}

                        @if($feeCompileQueue->section)

                            /
                            {{
                                $feeCompileQueue
                                    ->section
                                    ->name
                            }}

                        @endif

                    </div>

                </div>


                <div class="col-md-3">

                    <small class="text-muted">
                        Fee Template
                    </small>

                    <div class="fw-semibold">
                        {{
                            $feeCompileQueue
                                ->feeStructure
                                ->name ?? '—'
                        }}
                    </div>

                </div>


                <div class="col-md-3">

                    <small class="text-muted">
                        Compile Date
                    </small>

                    <div class="fw-semibold">

                        {{
                            $feeCompileQueue
                                ->compile_date
                                ?->format('d M Y')
                        }}

                    </div>

                </div>

            </div>

        </div>

    </div>


    <div class="card border-0 shadow-sm mb-3">

        <div class="card-header bg-white">

            <strong>
                Progress
            </strong>

        </div>


        <div class="card-body">

            <div class="progress"
                 style="height: 22px;">

                <div class="progress-bar"
                     style="width:
                        {{ min(
                            100,
                            $feeCompileQueue->progress
                        ) }}%">

                    {{ $feeCompileQueue->progress }}%

                </div>

            </div>


            <div class="row mt-3">

                <div class="col-md-3">
                    Processed:
                    <strong>
                        {{ $feeCompileQueue->processed_students }}
                    </strong>
                </div>

                <div class="col-md-3">
                    Started:
                    <strong>
                        {{
                            $feeCompileQueue
                                ->started_at
                                ?->format('d M Y h:i A')
                                ?? '—'
                        }}
                    </strong>
                </div>

                <div class="col-md-3">
                    Completed:
                    <strong>
                        {{
                            $feeCompileQueue
                                ->completed_at
                                ?->format('d M Y h:i A')
                                ?? '—'
                        }}
                    </strong>
                </div>

                <div class="col-md-3">
                    Created By:
                    <strong>
                        {{
                            $feeCompileQueue
                                ->createdBy
                                ->name ?? '—'
                        }}
                    </strong>
                </div>

            </div>

        </div>

    </div>


    @if($feeCompileQueue->error_message)

        <div class="alert alert-danger">

            <strong>
                Error:
            </strong>

            {{ $feeCompileQueue->error_message }}

        </div>

    @endif


    <div class="d-flex gap-2">

        @if(
            $feeCompileQueue->status ===
            'pending'
        )

            @can('fee-compile-queue.cancel')

                <form method="POST"
                      action="{{ route(
                        'fee-compile-queues.cancel',
                        $feeCompileQueue
                      ) }}">

                    @csrf

                    <button type="submit"
                            class="btn btn-outline-danger"
                            onclick="return confirm(
                                'Cancel this compile job?'
                            )">

                        Cancel Job

                    </button>

                </form>

            @endcan

        @endif


        @if(
            $feeCompileQueue->status ===
            'failed'
        )

            @can('fee-compile-queue.retry')

                <form method="POST"
                      action="{{ route(
                        'fee-compile-queues.retry',
                        $feeCompileQueue
                      ) }}">

                    @csrf

                    <button type="submit"
                            class="btn btn-warning"
                            onclick="return confirm(
                                'Retry this compile job?'
                            )">

                        <i class="bi bi-arrow-repeat"></i>
                        Retry

                    </button>

                </form>

            @endcan

        @endif

    </div>

</div>

@endsection