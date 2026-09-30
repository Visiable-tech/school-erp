@extends('layouts.admin')

@section('title', 'Composite Concession')

@section('content')

<div class="container-fluid">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h4 class="mb-1">
                Composite Concession
            </h4>

            <small class="text-muted">
                Apply concession, fee waiver and fine waiver
                through one approval request.
            </small>
        </div>

        <a href="{{ route('composite-concessions.index') }}"
           class="btn btn-outline-secondary">

            <i class="bi bi-arrow-left"></i>
            Back

        </a>

    </div>


    {{-- Error --}}
    @if(session('error'))

        <div class="alert alert-danger">
            {{ session('error') }}
        </div>

    @endif


    <form method="POST"
          action="{{ route('composite-concessions.store') }}"
          id="compositeForm">

        @csrf


        {{-- ========================================================= --}}
        {{-- STUDENT --}}
        {{-- ========================================================= --}}

        <div class="card shadow-sm mb-4">

            <div class="card-header bg-light">

                <strong>
                    <i class="bi bi-person"></i>
                    Student Information
                </strong>

            </div>


            <div class="card-body">

                <div class="row g-3">


                    {{-- Academic Year --}}

                    <div class="col-md-4">

                        <label class="form-label">
                            Academic Year
                            <span class="text-danger">*</span>
                        </label>

                        <select name="academic_year_id"
                                id="academic_year_id"
                                class="form-select @error('academic_year_id') is-invalid @enderror"
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

                        @error('academic_year_id')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- Student --}}

                    <div class="col-md-5">

                        <label class="form-label">
                            Student
                            <span class="text-danger">*</span>
                        </label>

                        <select id="student_selector"
                                class="form-select"
                                disabled>

                            <option value="">
                                Select Academic Year First
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


                    {{-- Assigned Date --}}

                    <div class="col-md-3">

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

                </div>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- CONCESSION --}}
        {{-- ========================================================= --}}

        <div class="card shadow-sm mb-4">

            <div class="card-header">

                <strong>
                    <i class="bi bi-percent"></i>
                    Regular Concession
                </strong>

            </div>


            <div class="card-body">

                <div class="row g-3">

                    <div class="col-md-6">

                        <label class="form-label">
                            Concession Mode
                        </label>

                        <select name="concession_mode"
                                id="concession_mode"
                                class="form-select">

                            <option value="none">
                                No Concession
                            </option>

                            <option value="fixed"
                                {{ old('concession_mode') == 'fixed' ? 'selected' : '' }}>
                                Fixed Amount
                            </option>

                            <option value="percentage"
                                {{ old('concession_mode') == 'percentage' ? 'selected' : '' }}>
                                Percentage
                            </option>

                        </select>

                    </div>


                    <div class="col-md-6"
                         id="concessionValueBox"
                         style="display:none;">

                        <label class="form-label"
                               id="concessionValueLabel">

                            Value

                        </label>

                        <input type="number"
                               step="0.01"
                               min="0"
                               name="concession_value"
                               id="concession_value"
                               class="form-control"
                               value="{{ old('concession_value') }}">

                    </div>

                </div>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- FEE WAIVER --}}
        {{-- ========================================================= --}}

        <div class="card shadow-sm mb-4">

            <div class="card-header">

                <strong>
                    <i class="bi bi-cash-stack"></i>
                    Fee Waiver
                </strong>

            </div>


            <div class="card-body">

                <div class="row g-3">

                    <div class="col-md-6">

                        <label class="form-label">
                            Fee Waiver Mode
                        </label>

                        <select name="fee_waiver_mode"
                                id="fee_waiver_mode"
                                class="form-select">

                            <option value="none">
                                No Fee Waiver
                            </option>

                            <option value="full"
                                {{ old('fee_waiver_mode') == 'full' ? 'selected' : '' }}>
                                Full Waiver
                            </option>

                            <option value="fixed"
                                {{ old('fee_waiver_mode') == 'fixed' ? 'selected' : '' }}>
                                Fixed Amount
                            </option>

                            <option value="percentage"
                                {{ old('fee_waiver_mode') == 'percentage' ? 'selected' : '' }}>
                                Percentage
                            </option>

                        </select>

                    </div>


                    <div class="col-md-6"
                         id="feeWaiverValueBox"
                         style="display:none;">

                        <label class="form-label"
                               id="feeWaiverValueLabel">

                            Value

                        </label>

                        <input type="number"
                               step="0.01"
                               min="0"
                               name="fee_waiver_value"
                               id="fee_waiver_value"
                               class="form-control"
                               value="{{ old('fee_waiver_value') }}">

                    </div>

                </div>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- FINE WAIVER --}}
        {{-- ========================================================= --}}

        <div class="card shadow-sm mb-4">

            <div class="card-header">

                <strong>
                    <i class="bi bi-receipt"></i>
                    Fine Waiver
                </strong>

            </div>


            <div class="card-body">

                <div class="row g-3">

                    <div class="col-md-6">

                        <label class="form-label">
                            Fine Waiver Mode
                        </label>

                        <select name="fine_waiver_mode"
                                id="fine_waiver_mode"
                                class="form-select">

                            <option value="none">
                                No Fine Waiver
                            </option>

                            <option value="full"
                                {{ old('fine_waiver_mode') == 'full' ? 'selected' : '' }}>
                                Full Fine Waiver
                            </option>

                            <option value="fixed"
                                {{ old('fine_waiver_mode') == 'fixed' ? 'selected' : '' }}>
                                Fixed Amount
                            </option>

                            <option value="percentage"
                                {{ old('fine_waiver_mode') == 'percentage' ? 'selected' : '' }}>
                                Percentage
                            </option>

                        </select>

                    </div>


                    <div class="col-md-6"
                         id="fineWaiverValueBox"
                         style="display:none;">

                        <label class="form-label"
                               id="fineWaiverValueLabel">

                            Value

                        </label>

                        <input type="number"
                               step="0.01"
                               min="0"
                               name="fine_waiver_value"
                               id="fine_waiver_value"
                               class="form-control"
                               value="{{ old('fine_waiver_value') }}">

                    </div>

                </div>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- DUES --}}
        {{-- ========================================================= --}}

        <div class="card shadow-sm mb-4">

            <div class="card-header d-flex justify-content-between align-items-center">

                <strong>
                    <i class="bi bi-list-check"></i>
                    Select Fee Dues
                </strong>

                <div>

                    <input type="checkbox"
                           id="checkAll"
                           class="form-check-input">

                    <label for="checkAll"
                           class="form-check-label">

                        Select All

                    </label>

                </div>

            </div>


            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-bordered table-hover mb-0 align-middle">

                        <thead class="table-light">

                            <tr>

                                <th width="50">
                                    #
                                </th>

                                <th>
                                    Fee Component
                                </th>

                                <th>
                                    Installment
                                </th>

                                <th class="text-end">
                                    Base
                                </th>

                                <th class="text-end">
                                    Concession
                                </th>

                                <th class="text-end">
                                    Fee Waiver
                                </th>

                                <th class="text-end">
                                    Fine
                                </th>

                                <th class="text-end">
                                    Fine Waiver
                                </th>

                                <th class="text-end">
                                    Available Fee
                                </th>

                                <th class="text-end">
                                    Available Fine
                                </th>

                            </tr>

                        </thead>


                        <tbody id="dueTableBody">

                            <tr>

                                <td colspan="10"
                                    class="text-center text-muted py-4">

                                    Select an academic year and student.

                                </td>

                            </tr>

                        </tbody>


                        <tfoot>

                            <tr class="table-light">

                                <th colspan="8"
                                    class="text-end">

                                    Selected Available Fee

                                </th>

                                <th class="text-end"
                                    id="selectedFeeTotal">

                                    ₹0.00

                                </th>

                                <th></th>

                            </tr>


                            <tr class="table-light">

                                <th colspan="9"
                                    class="text-end">

                                    Selected Available Fine

                                </th>

                                <th class="text-end"
                                    id="selectedFineTotal">

                                    ₹0.00

                                </th>

                            </tr>

                        </tfoot>

                    </table>

                </div>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- REASON --}}
        {{-- ========================================================= --}}

        <div class="card shadow-sm mb-4">

            <div class="card-body">

                <label class="form-label">
                    Reason
                    <span class="text-danger">*</span>
                </label>

                <textarea name="reason"
                          rows="4"
                          maxlength="2000"
                          class="form-control"
                          required>{{ old('reason') }}</textarea>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- SUBMIT --}}
        {{-- ========================================================= --}}

        <div class="d-flex justify-content-end gap-2">

            <a href="{{ route('composite-concessions.index') }}"
               class="btn btn-light">

                Cancel

            </a>

            <button type="submit"
                    class="btn btn-primary">

                <i class="bi bi-check-circle"></i>
                Create Approval Request

            </button>

        </div>

    </form>

