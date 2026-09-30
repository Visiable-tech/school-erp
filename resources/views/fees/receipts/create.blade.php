@extends('layouts.admin')

@section('title', 'Collect Fee')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Collect Fee</h4>
            <small class="text-muted">
                Select a student and collect outstanding fees.
            </small>
        </div>

        <a href="{{ route('fee-receipts.index') }}"
           class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Fee Receipts
        </a>
    </div>


    <form method="POST"
          action="{{ route('fee-receipts.store') }}"
          id="receiptForm">

        @csrf


        {{-- STUDENT INFORMATION --}}
        <div class="card shadow-sm mb-4">

            <div class="card-header bg-light">
                <strong>
                    <i class="bi bi-person"></i>
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

                                <option value="{{ $year->id }}"
                                    {{ old('academic_year_id') == $year->id ? 'selected' : '' }}>

                                    {{ $year->name }}

                                </option>

                            @endforeach

                        </select>

                    </div>


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
                               id="student_id">

                        <input type="hidden"
                               name="student_enrollment_id"
                               id="student_enrollment_id">

                    </div>


                    <div class="col-md-3">

                        <label class="form-label">
                            Payment Date
                            <span class="text-danger">*</span>
                        </label>

                        <input type="date"
                               name="payment_date"
                               class="form-control"
                               value="{{ old('payment_date', date('Y-m-d')) }}"
                               required>

                    </div>

                </div>

            </div>
        </div>


        {{-- OUTSTANDING DUES --}}
        <div class="card shadow-sm mb-4">

            <div class="card-header d-flex justify-content-between align-items-center">

                <strong>
                    <i class="bi bi-receipt"></i>
                    Outstanding Fees
                </strong>

                <button type="button"
                        id="payFullButton"
                        class="btn btn-sm btn-outline-success"
                        disabled>
                    Pay Full Outstanding
                </button>

            </div>


            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-bordered table-hover mb-0 align-middle">

                        <thead class="table-light">

                            <tr>
                                <th>Fee Component</th>
                                <th>Installment</th>
                                <th>Due Date</th>

                                <th class="text-end">
                                    Payable
                                </th>

                                <th class="text-end">
                                    Paid
                                </th>

                                <th class="text-end">
                                    Balance
                                </th>

                                <th width="170">
                                    Pay Now
                                </th>
                            </tr>

                        </thead>

                        <tbody id="duesBody">

                            <tr>
                                <td colspan="7"
                                    class="text-center text-muted py-4">

                                    Select academic year and student.

                                </td>
                            </tr>

                        </tbody>

                        <tfoot>

                            <tr class="table-light">

                                <th colspan="5"
                                    class="text-end">

                                    Total Outstanding

                                </th>

                                <th class="text-end"
                                    id="totalOutstanding">

                                    ₹0.00

                                </th>

                                <th></th>

                            </tr>


                            <tr class="table-success">

                                <th colspan="6"
                                    class="text-end">

                                    Amount Receiving

                                </th>

                                <th>
                                    <strong id="receivingTotal">
                                        ₹0.00
                                    </strong>
                                </th>

                            </tr>

                        </tfoot>

                    </table>

                </div>

            </div>
        </div>


        {{-- PAYMENT INFORMATION --}}
        <div class="card shadow-sm mb-4">

            <div class="card-header bg-light">

                <strong>
                    <i class="bi bi-credit-card"></i>
                    Payment Information
                </strong>

            </div>


            <div class="card-body">

                <div class="row g-3">

                    <div class="col-md-4">

                        <label class="form-label">
                            Payment Mode
                            <span class="text-danger">*</span>
                        </label>

                        <select name="payment_mode_id"
                                id="payment_mode_id"
                                class="form-select"
                                required>

                            <option value="">
                                Select Payment Mode
                            </option>

                            @foreach($paymentModes as $mode)

                                <option value="{{ $mode->id }}"
                                        data-type="{{ $mode->mode_type }}"
                                        data-reference="{{ $mode->requires_reference }}"
                                        data-bank="{{ $mode->requires_bank }}"
                                        data-instrument-date="{{ $mode->requires_instrument_date }}">

                                    {{ $mode->name }}

                                </option>

                            @endforeach

                        </select>

                    </div>


                    <div class="col-md-4">

                        <label class="form-label">
                            School Account
                        </label>

                        <select name="school_account_id"
                                class="form-select">

                            <option value="">
                                Select Account
                            </option>

                            @foreach($schoolAccounts as $account)

                                <option value="{{ $account->id }}">

                                    {{ $account->account_name }}

                                    @if($account->account_number)
                                        -
                                        {{ $account->account_number }}
                                    @endif

                                </option>

                            @endforeach

                        </select>

                    </div>


                    <div class="col-md-4 payment-reference"
                         style="display:none;">

                        <label class="form-label">
                            Transaction / Reference No.
                        </label>

                        <input type="text"
                               name="transaction_no"
                               class="form-control"
                               maxlength="100">

                    </div>


                    <div class="col-md-4 payment-bank"
                         style="display:none;">

                        <label class="form-label">
                            Bank Name
                        </label>

                        <input type="text"
                               name="bank_name"
                               class="form-control"
                               maxlength="150">

                    </div>


                    <div class="col-md-4 cheque-fields"
                         style="display:none;">

                        <label class="form-label">
                            Cheque / DD Number
                        </label>

                        <input type="text"
                               name="cheque_no"
                               class="form-control"
                               maxlength="100">

                    </div>


                    <div class="col-md-4 instrument-date"
                         style="display:none;">

                        <label class="form-label">
                            Cheque / DD Date
                        </label>

                        <input type="date"
                               name="cheque_date"
                               class="form-control">

                    </div>


                    <div class="col-12">

                        <label class="form-label">
                            Remarks
                        </label>

                        <textarea name="remarks"
                                  rows="3"
                                  class="form-control"
                                  maxlength="2000">{{ old('remarks') }}</textarea>

                    </div>

                </div>

            </div>
        </div>


        <div class="d-flex justify-content-end gap-2 mb-4">

            <a href="{{ route('fee-receipts.index') }}"
               class="btn btn-light">
                Cancel
            </a>

            <button type="submit"
                    id="submitReceipt"
                    class="btn btn-success">

                <i class="bi bi-cash-coin"></i>
                Collect Fee & Generate Receipt

            </button>

        </div>

    </form>

