@extends('layouts.admin')

@section('title', 'Edit Follow-up')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h4 class="mb-1">
            Edit Follow-up
        </h4>

        <div class="text-muted">
            {{ $admissionFollowup->enquiry->enquiry_no }}
            —
            {{ $admissionFollowup->enquiry->student_name }}
        </div>

    </div>

    <a
        href="{{ route('admission-followups.index') }}"
        class="btn btn-outline-secondary"
    >
        Back
    </a>

</div>


<div class="card border-0 shadow-sm">

    <div class="card-body">

        <form
            method="POST"
            action="{{ route(
                'admission-followups.update',
                $admissionFollowup
            ) }}"
        >

            @csrf
            @method('PUT')

            @include('admission-followups._form')

            <button
                type="submit"
                class="btn btn-primary"
            >
                Update Follow-up
            </button>

        </form>

    </div>

</div>

@endsection