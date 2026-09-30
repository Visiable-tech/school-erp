@extends('layouts.admin')

@section('title', 'Edit TC')

@section('content')

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <div>

            <h4 class="mb-0">
                Edit T.C. Reason
            </h4>

            <small class="text-muted">
                Student Information System → Masters
            </small>

        </div>


        <a
            href="{{ route('tc-reasons.index') }}"
            class="btn btn-secondary"
        >
            <i class="bi bi-arrow-left"></i>
            Back
        </a>

    </div>


    <div class="card shadow-sm">

        <div class="card-header">
            <strong>
                {{ $tcReason->name }}
            </strong>
        </div>


        <div class="card-body">

            @if($errors->any())

                <div class="alert alert-danger">

                    <ul class="mb-0">

                        @foreach($errors->all() as $error)

                            <li>{{ $error }}</li>

                        @endforeach

                    </ul>

                </div>

            @endif


            <form
                method="POST"
                action="{{ route(
                    'tc-reasons.update',
                    $tcReason
                ) }}"
            >

                @csrf
                @method('PUT')


                @include(
                    'admin.students.tc-reasons._form'
                )


                <div class="mt-3">

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        <i class="bi bi-save"></i>
                        Update
                    </button>

                    <a
                        href="{{ route('tc-reasons.index') }}"
                        class="btn btn-light"
                    >
                        Cancel
                    </a>

                </div>

            </form>

        </div>

    </div>

</div>

@section