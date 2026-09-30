@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between
                align-items-center mb-3">

        <div>

            <h4 class="mb-1">
                Optional Fee Assignments
            </h4>

            <small class="text-muted">
                Student-wise optional fees
            </small>

        </div>


        @can('optional-fee-assignment.create')

            <a href="{{ route(
                'optional-fee-assignments.create'
            ) }}"
               class="btn btn-primary">

                <i class="bi bi-plus-circle"></i>

                Assign Optional Fee

            </a>

        @endcan

    </div>


    <div class="card border-0 shadow-sm">

        <div class="table-responsive">

            <table class="table table-hover
                          align-middle mb-0">

                <thead class="table-light">

                    <tr>
                        <th>#</th>
                        <th>Student</th>
                        <th>Class</th>
                        <th>Component</th>
                        <th>Amount</th>
                        <th>Effective Period</th>
                        <th>Assigned</th>
                        <th width="80">Action</th>
                    </tr>

                </thead>


                <tbody>

                @forelse(
                    $assignments as $assignment
                )

                    <tr>

                        <td>
                            {{ $assignments->firstItem()
                               + $loop->index }}
                        </td>


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
                                    ->feeHead
                                    ->name ?? '—'
                            }}

                        </td>


                        <td>

                            ₹{{ number_format(
                                $assignment->amount,
                                2
                            ) }}

                        </td>


                        <td>

                            {{
                                $assignment
                                    ->effective_from
                                    ?->format('d M Y')
                                ?? '—'
                            }}

                            @if(
                                $assignment->effective_to
                            )

                                <br>

                                <small class="text-muted">

                                    to

                                    {{
                                        $assignment
                                            ->effective_to
                                            ->format('d M Y')
                                    }}

                                </small>

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

                            @can(
                                'optional-fee-assignment.delete'
                            )

                                <form method="POST"
                                      action="{{ route(
                                        'optional-fee-assignments.destroy',
                                        $assignment
                                      ) }}">

                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="btn btn-sm
                                               btn-outline-danger"
                                        onclick="return confirm(
                                            'Delete this optional fee assignment?'
                                        )">

                                        <i class="bi bi-trash"></i>

                                    </button>

                                </form>

                            @endcan

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="8"
                            class="text-center
                                   text-muted py-5">

                            No optional fee assignments found.

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