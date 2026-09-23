@extends('layouts.admin')

@section('title', 'Edit Calendar Event Type')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h4 class="mb-1">
            Edit Calendar Event Type
        </h4>

        <div class="text-muted">
            Update calendar event type
        </div>
    </div>

    <a
        href="{{ route('calendar-event-types.index') }}"
        class="btn btn-outline-secondary"
    >
        <i class="bi bi-arrow-left"></i>
        Back
    </a>

</div>


<div class="card border-0 shadow-sm">

    <div class="card-body">

        <form
            method="POST"
            action="{{ route(
                'calendar-event-types.update',
                $calendarEventType
            ) }}"
        >

            @csrf
            @method('PUT')

            @include(
                'calendar-event-types._form'
            )

            <div class="border-top pt-3 mt-3">

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    <i class="bi bi-check-lg"></i>
                    Update Event Type
                </button>

            </div>

        </form>

    </div>

</div>

@endsection