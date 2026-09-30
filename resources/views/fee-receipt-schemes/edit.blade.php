@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between
                align-items-center mb-3">

        <div>
            <h4 class="mb-1">
                Edit Receipt No. Scheme
            </h4>

            <small class="text-muted">
                {{ $feeReceiptScheme->name }}
            </small>
        </div>

        <a href="{{ route(
            'fee-receipt-schemes.index'
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
                    'fee-receipt-schemes.update',
                    $feeReceiptScheme
                  ) }}">

                @csrf
                @method('PUT')

                @include(
                    'fee-receipt-schemes._form'
                )

                <hr>

                <div class="text-end">

                    <button type="submit"
                            class="btn btn-primary">

                        <i class="bi bi-check-circle"></i>
                        Update Scheme

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection