@extends('layouts.admin')

@section('title', 'Edit Admission Enquiry')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h4 class="mb-1">
            Edit Admission Enquiry
        </h4>

        <div class="text-muted">
            {{ $admissionEnquiry->enquiry_no }}
        </div>

    </div>

    <a
        href="{{ route('admission-enquiries.index') }}"
        class="btn btn-outline-secondary"
    >
        <i class="bi bi-arrow-left"></i>
        Back
    </a>

</div>


<form
    method="POST"
    action="{{ route(
        'admission-enquiries.update',
        $admissionEnquiry
    ) }}"
>

    @csrf
    @method('PUT')

    @include('admission-enquiries._form')


    <div class="card border-0 shadow-sm">

        <div class="card-body">

            <button
                type="submit"
                class="btn btn-primary"
            >
                <i class="bi bi-check-lg"></i>
                Update Enquiry
            </button>

        </div>

    </div>

</form>

@endsection