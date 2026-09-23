@extends('layouts.admin')

@section('title', 'Calendar Event Types')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h4 class="mb-1">
            Calendar Event Types
        </h4>

        <div class="text-muted">
            Manage school calendar event categories
        </div>
    </div>


    @can('calendar.create')

        <a
            href="{{ route(
                'calendar-event-types.create'
            ) }}"
            class="btn btn-primary"
        >
            <i class="bi bi-plus-lg"></i>
            Add Event Type
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
                        <th>Name</th>
                        <th>Code</th>
                        <th>Holiday</th>
                        <th>Sort Order</th>
                        <th>Status</th>
                        <th width="150">Action</th>
                    </tr>

                </thead>

                <tbody>

                    @forelse($eventTypes as $type)

                        <tr>

                            <td>
                                {{
                                    $eventTypes->firstItem()
                                    + $loop->index
                                }}
                            </td>

                            <td>
                                <strong>
                                    {{ $type->name }}
                                </strong>
                            </td>

                            <td>
                                {{ $type->code ?: '-' }}
                            </td>

                            <td>

                                @if($type->is_holiday)

                                    <span class="badge bg-warning text-dark">
                                        Yes
                                    </span>

                                @else

                                    <span class="badge bg-light text-dark">
                                        No
                                    </span>

                                @endif

                            </td>

                            <td>
                                {{ $type->sort_order }}
                            </td>

                            <td>

                                @if($type->status)

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

                                @can('calendar.edit')

                                    <a
                                        href="{{ route(
                                            'calendar-event-types.edit',
                                            $type
                                        ) }}"
                                        class="btn btn-sm btn-outline-primary"
                                    >
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                @endcan


                                @can('calendar.delete')

                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'calendar-event-types.destroy',
                                            $type
                                        ) }}"
                                        class="d-inline"
                                        onsubmit="return confirm('Delete this event type?')"
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

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td
                                colspan="7"
                                class="text-center py-5 text-muted"
                            >
                                No calendar event types found.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        <div class="mt-3">
            {{ $eventTypes->links() }}
        </div>

    </div>

</div>

@endsection