</div>


<script>
document.addEventListener('DOMContentLoaded', function () {

    const year = document.getElementById('academic_year_id');
    const student = document.getElementById('student_selector');

    const studentId = document.getElementById('student_id');
    const enrollmentId = document.getElementById('student_enrollment_id');

    const duesBody = document.getElementById('duesBody');
    const payFullButton = document.getElementById('payFullButton');

    const paymentMode = document.getElementById('payment_mode_id');


    function money(value) {
        return '₹' + Number(value || 0).toLocaleString(
            'en-IN',
            {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }
        );
    }


    function escapeHtml(value) {
        const div = document.createElement('div');
        div.textContent = value || '';
        return div.innerHTML;
    }


    /*
    |--------------------------------------------------------------------------
    | LOAD STUDENTS
    |--------------------------------------------------------------------------
    */

    year.addEventListener('change', loadStudents);


    async function loadStudents() {

        studentId.value = '';
        enrollmentId.value = '';

        student.disabled = true;

        student.innerHTML =
            '<option value="">Loading Students...</option>';

        resetDues();


        if (!year.value) {

            student.innerHTML =
                '<option value="">Select Academic Year First</option>';

            return;
        }


        try {

            const response = await fetch(
                "{{ route('fee-receipts.students') }}"
                + '?academic_year_id='
                + encodeURIComponent(year.value),
                {
                    headers: {
                        'Accept': 'application/json'
                    }
                }
            );


            if (!response.ok) {
                throw new Error('Unable to load students.');
            }


            const rows = await response.json();


            student.innerHTML =
                '<option value="">Select Student</option>';


            rows.forEach(function(row) {

                const option =
                    document.createElement('option');

                option.value =
                    row.student_id;

                option.dataset.enrollment =
                    row.enrollment_id;


                let text =
                    row.student_name;


                if (row.admission_no) {
                    text += ' - ' + row.admission_no;
                }


                if (row.class_name) {
                    text += ' | ' + row.class_name;
                }


                if (row.section_name) {
                    text += ' - ' + row.section_name;
                }


                option.textContent = text;

                student.appendChild(option);
            });


            student.disabled = false;

        } catch (error) {

            student.innerHTML =
                '<option value="">Unable to load students</option>';

            console.error(error);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | STUDENT CHANGE
    |--------------------------------------------------------------------------
    */

    student.addEventListener('change', function () {

        const option =
            student.options[
                student.selectedIndex
            ];


        studentId.value =
            student.value || '';


        enrollmentId.value =
            option
                ? option.dataset.enrollment || ''
                : '';


        loadDues();
    });


    /*
    |--------------------------------------------------------------------------
    | LOAD DUES
    |--------------------------------------------------------------------------
    */

    async function loadDues() {

        resetDues();


        if (
            !year.value
            ||
            !studentId.value
            ||
            !enrollmentId.value
        ) {
            return;
        }


        duesBody.innerHTML = `
            <tr>
                <td colspan="7"
                    class="text-center py-4">
                    Loading outstanding fees...
                </td>
            </tr>
        `;


        const params =
            new URLSearchParams({

                academic_year_id:
                    year.value,

                student_id:
                    studentId.value,

                student_enrollment_id:
                    enrollmentId.value
            });


        try {

            const response = await fetch(
                "{{ route('fee-receipts.dues') }}"
                + '?'
                + params.toString(),
                {
                    headers: {
                        'Accept': 'application/json'
                    }
                }
            );


            if (!response.ok) {
                throw new Error('Unable to load dues.');
            }


            const rows = await response.json();


            if (!rows.length) {

                duesBody.innerHTML = `
                    <tr>
                        <td colspan="7"
                            class="text-center text-success py-4">

                            No outstanding fees found.

                        </td>
                    </tr>
                `;

                return;
            }


            duesBody.innerHTML = '';

            let outstanding = 0;


            rows.forEach(function(row) {

                outstanding +=
                    Number(row.balance_amount);


                const tr =
                    document.createElement('tr');


                tr.innerHTML = `

                    <td>
                        ${escapeHtml(row.fee_head)}
                    </td>

                    <td>
                        ${escapeHtml(row.installment)}
                    </td>

                    <td>
                        ${escapeHtml(row.due_date || '-')}
                    </td>

                    <td class="text-end">
                        ${money(row.payable_amount)}
                    </td>

                    <td class="text-end">
                        ${money(row.paid_amount)}
                    </td>

                    <td class="text-end fw-semibold">
                        ${money(row.balance_amount)}
                    </td>

                    <td>

                        <input type="number"
                               name="payments[${row.id}]"
                               class="form-control form-control-sm payment-input"
                               min="0"
                               max="${row.balance_amount}"
                               step="0.01"
                               value=""
                               data-balance="${row.balance_amount}">

                    </td>
                `;


                duesBody.appendChild(tr);
            });


            document.getElementById(
                'totalOutstanding'
            ).textContent =
                money(outstanding);


            document
                .querySelectorAll('.payment-input')
                .forEach(function(input) {

                    input.addEventListener(
                        'input',
                        calculateReceiving
                    );

                });


            payFullButton.disabled = false;

        } catch (error) {

            duesBody.innerHTML = `
                <tr>
                    <td colspan="7"
                        class="text-center text-danger py-4">

                        Unable to load outstanding fees.

                    </td>
                </tr>
            `;

            console.error(error);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | PAY FULL
    |--------------------------------------------------------------------------
    */

    payFullButton.addEventListener('click', function () {

        document
            .querySelectorAll('.payment-input')
            .forEach(function(input) {

                input.value =
                    Number(
                        input.dataset.balance
                    ).toFixed(2);

            });


        calculateReceiving();
    });


    /*
    |--------------------------------------------------------------------------
    | TOTAL
    |--------------------------------------------------------------------------
    */

    function calculateReceiving() {

        let total = 0;


        document
            .querySelectorAll('.payment-input')
            .forEach(function(input) {

                let amount =
                    Number(input.value || 0);

                const balance =
                    Number(
                        input.dataset.balance || 0
                    );


                if (amount > balance) {

                    amount = balance;

                    input.value =
                        balance.toFixed(2);
                }


                if (amount < 0) {

                    amount = 0;

                    input.value = '';
                }


                total += amount;
            });


        document.getElementById(
            'receivingTotal'
        ).textContent =
            money(total);
    }


    function resetDues() {

        duesBody.innerHTML = `
            <tr>
                <td colspan="7"
                    class="text-center text-muted py-4">

                    Select academic year and student.

                </td>
            </tr>
        `;


        document.getElementById(
            'totalOutstanding'
        ).textContent =
            money(0);


        document.getElementById(
            'receivingTotal'
        ).textContent =
            money(0);


        payFullButton.disabled = true;
    }


    /*
    |--------------------------------------------------------------------------
    | PAYMENT MODE
    |--------------------------------------------------------------------------
    */

    paymentMode.addEventListener(
        'change',
        updatePaymentFields
    );


    function updatePaymentFields() {

        const option =
            paymentMode.options[
                paymentMode.selectedIndex
            ];


        document
            .querySelectorAll(
                '.payment-reference, .payment-bank, .cheque-fields, .instrument-date'
            )
            .forEach(function(element) {

                element.style.display = 'none';

            });


        if (!option || !option.value) {
            return;
        }


        const type =
            option.dataset.type || '';

        const requiresReference =
            option.dataset.reference == '1';

        const requiresBank =
            option.dataset.bank == '1';

        const requiresDate =
            option.dataset.instrumentDate == '1';


        if (requiresReference) {

            document.querySelector(
                '.payment-reference'
            ).style.display = '';

        }


        if (requiresBank) {

            document.querySelector(
                '.payment-bank'
            ).style.display = '';

        }


        if (
            type === 'cheque'
            ||
            type === 'dd'
        ) {

            document.querySelector(
                '.cheque-fields'
            ).style.display = '';

        }


        if (requiresDate) {

            document.querySelector(
                '.instrument-date'
            ).style.display = '';

        }
    }


    /*
    |--------------------------------------------------------------------------
    | SUBMIT
    |--------------------------------------------------------------------------
    */

    document
        .getElementById('receiptForm')
        .addEventListener(
            'submit',
            function(event) {

                if (
                    !studentId.value
                    ||
                    !enrollmentId.value
                ) {

                    event.preventDefault();

                    alert(
                        'Please select a student.'
                    );

                    return;
                }


                let total = 0;


                document
                    .querySelectorAll('.payment-input')
                    .forEach(function(input) {

                        total +=
                            Number(
                                input.value || 0
                            );
                    });


                if (total <= 0) {

                    event.preventDefault();

                    alert(
                        'Please enter at least one payment amount.'
                    );

                    return;
                }


                if (
                    !confirm(
                        'Collect '
                        + money(total)
                        + ' and generate the fee receipt?'
                    )
                ) {

                    event.preventDefault();
                }
            }
        );

});
</script>

@endsection