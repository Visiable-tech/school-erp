@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between
                align-items-center mb-3">

        <div>

            <h4 class="mb-1">
                Edit Late Fee Fine
            </h4>

            <small class="text-muted">
                {{ $lateFeeFine->name }}
            </small>

        </div>

        <a href="{{ route(
            'late-fee-fines.index'
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
                    'late-fee-fines.update',
                    $lateFeeFine
                  ) }}">

                @csrf
                @method('PUT')

                @include(
                    'late-fee-fines._form'
                )

                <hr>

                <div class="text-end">

                    <button type="submit"
                            class="btn btn-primary">

                        <i class="bi bi-check-circle"></i>
                        Update Fine Rule

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection