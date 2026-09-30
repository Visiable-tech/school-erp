@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h4 class="mb-1">
                Fine Waiver Assignment
            </h4>

            <small class="text-muted">
                Waive full or partial outstanding fines for a student.
            </small>
        </div>

        <a href="{{ route('fine-waiver-assignments.index') }}"
           class="btn btn-outline-secondary">

            <i class="bi bi-arrow-left"></i>
            Back

        </a>

    </div>


    @if(session('error'))

        <div class="alert alert-danger">
            {{ session('error') }}
        </div>

    @endif


    @if($errors->any())

        <div class="alert alert-danger">

            <strong>
                Please check the following:
            </strong>

            <ul class="mb-0 mt-2">

                @foreach($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    <form method="POST"
          action="{{ route('fine-waiver-assignments.store') }}">

        @csrf


        {{-- STUDENT --}}

        <div class="card shadow-sm mb-4">

            <div class="card-header bg-white">
                <strong>
                    Student Information
                </strong>
            </div>

            <div class="card-body">

                <div class="row g-3">

                    <div class="col-md-4">

                        <label class="form-label">
                            Academic Year
                            <span class="text-danger">*</span>
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
                            Student
                            <span class="text-danger">*</span>
                        </label>

                        <select id="student_selector"
                                class="form-select"
                                disabled>

                            <option value="">
                                Select academic year first
                            </option>

                        </select>

                        <input type="hidden"
                               name="student_id"
                               id="student_id">

                        <input type="hidden"
                               name="student_enrollment_id"
                               id="student_enrollment_id">

                    </div>

                </div>

            </div>

        </div>


        {{-- WAIVER --}}

        <div class="card shadow-sm mb-4">

            <div class="card-header bg-white">
                <strong>
                    Fine Waiver Details
                </strong>
            </div>

            <div class="card-body">

                <div class="row g-3">

                    <div class="col-md-4">

                        <label class="form-label">
                            Date
                            <span class="text-danger">*</span>
                        </label>

                        <input type="date"
                               name="assigned_date"
                               class="form-control"
                               value="{{ old('assigned_date', date('Y-m-d')) }}"
                               required>

                    </div>


                    <div class="col-md-4">

                        <label class="form-label">
                            Waiver Mode
                        </label>

                        <select name="waiver_mode"
                                id="waiver_mode"
                                class="form-select"
                                required>

                            <option value="full">
                                Full Fine Waiver
                            </option>

                            <option value="fixed">
                                Fixed Amount
                            </option>

                            <option value="percentage">
                                Percentage
                            </option>

                        </select>

                    </div>


                    <div class="col-md-4"
                         id="waiverValueBox"
                         style="display:none;">

                        <label class="form-label"
                               id="waiverValueLabel">

                            Waiver Value

                        </label>

                        <input type="number"
                               name="waiver_value"
                               id="waiver_value"
                               class="form-control"
                               min="0"
                               step="0.01">

                    </div>


                    <div class="col-md-12">

                        <label class="form-label">
                            Reason
                            <span class="text-danger">*</span>
                        </label>

                        <textarea name="reason"
                                  class="form-control"
                                  rows="3"
                                  required>{{ old('reason') }}</textarea>

                    </div>

                </div>

            </div>

        </div>


        {{-- FINES --}}

        <div class="card shadow-sm mb-4">

            <div class="card-header bg-white d-flex justify-content-between">

                <strong>
                    Outstanding Fines
                </strong>

                <span id="selectedSummary"
                      class="text-muted">

                    Selected Fine: ₹0.00

                </span>

            </div>


            <div class="card-body">

                <div id="dueMessage"
                     class="alert alert-info mb-0">

                    Select academic year and student.

                </div>


                <div class="table-responsive"
                     id="duesTableBox"
                     style="display:none;">

                    <table class="table table-bordered align-middle">

                        <thead class="table-light">

                            <tr>

                                <th width="50">

                                    <input type="checkbox"
                                           id="checkAll">

                                </th>

                                <th>
                                    Fee Component
                                </th>

                                <th>
                                    Installment
                                </th>

                                <th class="text-end">
                                    Fine
                                </th>

                                <th class="text-end">
                                    Already Waived
                                </th>

                                <th class="text-end">
                                    Available Fine
                                </th>

                            </tr>

                        </thead>

                        <tbody id="duesBody"></tbody>

                    </table>

                </div>

            </div>

        </div>


        <div class="text-end">

            <button type="submit"
                    class="btn btn-primary px-4">

                <i class="bi bi-check-circle"></i>

                Submit Fine Waiver

            </button>

        </div>

    </form>

</div>


<script>

document.addEventListener('DOMContentLoaded', function () {

    const year =
        document.getElementById('academic_year_id');

    const student =
        document.getElementById('student_selector');

    const studentId =
        document.getElementById('student_id');

    const enrollmentId =
        document.getElementById('student_enrollment_id');

    const body =
        document.getElementById('duesBody');

    const tableBox =
        document.getElementById('duesTableBox');

    const message =
        document.getElementById('dueMessage');

    const checkAll =
        document.getElementById('checkAll');

    const summary =
        document.getElementById('selectedSummary');

    const mode =
        document.getElementById('waiver_mode');

    const valueBox =
        document.getElementById('waiverValueBox');

    const waiverValue =
        document.getElementById('waiver_value');

    const valueLabel =
        document.getElementById('waiverValueLabel');


    function money(value) {

        return Number(value || 0).toLocaleString(
            'en-IN',
            {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }
        );
    }


    function updateMode() {

        if (mode.value === 'full') {

            valueBox.style.display = 'none';

            waiverValue.value = '';

            waiverValue.removeAttribute('required');

        } else {

            valueBox.style.display = '';

            waiverValue.setAttribute(
                'required',
                'required'
            );

            if (mode.value === 'percentage') {

                valueLabel.innerText =
                    'Percentage (%)';

                waiverValue.max = 100;

            } else {

                valueLabel.innerText =
                    'Fixed Amount';

                waiverValue.removeAttribute(
                    'max'
                );
            }
        }
    }


    function updateSummary() {

        let total = 0;

        document
            .querySelectorAll('.fine-check:checked')
            .forEach(function (item) {

                total += Number(
                    item.dataset.available || 0
                );

            });

        summary.innerText =
            'Selected Fine: ₹'
            + money(total);
    }


    year.addEventListener(
        'change',
        function () {

            studentId.value = '';
            enrollmentId.value = '';

            tableBox.style.display = 'none';

            message.style.display = '';

            student.disabled = true;

            student.innerHTML =
                '<option>Loading...</option>';

            if (!year.value) {

                student.innerHTML =
                    '<option>Select academic year first</option>';

                return;
            }

            fetch(
                "{{ route('fine-waiver-assignments.students') }}"
                +
                '?academic_year_id='
                +
                encodeURIComponent(year.value)
            )
            .then(response => response.json())
            .then(data => {

                student.innerHTML =
                    '<option value="">Select Student</option>';

                data.forEach(function (row) {

                    const option =
                        document.createElement('option');

                    option.value =
                        row.student_id
                        + '|'
                        + row.enrollment_id;

                    let label =
                        row.student_name
                        + ' - '
                        + row.admission_no;

                    if (row.class_name) {

                        label +=
                            ' - '
                            + row.class_name;
                    }

                    if (row.section_name) {

                        label +=
                            ' / '
                            + row.section_name;
                    }

                    option.innerText =
                        label;

                    student.appendChild(
                        option
                    );
                });

                student.disabled = false;
            });
        }
    );


    student.addEventListener(
        'change',
        function () {

            if (!student.value) {
                return;
            }

            const parts =
                student.value.split('|');

            studentId.value =
                parts[0];

            enrollmentId.value =
                parts[1];

            message.style.display = '';

            message.className =
                'alert alert-info mb-0';

            message.innerText =
                'Loading outstanding fines...';

            tableBox.style.display =
                'none';

            const params =
                new URLSearchParams({

                    academic_year_id:
                        year.value,

                    student_id:
                        studentId.value,

                    student_enrollment_id:
                        enrollmentId.value

                });

            fetch(
                "{{ route('fine-waiver-assignments.dues') }}"
                + '?'
                + params.toString()
            )
            .then(response => response.json())
            .then(data => {

                body.innerHTML = '';

                checkAll.checked = false;

                if (!data.length) {

                    message.className =
                        'alert alert-warning mb-0';

                    message.innerText =
                        'No outstanding fine found for this student.';

                    return;
                }

                data.forEach(function (due) {

                    const row =
                        document.createElement('tr');

                    row.innerHTML = `

                        <td>

                            <input
                                type="checkbox"
                                name="due_ids[]"
                                value="${due.id}"
                                class="form-check-input fine-check"
                                data-available="${due.available_fine}"
                            >

                        </td>

                        <td>
                            ${due.fee_head}
                        </td>

                        <td>
                            ${due.installment}
                        </td>

                        <td class="text-end">
                            ₹${money(due.fine_amount)}
                        </td>

                        <td class="text-end">
                            ₹${money(due.fine_waiver_amount)}
                        </td>

                        <td class="text-end fw-semibold">
                            ₹${money(due.available_fine)}
                        </td>

                    `;

                    body.appendChild(row);
                });

                message.style.display =
                    'none';

                tableBox.style.display =
                    '';

                document
                    .querySelectorAll('.fine-check')
                    .forEach(function (item) {

                        item.addEventListener(
                            'change',
                            updateSummary
                        );
                    });

                updateSummary();
            });
        }
    );


    checkAll.addEventListener(
        'change',
        function () {

            document
                .querySelectorAll('.fine-check')
                .forEach(function (item) {

                    item.checked =
                        checkAll.checked;

                });

            updateSummary();
        }
    );


    mode.addEventListener(
        'change',
        updateMode
    );


    updateMode();

});

</script>

@endsection