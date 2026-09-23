@extends('layouts.admin')

@section('title', 'Edit Student Enrollment')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h4 class="mb-1">
            Edit Academic Enrollment
        </h4>

        <div class="text-muted">
            {{ $studentEnrollment->student->student_name }}
            |
            {{ $studentEnrollment->student->admission_no }}
        </div>

    </div>


    <a
        href="{{ route(
            'students.show',
            $studentEnrollment->student
        ) }}"
        class="btn btn-outline-secondary"
    >
        <i class="bi bi-arrow-left"></i>
        Back
    </a>

</div>


<div class="card border-0 shadow-sm">

    <div class="card-body">

        <form
            method="POST"
            action="{{ route(
                'student-enrollments.update',
                $studentEnrollment
            ) }}"
        >

            @csrf
            @method('PUT')


            <div class="row g-3">


                <div class="col-md-6">

                    <label class="form-label">
                        Academic Year
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        value="{{ $studentEnrollment->academicYear->name }}"
                        disabled
                    >

                </div>


                <div class="col-md-6">

                    <label class="form-label">
                        Class
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        value="{{ $studentEnrollment->schoolClass->name }}"
                        disabled
                    >

                </div>



                <div class="col-md-4">

                    <label class="form-label">
                        Section
                        <span class="text-danger">*</span>
                    </label>

                    <select
                        name="section_id"
                        class="form-select"
                        required
                    >

                        @foreach($sections as $section)

                            <option
                                value="{{ $section->id }}"
                                @selected(
                                    old(
                                        'section_id',
                                        $studentEnrollment->section_id
                                    )
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

                        <div class="text-danger small mt-1">
                            {{ $message }}
                        </div>

                    @enderror

                </div>



                <div class="col-md-4">

                    <label class="form-label">
                        Roll Number
                    </label>

                    <input
                        type="text"
                        name="roll_no"
                        class="form-control"
                        value="{{ old(
                            'roll_no',
                            $studentEnrollment->roll_no
                        ) }}"
                    >

                    @error('roll_no')

                        <div class="text-danger small mt-1">
                            {{ $message }}
                        </div>

                    @enderror

                </div>



                <div class="col-md-4">

                    <label class="form-label">
                        Enrollment Status
                    </label>

                    <select
                        name="enrollment_status"
                        class="form-select"
                        required
                    >

                        @foreach([
                            'active' => 'Active',
                            'promoted' => 'Promoted',
                            'transferred' => 'Transferred',
                            'withdrawn' => 'Withdrawn',
                            'completed' => 'Completed'
                        ] as $value => $label)

                            <option
                                value="{{ $value }}"
                                @selected(
                                    old(
                                        'enrollment_status',
                                        $studentEnrollment
                                            ->enrollment_status
                                    )
                                    === $value
                                )
                            >
                                {{ $label }}
                            </option>

                        @endforeach

                    </select>

                </div>

            </div>


            <hr class="my-4">


            <button
                type="submit"
                class="btn btn-primary"
            >

                <i class="bi bi-check-lg"></i>

                Update Enrollment

            </button>


            <a
                href="{{ route(
                    'students.show',
                    $studentEnrollment->student
                ) }}"
                class="btn btn-outline-secondary"
            >
                Cancel
            </a>

        </form>

    </div>

</div>

@endsection