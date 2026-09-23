@extends('layouts.admin')

@section('title', 'Add Calendar Event')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h4 class="mb-1">
            Add Calendar Event
        </h4>

        <div class="text-muted">
            Add an event to the school calendar
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

    <div class="card-header bg-white py-3">
        <strong>Event Details</strong>
    </div>

    <div class="card-body">

        <form
            method="POST"
            action="{{ route('school-calendar.store') }}"
        >

            @csrf

            @include('school-calendar._form')

            <div class="border-top pt-3 mt-3">

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    <i class="bi bi-check-lg"></i>
                    Save Event
                </button>

                <a
                    href="{{ route('school-calendar.index') }}"
                    class="btn btn-light ms-2"
                >
                    Cancel
                </a>

            </div>

        </form>

    </div>

</div>

@endsection