</div>


<script>
document.addEventListener('DOMContentLoaded', function () {

    const yearSelect =
        document.getElementById('academic_year_id');

    const studentSelector =
        document.getElementById('student_selector');

    const studentId =
        document.getElementById('student_id');

    const enrollmentId =
        document.getElementById('student_enrollment_id');

    const dueBody =
        document.getElementById('dueTableBody');

    const checkAll =
        document.getElementById('checkAll');


    /*
    |--------------------------------------------------------------------------
    | Currency
    |--------------------------------------------------------------------------
    */

    function money(value) {

        return '₹' +
            Number(value || 0).toLocaleString(
                'en-IN',
                {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Toggle Value Boxes
    |--------------------------------------------------------------------------
    */

    function setupMode(
        selectId,
        boxId,
        labelId
    ) {

        const select =
            document.getElementById(selectId);

        const box =
            document.getElementById(boxId);

        const label =
            document.getElementById(labelId);


        function update() {

            const mode = select.value;

            if (
                mode === 'none'
                ||
                mode === 'full'
            ) {

                box.style.display = 'none';

            } else {

                box.style.display = '';

                label.textContent =
                    mode === 'percentage'
                        ? 'Percentage (%)'
                        : 'Amount';
            }
        }


        select.addEventListener(
            'change',
            update
        );

        update();
    }


    setupMode(
        'concession_mode',
        'concessionValueBox',
        'concessionValueLabel'
    );

    setupMode(
        'fee_waiver_mode',
        'feeWaiverValueBox',
        'feeWaiverValueLabel'
    );

    setupMode(
        'fine_waiver_mode',
        'fineWaiverValueBox',
        'fineWaiverValueLabel'
    );


    /*
    |--------------------------------------------------------------------------
    | Academic Year
    |--------------------------------------------------------------------------
    */

    yearSelect.addEventListener(
        'change',
        loadStudents
    );


    async function loadStudents() {

        studentId.value = '';
        enrollmentId.value = '';

        dueBody.innerHTML = `
            <tr>
                <td colspan="10"
                    class="text-center text-muted py-4">
                    Select a student.
                </td>
            </tr>
        `;


        if (!yearSelect.value) {

            studentSelector.disabled = true;

            studentSelector.innerHTML = `
                <option value="">
                    Select Academic Year First
                </option>
            `;

            return;
        }


        studentSelector.disabled = true;

        studentSelector.innerHTML = `
            <option value="">
                Loading Students...
            </option>
        `;


        try {

            const url =
                "{{ route('composite-concessions.students') }}"
                +
                "?academic_year_id="
                +
                encodeURIComponent(
                    yearSelect.value
                );


            const response =
                await fetch(
                    url,
                    {
                        headers: {
                            'Accept':
                                'application/json'
                        }
                    }
                );


            if (!response.ok) {
                throw new Error(
                    'Unable to load students.'
                );
            }


            const students =
                await response.json();


            studentSelector.innerHTML = `
                <option value="">
                    Select Student
                </option>
            `;


            students.forEach(function (row) {

                const option =
                    document.createElement(
                        'option'
                    );


                option.value =
                    row.student_id;


                option.dataset.enrollment =
                    row.enrollment_id;


                option.textContent =
                    row.student_name
                    +
                    (
                        row.admission_no
                            ? ' - ' +
                              row.admission_no
                            : ''
                    )
                    +
                    (
                        row.class_name
                            ? ' | ' +
                              row.class_name
                            : ''
                    )
                    +
                    (
                        row.section_name
                            ? ' - ' +
                              row.section_name
                            : ''
                    );


                studentSelector.appendChild(
                    option
                );
            });


            studentSelector.disabled = false;

        } catch (error) {

            studentSelector.innerHTML = `
                <option value="">
                    Unable to load students
                </option>
            `;

            studentSelector.disabled = true;

            console.error(error);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Student Change
    |--------------------------------------------------------------------------
    */

    studentSelector.addEventListener(
        'change',
        function () {

            const option =
                studentSelector.options[
                    studentSelector.selectedIndex
                ];


            studentId.value =
                studentSelector.value || '';


            enrollmentId.value =
                option
                    ? (
                        option.dataset.enrollment
                        || ''
                    )
                    : '';


            loadDues();
        }
    );


    /*
    |--------------------------------------------------------------------------
    | Load Dues
    |--------------------------------------------------------------------------
    */

    async function loadDues() {

        checkAll.checked = false;

        updateTotals();


        if (
            !yearSelect.value
            ||
            !studentId.value
            ||
            !enrollmentId.value
        ) {

            dueBody.innerHTML = `
                <tr>
                    <td colspan="10"
                        class="text-center text-muted py-4">
                        Select a student.
                    </td>
                </tr>
            `;

            return;
        }


        dueBody.innerHTML = `
            <tr>
                <td colspan="10"
                    class="text-center py-4">
                    Loading fee dues...
                </td>
            </tr>
        `;


        try {

            const params =
                new URLSearchParams({
                    academic_year_id:
                        yearSelect.value,

                    student_id:
                        studentId.value,

                    student_enrollment_id:
                        enrollmentId.value
                });


            const response =
                await fetch(
                    "{{ route('composite-concessions.dues') }}"
                    +
                    "?"
                    +
                    params.toString(),
                    {
                        headers: {
                            'Accept':
                                'application/json'
                        }
                    }
                );


            if (!response.ok) {

                throw new Error(
                    'Unable to load fee dues.'
                );
            }


            const dues =
                await response.json();


            if (!dues.length) {

                dueBody.innerHTML = `
                    <tr>
                        <td colspan="10"
                            class="text-center text-muted py-4">
                            No unpaid fee dues are available.
                        </td>
                    </tr>
                `;

                return;
            }


            dueBody.innerHTML = '';


            dues.forEach(function (due) {

                const tr =
                    document.createElement(
                        'tr'
                    );


                tr.innerHTML = `

                    <td>

                        <input type="checkbox"
                               class="form-check-input due-checkbox"
                               name="due_ids[]"
                               value="${due.id}"
                               data-fee="${due.remaining_fee}"
                               data-fine="${due.remaining_fine}">

                    </td>


                    <td>

                        ${escapeHtml(due.fee_head)}

                        ${
                            !due.allow_concession
                            ?
                            `
                            <span class="badge bg-secondary ms-1">
                                No Concession
                            </span>
                            `
                            :
                            ''
                        }

                        ${
                            !due.allow_waiver
                            ?
                            `
                            <span class="badge bg-warning text-dark ms-1">
                                No Fee Waiver
                            </span>
                            `
                            :
                            ''
                        }

                    </td>


                    <td>
                        ${escapeHtml(due.installment)}
                    </td>


                    <td class="text-end">
                        ${money(due.base_amount)}
                    </td>


                    <td class="text-end">
                        ${money(due.discount_amount)}
                    </td>


                    <td class="text-end">
                        ${money(due.waiver_amount)}
                    </td>


                    <td class="text-end">
                        ${money(due.fine_amount)}
                    </td>


                    <td class="text-end">
                        ${money(due.fine_waiver_amount)}
                    </td>


                    <td class="text-end fw-semibold">
                        ${money(due.remaining_fee)}
                    </td>


                    <td class="text-end fw-semibold">
                        ${money(due.remaining_fine)}
                    </td>
                `;


                dueBody.appendChild(tr);
            });


            bindDueCheckboxes();

        } catch (error) {

            dueBody.innerHTML = `
                <tr>
                    <td colspan="10"
                        class="text-center text-danger py-4">
                        Unable to load fee dues.
                    </td>
                </tr>
            `;

            console.error(error);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Check All
    |--------------------------------------------------------------------------
    */

    checkAll.addEventListener(
        'change',
        function () {

            document
                .querySelectorAll(
                    '.due-checkbox'
                )
                .forEach(
                    function (checkbox) {

                        checkbox.checked =
                            checkAll.checked;
                    }
                );


            updateTotals();
        }
    );


    function bindDueCheckboxes() {

        document
            .querySelectorAll(
                '.due-checkbox'
            )
            .forEach(
                function (checkbox) {

                    checkbox.addEventListener(
                        'change',
                        updateTotals
                    );
                }
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Totals
    |--------------------------------------------------------------------------
    */

    function updateTotals() {

        let fee = 0;
        let fine = 0;


        document
            .querySelectorAll(
                '.due-checkbox:checked'
            )
            .forEach(
                function (checkbox) {

                    fee +=
                        Number(
                            checkbox.dataset.fee
                            || 0
                        );


                    fine +=
                        Number(
                            checkbox.dataset.fine
                            || 0
                        );
                }
            );


        document.getElementById(
            'selectedFeeTotal'
        ).textContent =
            money(fee);


        document.getElementById(
            'selectedFineTotal'
        ).textContent =
            money(fine);
    }


    /*
    |--------------------------------------------------------------------------
    | Escape
    |--------------------------------------------------------------------------
    */

    function escapeHtml(value) {

        const div =
            document.createElement('div');

        div.textContent =
            value || '';

        return div.innerHTML;
    }


    /*
    |--------------------------------------------------------------------------
    | Submit validation
    |--------------------------------------------------------------------------
    */

    document
        .getElementById('compositeForm')
        .addEventListener(
            'submit',
            function (event) {

                const selected =
                    document.querySelectorAll(
                        '.due-checkbox:checked'
                    );


                if (!studentId.value) {

                    event.preventDefault();

                    alert(
                        'Please select a student.'
                    );

                    return;
                }


                if (!selected.length) {

                    event.preventDefault();

                    alert(
                        'Please select at least one fee due.'
                    );

                    return;
                }


                const concession =
                    document.getElementById(
                        'concession_mode'
                    ).value;


                const feeWaiver =
                    document.getElementById(
                        'fee_waiver_mode'
                    ).value;


                const fineWaiver =
                    document.getElementById(
                        'fine_waiver_mode'
                    ).value;


                if (
                    concession === 'none'
                    &&
                    feeWaiver === 'none'
                    &&
                    fineWaiver === 'none'
                ) {

                    event.preventDefault();

                    alert(
                        'Please select at least one concession or waiver.'
                    );
                }
            }
        );

});
</script>

@endsection