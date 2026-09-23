@extends('layouts.admin')

@section('title', 'Add Admission Follow-up')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h4 class="mb-1">
            Add Follow-up
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


<div class="card border-0 shadow-sm mb-4">

    <div class="card-body">

        <div class="row">

            <div class="col-md-4">
                <small class="text-muted">
                    Student
                </small>

                <div class="fw-semibold">
                    {{ $admissionEnquiry->student_name }}
                </div>
            </div>

            <div class="col-md-4">
                <small class="text-muted">
                    Mobile
                </small>

                <div class="fw-semibold">
                    {{ $admissionEnquiry->mobile }}
                </div>
            </div>

            <div class="col-md-4">
                <small class="text-muted">
                    Current Status
                </small>

                <div class="fw-semibold">
                    {{
                        ucwords(
                            str_replace(
                                '_',
                                ' ',
                                $admissionEnquiry->enquiry_status
                            )
                        )
                    }}
                </div>
            </div>

        </div>

    </div>

</div>


<div class="card border-0 shadow-sm">

    <div class="card-body">

        <form
            method="POST"
            action="{{ route(
                'admission-followups.store',
                $admissionEnquiry
            ) }}"
        >

            @csrf

            @include('admission-followups._form')

            <button
                type="submit"
                class="btn btn-primary"
            >
                <i class="bi bi-check-lg"></i>
                Save Follow-up
            </button>

        </form>

    </div>

</div>

@endsection