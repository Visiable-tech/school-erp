@extends('layouts.admin')

@section('title', 'Edit Communication Mode')

@section('content')

<div class="container-fluid">

    <div class="card shadow-sm">

        <div class="card-header">

            <h5 class="mb-0">
                Edit Communication Mode
            </h5>

        </div>

        <div class="card-body">

            <form
                method="POST"
                action="{{ route(
                    'admission-communication-modes.update',
                    $admissionCommunicationMode
                ) }}"
            >

                @csrf
                @method('PUT')

                @include(
                    'admission.communication-modes._form'
                )

                <div class="d-flex gap-2">

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        Update
                    </button>

                    <a
                        href="{{ route(
                            'admission-communication-modes.index'
                        ) }}"
                        class="btn btn-secondary"
                    >
                        Cancel
                    </a>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection