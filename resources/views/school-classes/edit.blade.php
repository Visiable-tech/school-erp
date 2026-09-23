@extends('layouts.admin')

@section('title', 'Edit Class')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h4 class="mb-1">
            Edit Class
        </h4>

        <div class="text-muted">
            Update class information
        </div>

    </div>

    <a
        href="{{ route('school-classes.index') }}"
        class="btn btn-outline-secondary"
    >
        <i class="bi bi-arrow-left"></i>
        Back
    </a>

</div>


<div class="row">

    <div class="col-lg-8">

        <div class="card border-0 shadow-sm">

            <div class="card-header bg-white py-3">
                <strong>Class Details</strong>
            </div>

            <div class="card-body">

                <form
                    method="POST"
                    action="{{ route(
                        'school-classes.update',
                        $schoolClass
                    ) }}"
                >

                    @csrf
                    @method('PUT')

                    @include('school-classes._form')

                    <div class="border-top pt-3 mt-4">

                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            <i class="bi bi-check-lg"></i>
                            Update Class
                        </button>

                        <a
                            href="{{ route('school-classes.index') }}"
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