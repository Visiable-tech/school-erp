@extends('layouts.admin')

@section('title', 'Add Academic Year')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Edit Wing</h4>
        </div>

        <a href="{{ route('wings.index') }}"
        class="btn btn-outline-secondary">

            <i class="bi bi-arrow-left"></i>
            Back

        </a>
    </div>


    <div class="row justify-content-center">

        <div class="col-md-12">

            <div class="card">

                <div class="card-body">

                    @if($errors->any())
                        <div class="alert alert-danger">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form
                        method="POST"
                        action="{{ route('wings.update', $wing->id) }}"
                    >

                        @csrf
                        @method('PUT')

                        @include('wings._form')

                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            Update
                        </button>

                        <a
                            href="{{ route('wings.index') }}"
                            class="btn btn-secondary"
                        >
                            Cancel
                        </a>

                    </form>

                </div>

            </div>

        </div>

    </div>

@endsection    