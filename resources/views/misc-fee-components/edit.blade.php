@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between
                align-items-center mb-3">

        <div>

            <h4 class="mb-1">
                Edit Misc. Component
            </h4>

            <small class="text-muted">
                {{ $miscFeeComponent->name }}
            </small>

        </div>

        <a href="{{ route(
            'misc-fee-components.index'
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
                      'misc-fee-components.update',
                      $miscFeeComponent
                  ) }}">

                @csrf
                @method('PUT')

                @include(
                    'misc-fee-components._form'
                )

                <hr>

                <div class="d-flex
                            justify-content-end
                            gap-2">

                    <a href="{{ route(
                        'misc-fee-components.index'
                    ) }}"
                       class="btn btn-light">

                        Cancel

                    </a>

                    <button type="submit"
                            class="btn btn-primary">

                        <i class="bi bi-check-circle"></i>
                        Update Component

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection