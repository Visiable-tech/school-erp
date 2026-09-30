@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Fee Waiver Assignment</h4>
            <small class="text-muted">
                Assign a full, fixed or percentage waiver to eligible student fee dues.
            </small>
        </div>

        <a href="{{ route('fee-waiver-assignments.index') }}"
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
            <strong>Please check the following:</strong>

            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    <form method="POST"
          action="{{ route('fee-waiver-assignments.store') }}"
          id="waiverForm">

        @csrf

        <div class="card shadow-sm mb-4">

            <div class="card-header bg-white">
                <strong>Student Information</strong>
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

                                <option value="{{ $year->id }}"
                                    {{ old('academic_year_id') == $year->id ? 'selected' : '' }}>

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
                               id="student_id"
                               value="{{ old('student_id') }}">

                        <input type="hidden"
                               name="student_enrollment_id"
                               id="student_enrollment_id"
                               value="{{ old('student_enrollment_id') }}">

                    </div>

                </div>

            </div>

        </div>


        <div class="card shadow-sm mb-4">

            <div class="card-header bg-white">
                <strong>Waiver Details</strong>
            </div>

            <div class="card-body">

                <div class="row g-3">

                    <div class="col-md-4">

                        <label class="form-label">
                            Assignment Date
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
                            <span class="text-danger">*</span>
                        </label>

                        <select name="waiver_mode"
                                id="waiver_mode"
                                class="form-select"
                                required>

                            <option value="full">
                                Full Waiver
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
                               step="0.01"
                               min="0"
                               name="waiver_value"
                               id="waiver_value"
                               class="form-control">

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


        <div class="card shadow-sm mb-4">

            <div class="card-header bg-white d-flex justify-content-between">

                <strong>
                    Eligible Fee Dues
                </strong>

                <span id="selectedSummary"
                      class="text-muted">
                    Selected: ₹0.00
                </span>

            </div>


            <div class="card-body">

                <div id="dueMessage"
                     class="alert alert-info mb-0">

                    Select academic year and student to load eligible fee dues.

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

                                <th>Fee Component</th>

                                <th>Installment</th>

                                <th class="text-end">
                                    Base
                                </th>

                                <th class="text-end">
                                    Concession
                                </th>

                                <th class="text-end">
                                    Existing Waiver
                                </th>

                                <th class="text-end">
                                    Available
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
                Submit Waiver Request

            </button>

        </div>

    </form>

</div>


<script>

