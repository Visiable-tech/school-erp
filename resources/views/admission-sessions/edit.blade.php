@extends('layouts.admin')

@section('title', 'Edit Admission Session')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h4 class="mb-1">
            Edit Admission Session
        </h4>

        <div class="text-muted">
            Update admission session details
        </div>
    </div>

    <a
        href="{{ route('admission-sessions.index') }}"
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
                'admission-sessions.update',
                $admissionSession
            ) }}"
        >

            @csrf
            @method('PUT')

            @include('admission-sessions._form')

            <div class="border-top pt-3 mt-3">

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    <i class="bi bi-check-lg"></i>
                    Update Admission Session
                </button>

            </div>

        </form>

    </div>

</div>

@endsection