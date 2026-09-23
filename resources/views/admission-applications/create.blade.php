@extends('layouts.admin')

@section('title', 'Admission Application')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h4 class="mb-1">
            Admission Application
        </h4>

        <div class="text-muted">
            {{ $admissionEnquiry->enquiry_no }}
            —
            {{ $admissionEnquiry->student_name }}
        </div>
    </div>

    <a
        href="{{ route('admission-enquiries.index') }}"
        class="btn btn-outline-secondary"
    >
        Back
    </a>

</div>


<form
    method="POST"
    action="{{ route(
        'admission-applications.store',
        $admissionEnquiry
    ) }}"
>

    @csrf

    @include('admission-applications._form')


    <div class="card border-0 shadow-sm">

        <div class="card-body">

            <button
                class="btn btn-primary"
                type="submit"
            >
                <i class="bi bi-check-lg"></i>
                Submit Application
            </button>

        </div>

    </div>

</form>

@endsection