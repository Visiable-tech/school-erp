@extends('layouts.admin')

@section('title', 'Add Source of Information')

@section('content')
<div class="container-fluid">

    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">Add Source of Information</h5>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admission-information-sources.store') }}">
                @csrf

                @include('admission.information-sources._form')

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i> Save
                    </button>
                    <a href="{{ route('admission-information-sources.index') }}"
                       class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection