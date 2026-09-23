@extends('layouts.admin')

@section('title', 'Edit Calendar Event')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h4 class="mb-1">
            Edit Calendar Event
        </h4>

        <div class="text-muted">
            Update school calendar event
        </div>
    </div>

    <a
        href="{{ route('school-calendar.index') }}"
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
                'school-calendar.update',
                $schoolCalendarEvent
            ) }}"
        >

            @csrf
            @method('PUT')

            @include('school-calendar._form')

            <div class="border-top pt-3 mt-3">

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    <i class="bi bi-check-lg"></i>
                    Update Event
                </button>

            </div>

        </form>

    </div>

</div>

@endsection