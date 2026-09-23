@extends('layouts.admin')

@section('title', 'Admission Sessions')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h4 class="mb-1">
            Admission Sessions
        </h4>

        <div class="text-muted">
            Manage admission cycles
        </div>

    </div>


    @can('admission-session.create')

        <a
            href="{{ route('admission-sessions.create') }}"
            class="btn btn-primary"
        >
            <i class="bi bi-plus-lg"></i>
            Add Admission Session
        </a>

    @endcan

</div>


<div class="card border-0 shadow-sm">

    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-hover align-middle">

                <thead class="table-light">

                    <tr>
                        <th>#</th>
                        <th>Session</th>
                        <th>Academic Year</th>
                        <th>Period</th>
                        <th>Current</th>
                        <th>Status</th>
                        <th width="150">Action</th>
                    </tr>

                </thead>

                <tbody>

                    @forelse($sessions as $session)

                        <tr>

                            <td>
                                {{
                                    $sessions->firstItem()
                                    + $loop->index
                                }}
                            </td>


                            <td>
                                <strong>
                                    {{ $session->name }}
                                </strong>
                            </td>


                            <td>
                                {{
                                    $session
                                        ->academicYear
                                        ?->name ?? '-'
                                }}
                            </td>


                            <td>

                                @if($session->start_date)

                                    {{
                                        $session
                                            ->start_date
                                            ->format('d M Y')
                                    }}

                                @else

                                    -

                                @endif

                                <br>

                                <small class="text-muted">

                                    to

                                    @if($session->end_date)

                                        {{
                                            $session
                                                ->end_date
                                                ->format('d M Y')
                                        }}

                                    @else

                                        -

                                    @endif

                                </small>

                            </td>


                            <td>

                                @if($session->is_current)

                                    <span class="badge bg-primary">
                                        Current
                                    </span>

                                @else

                                    <span class="text-muted">
                                        -
                                    </span>

                                @endif

                            </td>


                            <td>

                                @if($session->status)

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

                                @can('admission-session.edit')

                                    <a
                                        href="{{ route(
                                            'admission-sessions.edit',
                                            $session
                                        ) }}"
                                        class="btn btn-sm btn-outline-primary"
                                    >
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                @endcan


                                @can('admission-session.delete')

                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'admission-sessions.destroy',
                                            $session
                                        ) }}"
                                        class="d-inline"
                                        onsubmit="return confirm('Delete this admission session?')"
                                    >

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-outline-danger"
                                        >
                                            <i class="bi bi-trash"></i>
                                        </button>

                                    </form>

                                @endcan

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="7"
                                class="text-center py-5 text-muted"
                            >

                                <i class="bi bi-calendar2-plus fs-2 d-block mb-2"></i>

                                No admission sessions found.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        @if($sessions->hasPages())

            <div class="mt-3">
                {{ $sessions->links() }}
            </div>

        @endif

    </div>

</div>

@endsection