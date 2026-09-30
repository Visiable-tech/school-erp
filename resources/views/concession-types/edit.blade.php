@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between
                align-items-center mb-3">

        <div>

            <h4 class="mb-1">
                Edit Concession Type
            </h4>

            <small class="text-muted">
                {{ $concessionType->name }}
            </small>

        </div>

        <a href="{{ route(
            'concession-types.index'
        ) }}"
           class="btn btn-outline-secondary">

            <i class="bi bi-arrow-left"></i>
            Back

        </a>

    </div>


    <div class="card border-0 shadow-sm">

        <div class="card-body">

            <form method="POST"
                  action="{{ route(
                    'concession-types.update',
                    $concessionType
                  ) }}">

                @csrf
                @method('PUT')

                @include(
                    'concession-types._form'
                )

                <hr>

                <div class="text-end">

                    <button type="submit"
                            class="btn btn-primary">

                        <i class="bi bi-check-circle"></i>
                        Update Concession Type

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection