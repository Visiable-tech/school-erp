@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between
                align-items-center mb-3">

        <div>
            <h4 class="mb-1">
                Optional Fee Assignment
            </h4>

            <small class="text-muted">
                Assign an optional fee to a student
            </small>
        </div>

        <a href="{{ route(
            'optional-fee-assignments.index'
        ) }}"
           class="btn btn-outline-secondary">

            <i class="bi bi-arrow-left"></i>
            Back

        </a>

    </div>


    <div class="card border-0 shadow-sm">

        <div class="card-body">

            <form method="POST"
                  action="{{ route(
                    'optional-fee-assignments.store'
                  ) }}">

                @csrf


                <div class="row g-3">

                    <div class="col-md-4">

                        <label class="form-label">
                            Academic Year *
                        </label>

                        <select name="academic_year_id"
                                id="academic_year_id"
                                class="form-select"
                                required>

                            <option value="">
                                Select Academic Year
                            </option>

                            @foreach($academicYears as $year)

                                <option value="{{ $year->id }}">
                                    {{ $year->name }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    <div class="col-md-8">

                        <label class="form-label">
                            Search Student *
                        </label>

                        <div class="input-group">

                            <input type="text"
                                   id="studentSearch"
                                   class="form-control"
                                   placeholder="Student name or admission number">

                            <button type="button"
                                    id="searchStudent"
                                    class="btn btn-outline-primary">

                                Search

                            </button>

                        </div>

                    </div>


                    <div class="col-md-12">

                        <label class="form-label">
                            Student *
                        </label>

                        <select name="student_enrollment_id"
                                id="student_enrollment_id"
                                class="form-select"
                                required>

                            <option value="">
                                Search and select student
                            </option>

                        </select>

                    </div>


                    <div class="col-md-4">

                        <label class="form-label">
                            Optional Fee Component *
                        </label>

                        <select name="fee_head_id"
                                id="fee_head_id"
                                class="form-select"
                                required>

                            <option value="">
                                Select Component
                            </option>

                            @foreach(
                                $feeComponents as $component
                            )

                                <option
                                    value="{{ $component->id }}">

                                    {{ $component->name }}

                                    @if($component->feeCycle)
                                        -
                                        {{ $component->feeCycle->name }}
                                    @endif

                                </option>

                            @endforeach

                        </select>

                    </div>


                    <div class="col-md-4">

                        <label class="form-label">
                            Amount *
                        </label>

                        <div class="input-group">

                            <span class="input-group-text">
                                ₹
                            </span>

                            <input type="number"
                                   name="amount"
                                   class="form-control"
                                   min="0.01"
                                   step="0.01"
                                   required>

                        </div>

                    </div>


                    <div class="col-md-4">

                        <label class="form-label">
                            Assigned Date *
                        </label>

                        <input type="date"
                               name="assigned_date"
                               class="form-control"
                               value="{{ date('Y-m-d') }}"
                               required>

                    </div>


                    <div class="col-md-4">

                        <label class="form-label">
                            Effective From
                        </label>

                        <input type="date"
                               name="effective_from"
                               class="form-control">

                    </div>


                    <div class="col-md-4">

                        <label class="form-label">
                            Effective To
                        </label>

                        <input type="date"
                               name="effective_to"
                               class="form-control">

                    </div>


                    <div class="col-md-12">

                        <label class="form-label">
                            Remarks
                        </label>

                        <textarea name="remarks"
                                  class="form-control"
                                  rows="3"></textarea>

                    </div>

                </div>


                <hr>


                <div class="text-end">

                    <button type="submit"
                            class="btn btn-primary">

                        <i class="bi bi-check-circle"></i>

                        Assign Optional Fee

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const year =
            document.getElementById(
                'academic_year_id'
            );

        const search =
            document.getElementById(
                'studentSearch'
            );

        const button =
            document.getElementById(
                'searchStudent'
            );

        const student =
            document.getElementById(
                'student_enrollment_id'
            );


        async function loadStudents()
        {
            if (!year.value) {

                alert(
                    'Please select Academic Year first.'
                );

                return;
            }


            const params =
                new URLSearchParams({
                    academic_year_id:
                        year.value,

                    search:
                        search.value
                });


            student.innerHTML =
                '<option value="">Loading...</option>';


            try {

                const response =
                    await fetch(
                        '{{ route(
                            "optional-fee-assignments.students"
                        ) }}'
                        + '?' + params
                    );


                if (!response.ok) {
                    throw new Error(
                        'Student loading failed.'
                    );
                }


                const students =
                    await response.json();


                student.innerHTML =
                    '<option value="">Select Student</option>';


                students.forEach(
                    function (item) {

                        const option =
                            document.createElement(
                                'option'
                            );

                        option.value =
                            item.id;

                        option.textContent =
                            item.name
                            + ' | '
                            + item.admission_no
                            + ' | '
                            + item.class
                            + ' - '
                            + item.section;

                        student.appendChild(
                            option
                        );
                    }
                );

            } catch (error) {

                console.error(error);

                student.innerHTML =
                    '<option value="">Unable to load students</option>';
            }
        }


        button.addEventListener(
            'click',
            loadStudents
        );


        search.addEventListener(
            'keydown',
            function (event) {

                if (event.key === 'Enter') {

                    event.preventDefault();

                    loadStudents();
                }
            }
        );

    }
);

</script>

@endsection