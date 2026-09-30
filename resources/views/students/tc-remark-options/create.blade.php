@extends('layouts.admin')

@section('title', 'Add TC Remarks')

@section('content')

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <div>

            <h4 class="mb-0">
                Add T.C. Remark Option
            </h4>

            <small class="text-muted">
                Student Information System → Masters
            </small>

        </div>


        <a
            href="{{ route('tc-remark-options.index') }}"
            class="btn btn-secondary"
        >
            <i class="bi bi-arrow-left"></i>
            Back
        </a>

    </div>


    <div class="card shadow-sm">

        <div class="card-header">
            <strong>T.C. Remark Details</strong>
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
                action="{{ route('tc-remark-options.store') }}"
            >

                @csrf

                @include(
                    'admin.students.tc-remark-options._form'
                )


                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    <i class="bi bi-save"></i>
                    Save
                </button>

                <a
                    href="{{ route('tc-remark-options.index') }}"
                    class="btn btn-light"
                >
                    Cancel
                </a>

            </form>

        </div>

    </div>

</div>

@endsection