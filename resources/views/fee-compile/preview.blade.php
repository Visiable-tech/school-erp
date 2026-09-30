@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between
                align-items-center mb-3">

        <div>

            <h4 class="mb-1">
                Compile Fee Preview
            </h4>

            <small class="text-muted">
                Review students before generating dues
            </small>

        </div>


        <a href="{{ route(
            'fee-compile.index'
        ) }}"
           class="btn btn-outline-secondary">

            <i class="bi bi-arrow-left"></i>
            Back

        </a>

    </div>


    <div class="row g-3 mb-3">

        <div class="col-md-3">

            <div class="card border-0 shadow-sm">

                <div class="card-body">

                    <small class="text-muted">
                        Academic Year
                    </small>

                    <h6 class="mb-0">
                        {{ $academicYear->name }}
                    </h6>

                </div>

            </div>

        </div>


        <div class="col-md-3">

            <div class="card border-0 shadow-sm">

                <div class="card-body">

                    <small class="text-muted">
                        Class / Section
                    </small>

                    <h6 class="mb-0">

                        {{ $schoolClass->name }}

                        @if($section)
                            / {{ $section->name }}
                        @endif

                    </h6>

                </div>

            </div>

        </div>


        <div class="col-md-3">

            <div class="card border-0 shadow-sm">

                <div class="card-body">

                    <small class="text-muted">
                        Fee Template
                    </small>

                    <h6 class="mb-0">
                        {{ $feeStructure->name }}
                    </h6>

                </div>

            </div>

        </div>


        <div class="col-md-3">

            <div class="card border-0 shadow-sm">

                <div class="card-body">

                    <small class="text-muted">
                        Student Fee
                    </small>

                    <h5 class="mb-0">

                        ₹{{ number_format(
                            $templateTotal,
                            2
                        ) }}

                    </h5>

                </div>

            </div>

        </div>

    </div>


    <form method="POST"
          action="{{ route(
              'fee-compile.process'
          ) }}"
          id="compileForm">

        @csrf


        <input type="hidden"
               name="academic_year_id"
               value="{{ $validated[
                    'academic_year_id'
               ] }}">


        <input type="hidden"
               name="school_class_id"
               value="{{ $validated[
                    'school_class_id'
               ] }}">


        <input type="hidden"
               name="section_id"
               value="{{ $validated[
                    'section_id'
               ] ?? '' }}">


        <input type="hidden"
               name="fee_structure_id"
               value="{{ $validated[
                    'fee_structure_id'
               ] }}">


        <input type="hidden"
               name="compile_date"
               value="{{ $validated[
                    'compile_date'
               ] }}">


        <div class="card border-0 shadow-sm">

            <div class="card-header bg-white">

                <div class="d-flex
                            justify-content-between
                            align-items-center">

                    <div>

                        <strong>
                            Students
                        </strong>

                        <span class="badge bg-secondary ms-2">

                            {{ $enrollments->count() }}

                        </span>

                    </div>


                    <div>

                        <button type="button"
                                id="selectAvailable"
                                class="btn btn-sm
                                       btn-outline-primary">

                            Select Available

                        </button>

                    </div>

                </div>

            </div>


            <div class="table-responsive">

                <table class="table
                              table-hover
                              align-middle
                              mb-0">

                    <thead class="table-light">

                        <tr>

                            <th width="50">

                                <input type="checkbox"
                                       id="selectAll"
                                       class="form-check-input">

                            </th>

                            <th>Student</th>
                            <th>Admission No.</th>
                            <th>Roll No.</th>
                            <th>Section</th>
                            <th>Fee</th>
                            <th>Status</th>

                        </tr>

                    </thead>


                    <tbody>

                    @forelse(
                        $enrollments
                        as $enrollment
                    )

                        @php

                            $compiled =
                                in_array(
                                    (int)
                                    $enrollment->student_id,
                                    $alreadyCompiled,
                                    true
                                );

                        @endphp


                        <tr>

                            <td>

                                @if(!$compiled)

                                    <input type="checkbox"
                                           name="enrollment_ids[]"
                                           value="{{ $enrollment->id }}"
                                           class="form-check-input
                                                  student-checkbox">

                                @else

                                    <i class="bi bi-lock
                                              text-muted"></i>

                                @endif

                            </td>


                            <td>

                                <strong>
                                    {{
                                        $enrollment
                                            ->student
                                            ->name
                                        ?? $enrollment
                                            ->student
                                            ->student_name
                                        ?? 'Student'
                                    }}
                                </strong>

                            </td>


                            <td>

                                {{
                                    $enrollment
                                        ->student
                                        ->admission_no
                                    ?? '—'
                                }}

                            </td>


                            <td>

                                {{
                                    $enrollment
                                        ->roll_no
                                    ?? '—'
                                }}

                            </td>


                            <td>

                                {{
                                    $enrollment
                                        ->section
                                        ->name
                                    ?? '—'
                                }}

                            </td>


                            <td>

                                ₹{{ number_format(
                                    $templateTotal,
                                    2
                                ) }}

                            </td>


                            <td>

                                @if($compiled)

                                    <span class="badge bg-success">
                                        Already Compiled
                                    </span>

                                @else

                                    <span class="badge bg-warning
                                                 text-dark">

                                        Ready

                                    </span>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="7"
                                class="text-center
                                       text-muted py-5">

                                No enrolled students found.

                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>


            @if($enrollments->isNotEmpty())

                <div class="card-footer bg-white">

                    <div class="d-flex
                                justify-content-between
                                align-items-center">

                        <div>

                            Selected:

                            <strong id="selectedCount">
                                0
                            </strong>

                            student(s)

                        </div>


                        <button type="submit"
                                class="btn btn-success"
                                id="compileButton"
                                disabled>

                            <i class="bi bi-calculator"></i>

                            Compile Selected Students

                        </button>

                    </div>

                </div>

            @endif

        </div>

    </form>

