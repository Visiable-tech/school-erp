@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between
                align-items-center mb-3">

        <div>

            <h4 class="mb-1">
                Fee Compile Queues
            </h4>

            <small class="text-muted">
                Fee Management → Setup → Fee Compile Queues
            </small>

        </div>


        @can('student-fee-assignment.bulk-create')

            <a href="{{ route(
                'fee-compile.index'
            ) }}"
               class="btn btn-primary">

                <i class="bi bi-calculator"></i>
                Compile Fee

            </a>

        @endcan

    </div>


    <div class="card border-0 shadow-sm mb-3">

        <div class="card-body">

            <form method="GET">

                <div class="row g-2">

                    <div class="col-md-4">

                        <select name="status"
                                class="form-select">

                            <option value="">
                                All Status
                            </option>

                            @foreach([
                                'pending' => 'Pending',
                                'processing' => 'Processing',
                                'completed' => 'Completed',
                                'failed' => 'Failed',
                                'cancelled' => 'Cancelled',
                            ] as $key => $label)

                                <option
                                    value="{{ $key }}"
                                    {{ request('status') === $key
                                        ? 'selected'
                                        : '' }}>

                                    {{ $label }}

                                </option>

                            @endforeach

                        </select>

                    </div>


                    <div class="col-md-2">

                        <button type="submit"
                                class="btn btn-primary">

                            <i class="bi bi-search"></i>
                            Filter

                        </button>

                    </div>


                    <div class="col-md-2">

                        <a href="{{ route(
                            'fee-compile-queues.index'
                        ) }}"
                           class="btn btn-light">

                            Reset

                        </a>

                    </div>

                </div>

            </form>

        </div>

    </div>


    <div class="card border-0 shadow-sm">

        <div class="table-responsive">

            <table class="table table-hover
                          align-middle mb-0">

                <thead class="table-light">

                    <tr>

                        <th>Job</th>
                        <th>Academic Year</th>
                        <th>Class</th>
                        <th>Template</th>
                        <th>Students</th>
                        <th width="180">Progress</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th width="110">Action</th>

                    </tr>

                </thead>


                <tbody>

                @forelse($queues as $queue)

                    <tr>

                        <td>
                            <strong>
                                #{{ $queue->id }}
                            </strong>
                        </td>


                        <td>
                            {{
                                $queue->academicYear
                                    ->name ?? '—'
                            }}
                        </td>


                        <td>

                            {{
                                $queue->schoolClass
                                    ->name ?? '—'
                            }}

                            @if($queue->section)

                                <div class="small text-muted">
                                    Section:
                                    {{ $queue->section->name }}
                                </div>

                            @endif

                        </td>


                        <td>
                            {{
                                $queue->feeStructure
                                    ->name ?? '—'
                            }}
                        </td>


                        <td>

                            {{ $queue->processed_students }}

                            /

                            {{ $queue->total_students }}

                        </td>


                        <td>

                            <div class="progress"
                                 style="height: 8px;">

                                <div class="progress-bar"
                                     role="progressbar"
                                     style="width:
                                        {{ min(
                                            100,
                                            $queue->progress
                                        ) }}%">
                                </div>

                            </div>

                            <small class="text-muted">
                                {{ $queue->progress }}%
                            </small>

                        </td>


                        <td>

                            @switch($queue->status)

                                @case('pending')

                                    <span class="badge bg-warning text-dark">
                                        Pending
                                    </span>

                                    @break


                                @case('processing')

                                    <span class="badge bg-info">
                                        Processing
                                    </span>

                                    @break


                                @case('completed')

                                    <span class="badge bg-success">
                                        Completed
                                    </span>

                                    @break


                                @case('failed')

                                    <span class="badge bg-danger">
                                        Failed
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

                            {{ $queue->created_at
                                ?->format(
                                    'd M Y h:i A'
                                ) }}

                        </td>


                        <td>

                            <a href="{{ route(
                                'fee-compile-queues.show',
                                $queue
                            ) }}"
                               class="btn btn-sm
                                      btn-outline-primary">

                                <i class="bi bi-eye"></i>

                            </a>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="9"
                            class="text-center
                                   text-muted py-5">

                            No compile jobs found.

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>


        @if($queues->hasPages())

            <div class="card-footer bg-white">

                {{ $queues->links() }}

            </div>

        @endif

    </div>

</div>

@endsection