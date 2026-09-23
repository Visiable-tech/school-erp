@extends('layouts.admin')

@section('title', 'Add Academic Year')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Add Academic Year</h4>
        <div class="text-muted">
            Create a new academic session
        </div>
    </div>

    <a href="{{ route('academic-years.index') }}"
       class="btn btn-outline-secondary">

        <i class="bi bi-arrow-left"></i>
        Back

    </a>
</div>


<div class="row">

    <div class="col-lg-12">

        <div class="card border-0 shadow-sm">

            <div class="card-header bg-white py-3">
                <strong>Academic Year Details</strong>
            </div>

            <div class="card-body">

                <form method="POST"
                      action="{{ route('academic-years.store') }}">

                    @csrf

                    @include('academic-years._form')

                    <div class="border-top pt-3 mt-4">

                        <button type="submit"
                                class="btn btn-primary">

                            <i class="bi bi-check-lg"></i>
                            Save Academic Year

                        </button>

                        <a href="{{ route('academic-years.index') }}"
                           class="btn btn-light ms-2">

                            Cancel

                        </a>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>

@endsection