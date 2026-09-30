@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <div>
            <h4 class="mb-1">Add Payment Mode</h4>

            <small class="text-muted">
                Fee Management → Setup → Payment Modes
            </small>
        </div>

        <a href="{{ route('payment-modes.index') }}"
           class="btn btn-outline-secondary">

            <i class="bi bi-arrow-left"></i>
            Back

        </a>

    </div>


    <div class="card border-0 shadow-sm">

        <div class="card-body">

            <form method="POST"
                  action="{{ route('payment-modes.store') }}">

                @csrf

                @include('payment-modes._form')

                <hr>

                <div class="text-end">

                    <button type="submit"
                            class="btn btn-primary">

                        <i class="bi bi-check-circle"></i>
                        Save Payment Mode

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection