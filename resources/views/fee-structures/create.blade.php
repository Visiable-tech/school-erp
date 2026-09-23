@extends('layouts.admin')

@section('title', 'Add Fee Structure')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h4 class="mb-1">
            Add Fee Structure
        </h4>

        <div class="text-muted">
            Configure fees for an academic year and class.
        </div>
    </div>

    <a
        href="{{ route('fee-structures.index') }}"
        class="btn btn-outline-secondary"
    >
        <i class="bi bi-arrow-left me-1"></i>
        Back
    </a>

</div>


<div class="card border-0 shadow-sm">

    <div class="card-body">

        <form
            method="POST"
            action="{{ route('fee-structures.store') }}"
        >

            @csrf

            @include('fee-structures._form')


            <div class="mt-4">

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    <i class="bi bi-check-circle me-1"></i>
                    Save Fee Structure
                </button>

                <a
                    href="{{ route('fee-structures.index') }}"
                    class="btn btn-outline-secondary"
                >
                    Cancel
                </a>

            </div>

        </form>

    </div>

</div>

@endsection