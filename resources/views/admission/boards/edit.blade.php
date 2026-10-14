@extends('layouts.admin')

@section('title', 'Edit Board / University')

@section('content')

<div class="container-fluid">

    <div class="card shadow-sm">

        <div class="card-header">
            <h5 class="mb-0">
                Edit Board / University
            </h5>
        </div>

        <div class="card-body">

            <form
                method="POST"
                action="{{ route(
                    'admission-boards.update',
                    $admissionBoard
                ) }}"
            >
                @csrf
                @method('PUT')

                @include('admission.boards._form')

                <div class="d-flex gap-2">

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        Update
                    </button>

                    <a
                        href="{{ route('admission-boards.index') }}"
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