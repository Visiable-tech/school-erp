@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between
                align-items-center mb-3">

        <div>

            <h4 class="mb-1">
                Edit School Account
            </h4>

            <small class="text-muted">
                {{ $schoolAccount->account_name }}
            </small>

        </div>

        <a href="{{ route(
            'school-accounts.index'
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
                      'school-accounts.update',
                      $schoolAccount
                  ) }}">

                @csrf
                @method('PUT')

                @include(
                    'school-accounts._form'
                )

                <hr>

                <div class="d-flex
                            justify-content-end
                            gap-2">

                    <a href="{{ route(
                        'school-accounts.index'
                    ) }}"
                       class="btn btn-light">
                        Cancel
                    </a>

                    <button type="submit"
                            class="btn btn-primary">

                        <i class="bi bi-check-circle"></i>
                        Update Account

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection