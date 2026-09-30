@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <div>
            <h4 class="mb-1">
                Concession Assignment
            </h4>

            <small class="text-muted">
                Assign fee concession to a student
            </small>
        </div>

        <a href="{{ route('concession-assignments.index') }}"
           class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>

    </div>


    <form method="POST"
          action="{{ route('concession-assignments.store') }}">

        @csrf

        <div class="card border-0 shadow-sm mb-3">

            <div class="card-header bg-white">
                <strong>Student</strong>
            </div>

            <div class="card-body">

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


                    <div class="col-md-5">

                        <label class="form-label">
                            Search Student
                        </label>

                        <input type="text"
                               id="student_search"
                               class="form-control"
                               placeholder="Name / Admission No.">

                    </div>


                    <div class="col-md-3 d-flex align-items-end">

                        <button type="button"
                                id="searchStudent"
                                class="btn btn-outline-primary w-100">

                            Search Student

                        </button>

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
                                Select Student
                            </option>

                        </select>

                    </div>

                </div>

            </div>

        </div>


        <div class="card border-0 shadow-sm mb-3">

            <div class="card-header bg-white">
                <strong>Concession Details</strong>
            </div>

            <div class="card-body">

                <div class="row g-3">

                    <div class="col-md-4">

                        <label class="form-label">
                            Concession Type *
                        </label>

                        <select name="concession_type_id"
                                id="concession_type_id"
                                class="form-select"
                                required>

                            <option value="">
                                Select Concession Type
                            </option>

                            @foreach($concessionTypes as $type)

                                <option
                                    value="{{ $type->id }}"
                                    data-mode="{{ $type->concession_mode }}"
                                    data-value="{{ $type->default_value }}"
                                    data-max="{{ $type->maximum_amount }}">

                                    {{ $type->name }}

                                </option>

                            @endforeach

                        </select>

                    </div>


                    <div class="col-md-2">

                        <label class="form-label">
                            Mode *
                        </label>

                        <select name="concession_mode"
                                id="concession_mode"
                                class="form-select"
                                required>

                            <option value="percentage">
                                Percentage
                            </option>

                            <option value="fixed">
                                Fixed
                            </option>

                        </select>

                    </div>


                    <div class="col-md-2">

                        <label class="form-label">
                            Value *
                        </label>

                        <input type="number"
                               name="concession_value"
                               id="concession_value"
                               class="form-control"
                               min="0.01"
                               step="0.01"
                               required>

                    </div>


                    <div class="col-md-2">

                        <label class="form-label">
                            Maximum Amount
                        </label>

                        <input type="number"
                               name="maximum_amount"
                               id="maximum_amount"
                               class="form-control"
                               min="0"
                               step="0.01">

                    </div>


                    <div class="col-md-2">

                        <label class="form-label">
                            Assigned Date *
                        </label>

                        <input type="date"
                               name="assigned_date"
                               class="form-control"
                               value="{{ date('Y-m-d') }}"
                               required>

                    </div>


                    <div class="col-md-3">

                        <label class="form-label">
                            Effective From
                        </label>

                        <input type="date"
                               name="effective_from"
                               class="form-control">

                    </div>


                    <div class="col-md-3">

                        <label class="form-label">
                            Effective To
                        </label>

                        <input type="date"
                               name="effective_to"
                               class="form-control">

                    </div>


                    <div class="col-md-6">

                        <label class="form-label">
                            Remarks
                        </label>

                        <input type="text"
                               name="remarks"
                               class="form-control">

                    </div>

                </div>

            </div>

        </div>


        <div class="card border-0 shadow-sm">

            <div class="card-header bg-white d-flex justify-content-between">

                <strong>
                    Apply To Fee Dues
                </strong>

                <button type="button"
                        id="selectAll"
                        class="btn btn-sm btn-outline-primary">

                    Select All

                </button>

            </div>


            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">

                        <tr>
                            <th width="50"></th>
                            <th>Component</th>
                            <th>Installment</th>
                            <th>Due Date</th>
                            <th>Base</th>
                            <th>Existing Concession</th>
                            <th>Balance</th>
                        </tr>

                    </thead>

                    <tbody id="dueRows">

                        <tr>
                            <td colspan="7"
                                class="text-center text-muted py-4">

                                Select a student to load fee dues.

                            </td>
                        </tr>

                    </tbody>

                </table>

            </div>


            <div class="card-footer bg-white text-end">

                <button type="submit"
                        class="btn btn-primary">

                    <i class="bi bi-check-circle"></i>
                    Save Concession

                </button>

            </div>

        </div>

    </form>

