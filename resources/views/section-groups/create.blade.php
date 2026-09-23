@extends('layouts.admin')

@section('title', 'Add Section Group')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h4 class="mb-1">
            Add Section Group
        </h4>

        <div class="text-muted">
            Group multiple sections together
        </div>
    </div>

    <a
        href="{{ route('section-groups.index') }}"
        class="btn btn-outline-secondary"
    >
        <i class="bi bi-arrow-left"></i>
        Back
    </a>

</div>


<div class="card border-0 shadow-sm">

    <div class="card-header bg-white py-3">
        <strong>Section Group Details</strong>
    </div>

    <div class="card-body">

        <form
            method="POST"
            action="{{ route('section-groups.store') }}"
        >

            @csrf

            @include('section-groups._form')

            <div class="border-top pt-3 mt-4">

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    <i class="bi bi-check-lg"></i>
                    Save Section Group
                </button>

                <a
                    href="{{ route('section-groups.index') }}"
                    class="btn btn-light ms-2"
                >
                    Cancel
                </a>

            </div>

        </form>

    </div>

</div>


@include('section-groups._script')

@endsection