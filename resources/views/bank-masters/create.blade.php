@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between
                align-items-center mb-3">

        <div>
            <h4 class="mb-1">
                Add Bank
            </h4>

            <small class="text-muted">
                Fee Management → Setup → Banks Master
            </small>
        </div>

        <a href="{{ route('bank-masters.index') }}"
           class="btn btn-outline-secondary">

            <i class="bi bi-arrow-left"></i>
            Back

        </a>

    </div>


    <div class="card border-0 shadow-sm">

        <div class="card-body">

            <form method="POST"
                  action="{{ route(
                      'bank-masters.store'
                  ) }}">

                @csrf

                @include('bank-masters._form')

                <hr>

                <div class="d-flex
                            justify-content-end
                            gap-2">

                    <a href="{{ route(
                        'bank-masters.index'
                    ) }}"
                       class="btn btn-light">
                        Cancel
                    </a>

                    <button type="submit"
                            class="btn btn-primary">

                        <i class="bi bi-check-circle"></i>
                        Save Bank

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection