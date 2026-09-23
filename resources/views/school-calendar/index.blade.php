@extends('layouts.admin')

@section('title', 'School Calendar')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h4 class="mb-1">
            School Calendar
        </h4>

        <div class="text-muted">
            Manage holidays, examinations and school events
        </div>
    </div>


    @can('calendar.create')

        <a
            href="{{ route('school-calendar.create') }}"
            class="btn btn-primary"
        >
            <i class="bi bi-plus-lg"></i>
            Add Event
        </a>

    @endcan

</div>


<div class="card border-0 shadow-sm mb-4">

    <div class="card-body">

        <form
            method="GET"
            action="{{ route('school-calendar.index') }}"
        >

            <div class="row g-3">

                <div class="col-md-3">

                    <label class="form-label">
                        Academic Year
                    </label>

                    <select
                        name="academic_year_id"
                        class="form-select"
                    >

                        <option value="">
                            All Academic Years
                        </option>

                        @foreach($academicYears as $year)

                            <option
                                value="{{ $year->id }}"
                                @selected(
                                    request('academic_year_id')
                                    == $year->id
                                )
                            >
                                {{ $year->name }}
                            </option>

                        @endforeach

                    </select>

                </div>


                <div class="col-md-3">

                    <label class="form-label">
                        Event Type
                    </label>

                    <select
                        name="calendar_event_type_id"
                        class="form-select"
                    >

                        <option value="">
                            All Types
                        </option>

                        @foreach($eventTypes as $type)

                            <option
                                value="{{ $type->id }}"
                                @selected(
                                    request('calendar_event_type_id')
                                    == $type->id
                                )
                            >
                                {{ $type->name }}
                            </option>

                        @endforeach

                    </select>

                </div>


                <div class="col-md-2">

                    <label class="form-label">
                        From
                    </label>

                    <input
                        type="date"
                        name="from_date"
                        class="form-control"
                        value="{{ request('from_date') }}"
                    >

                </div>


                <div class="col-md-2">

                    <label class="form-label">
                        To
                    </label>

                    <input
                        type="date"
                        name="to_date"
                        class="form-control"
                        value="{{ request('to_date') }}"
                    >

                </div>


                <div class="col-md-2 d-flex align-items-end">

                    <button
                        class="btn btn-primary me-2"
                    >
                        Filter
                    </button>

                    <a
                        href="{{ route('school-calendar.index') }}"
                        class="btn btn-light"
                    >
                        Reset
                    </a>

                </div>

            </div>

        </form>

    </div>

</div>


<div class="card border-0 shadow-sm">

    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-hover align-middle">

                <thead class="table-light">

                    <tr>
                        <th>Date</th>
                        <th>Event</th>
                        <th>Type</th>
                        <th>Academic Year</th>
                        <th>Holiday</th>
                        <th>Status</th>
                        <th width="150">Action</th>
                    </tr>

                </thead>

                <tbody>

                    @forelse($events as $event)

                        <tr>

                            <td>

                                <strong>
                                    {{
                                        $event->start_at
                                            ->format('d M Y')
                                    }}
                                </strong>

                                @if(!$event->is_all_day)

                                    <div class="small text-muted">
                                        {{
                                            $event->start_at
                                                ->format('h:i A')
                                        }}
                                    </div>

                                @else

                                    <div class="small text-muted">
                                        All Day
                                    </div>

                                @endif

                            </td>


                            <td>

                                <strong>
                                    {{ $event->title }}
                                </strong>

                                @if($event->description)

                                    <div class="small text-muted">
                                        {{
                                            \Illuminate\Support\Str::limit(
                                                $event->description,
                                                60
                                            )
                                        }}
                                    </div>

                                @endif

                            </td>


                            <td>
                                {{
                                    $event->eventType?->name
                                    ?? '-'
                                }}
                            </td>


                            <td>
                                {{
                                    $event->academicYear?->name
                                    ?? '-'
                                }}
                            </td>


                            <td>

                                @if($event->is_holiday)

                                    <span class="badge bg-warning text-dark">
                                        Holiday
                                    </span>

                                @else

                                    <span class="badge bg-light text-dark border">
                                        No
                                    </span>

                                @endif

                            </td>


                            <td>

                                @if($event->status)

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
                                            'school-calendar.edit',
                                            $event
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
                                            'school-calendar.destroy',
                                            $event
                                        ) }}"
                                        class="d-inline"
                                        onsubmit="return confirm('Delete this calendar event?')"
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
                                No calendar events found.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        @if($events->hasPages())

            <div class="mt-3">
                {{ $events->links() }}
            </div>

        @endif

    </div>

</div>

@endsection