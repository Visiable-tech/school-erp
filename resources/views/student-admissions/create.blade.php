@extends('layouts.admin')

@section('title', 'Student Admission')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h4 class="mb-1">
            Student Admission
        </h4>

        <div class="text-muted">

            {{ $admissionApplication->application_no }}

        </div>

    </div>


    <a
        href="{{ route(
            'admission-applications.edit',
            $admissionApplication
        ) }}"
        class="btn btn-outline-secondary"
    >

        <i class="bi bi-arrow-left"></i>
        Back

    </a>

</div>


{{-- STUDENT INFORMATION --}}

<div class="card border-0 shadow-sm mb-4">

    <div class="card-header bg-white py-3">

        <strong>
            Student Information
        </strong>

    </div>


    <div class="card-body">

        <div class="row">

            <div class="col-md-3 mb-3">

                <small class="text-muted">
                    Student
                </small>

                <div class="fw-semibold">

                    {{
                        $admissionApplication
                            ->student_name
                    }}

                </div>

            </div>


            <div class="col-md-3 mb-3">

                <small class="text-muted">
                    Date of Birth
                </small>

                <div class="fw-semibold">

                    {{
                        $admissionApplication
                            ->date_of_birth
                            ?->format('d M Y')
                        ?? '-'
                    }}

                </div>

            </div>


            <div class="col-md-3 mb-3">

                <small class="text-muted">
                    Academic Year
                </small>

                <div class="fw-semibold">

                    {{
                        $admissionApplication
                            ->academicYear
                            ?->name
                        ?? '-'
                    }}

                </div>

            </div>


            <div class="col-md-3 mb-3">

                <small class="text-muted">
                    Class
                </small>

                <div class="fw-semibold">

                    {{
                        $admissionApplication
                            ->schoolClass
                            ?->name
                        ?? '-'
                    }}

                </div>

            </div>

        </div>

    </div>

</div>


{{-- ADMISSION DETAILS --}}

<div class="card border-0 shadow-sm">

    <div class="card-header bg-white py-3">

        <strong>
            Admission Details
        </strong>

    </div>


    <div class="card-body">

        <form
            method="POST"
            action="{{ route(
                'student-admissions.store',
                $admissionApplication
            ) }}"
        >

            @csrf


            <div class="row">


                {{-- ADMISSION DATE --}}

                <div class="col-md-4 mb-3">

                    <label class="form-label">

                        Admission Date

                        <span class="text-danger">
                            *
                        </span>

                    </label>


                    <input
                        type="date"
                        name="admission_date"
                        class="form-control @error('admission_date') is-invalid @enderror"
                        value="{{ old(
                            'admission_date',
                            now()->format('Y-m-d')
                        ) }}"
                        required
                    >


                    @error('admission_date')

                        <div class="invalid-feedback">

                            {{ $message }}

                        </div>

                    @enderror

                </div>



                {{-- SECTION --}}

                <div class="col-md-4 mb-3">

                    <label class="form-label">

                        Section

                        <span class="text-danger">
                            *
                        </span>

                    </label>


                    <select
                        name="section_id"
                        class="form-select @error('section_id') is-invalid @enderror"
                        required
                    >

                        <option value="">
                            Select Section
                        </option>


                        @foreach($sections as $section)

                            <option
                                value="{{ $section->id }}"
                                @selected(
                                    old('section_id')
                                    == $section->id
                                )
                            >

                                {{ $section->name }}

                                @if($section->capacity)

                                    (Capacity:
                                    {{ $section->capacity }})

                                @endif

                            </option>

                        @endforeach

                    </select>


                    @error('section_id')

                        <div class="invalid-feedback">

                            {{ $message }}

                        </div>

                    @enderror


                    @if($sections->isEmpty())

                        <small class="text-danger">

                            No active sections found for
                            this class and academic year.

                        </small>

                    @endif

                </div>



                {{-- ROLL NUMBER --}}

                <div class="col-md-4 mb-3">

                    <label class="form-label">
                        Roll Number
                    </label>


                    <input
                        type="text"
                        name="roll_no"
                        class="form-control"
                        value="{{ old('roll_no') }}"
                        placeholder="Optional"
                    >

                </div>


            </div>


            <div class="alert alert-info">

                <strong>Admission Number:</strong>

                It will be generated automatically
                when the admission is confirmed.

            </div>


            <button
                type="submit"
                class="btn btn-success"
                {{ $sections->isEmpty() ? 'disabled' : '' }}
                onclick="return confirm('Confirm student admission?')"
            >

                <i class="bi bi-person-check"></i>

                Confirm Admission

            </button>

        </form>

    </div>

</div>

@endsection