</div>


<script>

document.addEventListener('DOMContentLoaded', function () {

    const year =
        document.getElementById('academic_year_id');

    const search =
        document.getElementById('student_search');

    const student =
        document.getElementById('student_enrollment_id');

    const dueRows =
        document.getElementById('dueRows');


    document.getElementById('searchStudent')
        .addEventListener('click', loadStudents);


    async function loadStudents()
    {
        if (!year.value) {
            alert('Select Academic Year first.');
            return;
        }

        const params = new URLSearchParams({
            academic_year_id: year.value,
            search: search.value
        });

        const response = await fetch(
            '{{ route("concession-assignments.students") }}'
            + '?' + params
        );

        const data = await response.json();

        student.innerHTML =
            '<option value="">Select Student</option>';

        data.forEach(function (item) {

            const option =
                document.createElement('option');

            option.value = item.id;

            option.textContent =
                item.name
                + ' | '
                + item.admission_no
                + ' | '
                + item.class
                + ' - '
                + item.section;

            student.appendChild(option);
        });
    }


    student.addEventListener(
        'change',
        loadDues
    );


    async function loadDues()
    {
        dueRows.innerHTML = '';

        if (!student.value) {
            return;
        }

        const params = new URLSearchParams({
            academic_year_id: year.value,
            student_enrollment_id: student.value
        });

        const response = await fetch(
            '{{ route("concession-assignments.dues") }}'
            + '?' + params
        );

        const data = await response.json();


        if (!data.length) {

            dueRows.innerHTML = `
                <tr>
                    <td colspan="7"
                        class="text-center text-muted py-4">
                        No eligible unpaid fee dues found.
                    </td>
                </tr>
            `;

            return;
        }


        data.forEach(function (due) {

            const row =
                document.createElement('tr');

            row.innerHTML = `

                <td>
                    <input type="checkbox"
                           name="due_ids[]"
                           value="${due.id}"
                           class="form-check-input due-check">
                </td>

                <td>
                    ${due.fee_head_name ?? ''}
                </td>

                <td>
                    ${due.installment_name ?? ''}
                </td>

                <td>
                    ${due.due_date ?? ''}
                </td>

                <td>
                    ₹${Number(
                        due.base_amount
                    ).toFixed(2)}
                </td>

                <td>
                    ₹${Number(
                        due.discount_amount
                    ).toFixed(2)}
                </td>

                <td>
                    ₹${Number(
                        due.balance_amount
                    ).toFixed(2)}
                </td>
            `;

            dueRows.appendChild(row);
        });
    }


    document.getElementById('selectAll')
        .addEventListener(
            'click',
            function () {

                document.querySelectorAll(
                    '.due-check'
                ).forEach(function (checkbox) {

                    checkbox.checked = true;
                });
            }
        );


    document.getElementById(
        'concession_type_id'
    ).addEventListener(
        'change',
        function () {

            const option =
                this.options[
                    this.selectedIndex
                ];

            document.getElementById(
                'concession_mode'
            ).value =
                option.dataset.mode
                || 'percentage';

            document.getElementById(
                'concession_value'
            ).value =
                option.dataset.value
                || '';

            document.getElementById(
                'maximum_amount'
            ).value =
                option.dataset.max
                || '';
        }
    );

});

</script>

@endsection