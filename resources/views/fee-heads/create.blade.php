@extends('layouts.admin')

@section('title', 'Add Fee Head')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h4 class="mb-1">Add Fee Head</h4>

        <div class="text-muted">
            Create a new fee category.
        </div>
    </div>

    <a
        href="{{ route('fee-heads.index') }}"
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
            action="{{ route('fee-heads.store') }}"
        >

            @csrf

            @include('fee-heads._form')


            <div class="mt-4">

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    <i class="bi bi-check-circle me-1"></i>
                    Save Fee Head
                </button>


                <a
                    href="{{ route('fee-heads.index') }}"
                    class="btn btn-outline-secondary"
                >
                    Cancel
                </a>

            </div>

        </form>

    </div>

</div>

@endsection