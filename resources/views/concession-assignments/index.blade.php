@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <div>
            <h4 class="mb-1">
                Concession Assignments
            </h4>

            <small class="text-muted">
                Fee Management → Optional Assignments
            </small>
        </div>


        @can('concession-assignment.create')

            <a href="{{ route(
                'concession-assignments.create'
            ) }}"
               class="btn btn-primary">

                <i class="bi bi-plus-circle"></i>
                Assign Concession

            </a>

        @endcan

    </div>


    <div class="card border-0 shadow-sm">

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">

                    <tr>
                        <th>Student</th>
                        <th>Class</th>
                        <th>Concession</th>
                        <th>Value</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th width="190">Action</th>
                    </tr>

                </thead>


                <tbody>

                @forelse($assignments as $assignment)

                    <tr>

                        <td>

                            <strong>
                                {{
                                    $assignment->student->name
                                    ?? $assignment->student->student_name
                                    ?? '—'
                                }}
                            </strong>

                            <div class="small text-muted">
                                {{
                                    $assignment->student->admission_no
                                    ?? ''
                                }}
                            </div>

                        </td>


                        <td>

                            {{
                                $assignment
                                    ->enrollment
                                    ->schoolClass
                                    ->name ?? '—'
                            }}

                            @if(
                                $assignment
                                    ->enrollment
                                    ->section
                            )
                                /
                                {{
                                    $assignment
                                        ->enrollment
                                        ->section
                                        ->name
                                }}
                            @endif

                        </td>


                        <td>
                            {{
                                $assignment
                                    ->concessionType
                                    ->name ?? '—'
                            }}
                        </td>


                        <td>

                            @if(
                                $assignment->concession_mode
                                === 'percentage'
                            )

                                {{
                                    number_format(
                                        $assignment->concession_value,
                                        2
                                    )
                                }}%

                            @else

                                ₹{{
                                    number_format(
                                        $assignment->concession_value,
                                        2
                                    )
                                }}

                            @endif

                        </td>


                        <td>
                            {{
                                $assignment
                                    ->assigned_date
                                    ?->format('d M Y')
                            }}
                        </td>


                        <td>

                            @switch(
                                $assignment->approval_status
                            )

                                @case('pending')
                                    <span class="badge bg-warning text-dark">
                                        Pending
                                    </span>
                                    @break

                                @case('approved')
                                    <span class="badge bg-success">
                                        Approved
                                    </span>
                                    @break

                                @case('rejected')
                                    <span class="badge bg-danger">
                                        Rejected
                                    </span>
                                    @break

                                @default
                                    <span class="badge bg-secondary">
                                        {{
                                            ucfirst(
                                                $assignment->approval_status
                                            )
                                        }}
                                    </span>

                            @endswitch

                        </td>


                        <td>

                            <div class="d-flex gap-1">

                                @if(
                                    $assignment->approval_status
                                    === 'pending'
                                )

                                    @can('concession-assignment.approve')

                                        <form method="POST"
                                              action="{{ route(
                                                'concession-assignments.approve',
                                                $assignment
                                              ) }}">

                                            @csrf

                                            <button class="btn btn-sm btn-success"
                                                    title="Approve">

                                                <i class="bi bi-check-lg"></i>

                                            </button>

                                        </form>


                                        <form method="POST"
                                              action="{{ route(
                                                'concession-assignments.reject',
                                                $assignment
                                              ) }}">

                                            @csrf

                                            <button class="btn btn-sm btn-warning"
                                                    title="Reject">

                                                <i class="bi bi-x-lg"></i>

                                            </button>

                                        </form>

                                    @endcan

                                @endif


                                @can('concession-assignment.delete')

                                    <form method="POST"
                                          action="{{ route(
                                            'concession-assignments.destroy',
                                            $assignment
                                          ) }}">

                                        @csrf
                                        @method('DELETE')

                                        <button class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm(
                                                    'Remove this concession?'
                                                )">

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

                            No concession assignments found.

                        </td>
                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>


        @if($assignments->hasPages())

            <div class="card-footer bg-white">
                {{ $assignments->links() }}
            </div>

        @endif

    </div>

</div>

@endsection