</div>


<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const selectAll =
            document.getElementById(
                'selectAll'
            );

        const selectAvailable =
            document.getElementById(
                'selectAvailable'
            );

        const checkboxes =
            document.querySelectorAll(
                '.student-checkbox'
            );

        const count =
            document.getElementById(
                'selectedCount'
            );

        const button =
            document.getElementById(
                'compileButton'
            );


        function updateCount()
        {
            const selected =
                document.querySelectorAll(
                    '.student-checkbox:checked'
                ).length;

            count.textContent =
                selected;

            button.disabled =
                selected === 0;


            if (selectAll) {

                selectAll.checked =
                    checkboxes.length > 0 &&
                    selected ===
                        checkboxes.length;
            }
        }


        if (selectAll) {

            selectAll.addEventListener(
                'change',
                function () {

                    checkboxes.forEach(
                        function (checkbox) {

                            checkbox.checked =
                                selectAll.checked;
                        }
                    );

                    updateCount();
                }
            );
        }


        if (selectAvailable) {

            selectAvailable.addEventListener(
                'click',
                function () {

                    checkboxes.forEach(
                        function (checkbox) {

                            checkbox.checked =
                                true;
                        }
                    );

                    updateCount();
                }
            );
        }


        checkboxes.forEach(
            function (checkbox) {

                checkbox.addEventListener(
                    'change',
                    updateCount
                );
            }
        );


        document
            .getElementById(
                'compileForm'
            )
            .addEventListener(
                'submit',
                function (event) {

                    const selected =
                        document.querySelectorAll(
                            '.student-checkbox:checked'
                        ).length;

                    if (selected === 0) {

                        event.preventDefault();

                        alert(
                            'Please select at least one student.'
                        );

                        return;
                    }


                    if (
                        !confirm(
                            'Compile fee for ' +
                            selected +
                            ' selected student(s)?'
                        )
                    ) {
                        event.preventDefault();
                    }
                }
            );

    }
);

</script>

@endsection