@extends('layouts.admin')

@section('title', 'Add Communication Mode')

@section('content')

<div class="container-fluid">

    <div class="card shadow-sm">

        <div class="card-header">

            <h5 class="mb-0">
                Add Communication Mode
            </h5>

        </div>

        <div class="card-body">

            <form
                method="POST"
                action="{{ route(
                    'admission-communication-modes.store'
                ) }}"
            >

                @csrf

                @include(
                    'admission.communication-modes._form'
                )

                <div class="d-flex gap-2">

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        Save
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