document.addEventListener('DOMContentLoaded', function () {

    const yearSelect =
        document.getElementById('academic_year_id');

    const studentSelect =
        document.getElementById('student_selector');

    const studentId =
        document.getElementById('student_id');

    const enrollmentId =
        document.getElementById('student_enrollment_id');

    const duesBody =
        document.getElementById('duesBody');

    const duesTableBox =
        document.getElementById('duesTableBox');

    const dueMessage =
        document.getElementById('dueMessage');

    const waiverMode =
        document.getElementById('waiver_mode');

    const waiverValueBox =
        document.getElementById('waiverValueBox');

    const waiverValue =
        document.getElementById('waiver_value');

    const waiverValueLabel =
        document.getElementById('waiverValueLabel');

    const checkAll =
        document.getElementById('checkAll');

    const selectedSummary =
        document.getElementById('selectedSummary');


    function money(value) {

        return Number(value || 0).toLocaleString(
            'en-IN',
            {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }
        );
    }


    function updateWaiverMode() {

        if (waiverMode.value === 'full') {

            waiverValueBox.style.display = 'none';

            waiverValue.value = '';

            waiverValue.removeAttribute('required');

        } else {

            waiverValueBox.style.display = '';

            waiverValue.setAttribute(
                'required',
                'required'
            );

            if (
                waiverMode.value ===
                'percentage'
            ) {

                waiverValueLabel.textContent =
                    'Percentage (%)';

                waiverValue.max = 100;

            } else {

                waiverValueLabel.textContent =
                    'Fixed Waiver Amount';

                waiverValue.removeAttribute('max');

            }
        }
    }


    function updateSelectedSummary() {

        let total = 0;

        document
            .querySelectorAll('.due-check:checked')
            .forEach(function (checkbox) {

                total += Number(
                    checkbox.dataset.available || 0
                );

            });

        selectedSummary.textContent =
            'Selected Eligible Amount: ₹'
            + money(total);
    }


    function loadStudents() {

        const yearId = yearSelect.value;

        studentId.value = '';
        enrollmentId.value = '';

        duesBody.innerHTML = '';

        duesTableBox.style.display = 'none';

        dueMessage.style.display = '';

        dueMessage.textContent =
            'Select a student to load eligible fee dues.';

        if (!yearId) {

            studentSelect.disabled = true;

            studentSelect.innerHTML =
                '<option value="">Select academic year first</option>';

            return;
        }

        studentSelect.disabled = true;

        studentSelect.innerHTML =
            '<option value="">Loading students...</option>';

        fetch(
            "{{ route('fee-waiver-assignments.students') }}"
            + '?academic_year_id='
            + encodeURIComponent(yearId)
        )
        .then(response => response.json())
        .then(data => {

            studentSelect.innerHTML =
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

                option.textContent = label;

                studentSelect.appendChild(
                    option
                );
            });

            studentSelect.disabled = false;

        })
        .catch(() => {

            studentSelect.innerHTML =
                '<option value="">Unable to load students</option>';

        });
    }


    function loadDues() {

        const value =
            studentSelect.value;

        if (!value) {

            studentId.value = '';
            enrollmentId.value = '';

            return;
        }

        const parts =
            value.split('|');

        studentId.value =
            parts[0];

        enrollmentId.value =
            parts[1];

        dueMessage.style.display = '';

        dueMessage.className =
            'alert alert-info mb-0';

        dueMessage.textContent =
            'Loading eligible fee dues...';

        duesTableBox.style.display =
            'none';

        const params =
            new URLSearchParams({
                academic_year_id:
                    yearSelect.value,

                student_id:
                    studentId.value,

                student_enrollment_id:
                    enrollmentId.value
            });

        fetch(
            "{{ route('fee-waiver-assignments.dues') }}"
            + '?'
            + params.toString()
        )
        .then(response => response.json())
        .then(data => {

            duesBody.innerHTML = '';

            checkAll.checked = false;

            if (!data.length) {

                dueMessage.className =
                    'alert alert-warning mb-0';

                dueMessage.textContent =
                    'No eligible unpaid fee dues found. Check that the Fee Component has Allow Waiver enabled.';

                return;
            }

            data.forEach(function (due) {

                const row =
                    document.createElement('tr');

                row.innerHTML = `

                    <td>

                        <input
                            type="checkbox"
                            class="form-check-input due-check"
                            name="due_ids[]"
                            value="${due.id}"
                            data-available="${due.available_for_waiver}"
                        >

                    </td>

                    <td>
                        ${due.fee_head}
                    </td>

                    <td>
                        ${due.installment}
                    </td>

                    <td class="text-end">
                        ₹${money(due.base_amount)}
                    </td>

                    <td class="text-end">
                        ₹${money(due.discount_amount)}
                    </td>

                    <td class="text-end">
                        ₹${money(due.existing_waiver)}
                    </td>

                    <td class="text-end fw-semibold">
                        ₹${money(due.available_for_waiver)}
                    </td>
                `;

                duesBody.appendChild(row);
            });

            dueMessage.style.display =
                'none';

            duesTableBox.style.display =
                '';

            document
                .querySelectorAll('.due-check')
                .forEach(function (checkbox) {

                    checkbox.addEventListener(
                        'change',
                        updateSelectedSummary
                    );

                });

            updateSelectedSummary();

        })
        .catch(() => {

            dueMessage.className =
                'alert alert-danger mb-0';

            dueMessage.textContent =
                'Unable to load fee dues.';

        });
    }


    yearSelect.addEventListener(
        'change',
        loadStudents
    );

    studentSelect.addEventListener(
        'change',
        loadDues
    );

    waiverMode.addEventListener(
        'change',
        updateWaiverMode
    );


    checkAll.addEventListener(
        'change',
        function () {

            document
                .querySelectorAll('.due-check')
                .forEach(function (checkbox) {

                    checkbox.checked =
                        checkAll.checked;

                });

            updateSelectedSummary();
        }
    );


    updateWaiverMode();

});

</script>

@endsection