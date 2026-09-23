@extends('layouts.admin')

@section('title', 'Add Subject')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h4 class="mb-1">
            Add Subject
        </h4>

        <div class="text-muted">
            Create a new subject for the school
        </div>
    </div>

    <a
        href="{{ route('subjects.index') }}"
        class="btn btn-outline-secondary"
    >
        <i class="bi bi-arrow-left"></i>
        Back
    </a>

</div>


<div class="row">

    <div class="col-lg-9">

        <div class="card border-0 shadow-sm">

            <div class="card-header bg-white py-3">
                <strong>Subject Details</strong>
            </div>

            <div class="card-body">

                <form
                    method="POST"
                    action="{{ route('subjects.store') }}"
                >

                    @csrf

                    @include('subjects._form')

                    <div class="border-top pt-3 mt-3">

                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            <i class="bi bi-check-lg"></i>
                            Save Subject
                        </button>

                        <a
                            href="{{ route('subjects.index') }}"
                            class="btn btn-light ms-2"
                        >
                            Cancel
                        </a>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>

@endsection