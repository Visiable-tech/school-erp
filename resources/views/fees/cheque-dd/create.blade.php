@extends('layouts.admin')

@section('title', 'Add Cheque / DD Detail')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h4 class="mb-1">
                Add Cheque / DD Detail
            </h4>

            <small class="text-muted">
                Link cheque or demand draft information with an existing fee receipt.
            </small>

        </div>


        <a href="{{ route('cheque-dd-details.index') }}"
           class="btn btn-outline-secondary">

            <i class="bi bi-arrow-left"></i>
            Back

        </a>

    </div>


    @if($errors->any())

        <div class="alert alert-danger">

            <strong>Please correct the following:</strong>

            <ul class="mb-0 mt-2">

                @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif


    <form method="POST"
          action="{{ route('cheque-dd-details.store') }}"
          id="instrumentForm">

        @csrf


        {{-- RECEIPT --}}
        <div class="card shadow-sm mb-4">

            <div class="card-header bg-white">

                <strong>
                    <i class="bi bi-receipt me-1"></i>
                    Fee Receipt
                </strong>

            </div>


            <div class="card-body">

                <div class="row g-3">

                    <div class="col-md-6">

                        <label class="form-label">
                            Select Cheque/DD Receipt
                            <span class="text-danger">*</span>
                        </label>


                        <select name="fee_collection_id"
                                id="fee_collection_id"
                                class="form-select"
                                required>

                            <option value="">
                                Select Receipt
                            </option>

                            @foreach($receipts as $receipt)

                                <option value="{{ $receipt->id }}"
                                    {{ old('fee_collection_id') == $receipt->id ? 'selected' : '' }}>

                                    {{ $receipt->receipt_no }}

                                    -

                                    {{ $receipt->student->student_name
                                        ?? $receipt->student->name
                                        ?? 'Student' }}

                                    -

                                    ₹{{ number_format($receipt->total_amount, 2) }}

                                </option>

                            @endforeach

                        </select>

                    </div>

                </div>


                <div id="receiptInfo"
                     class="mt-4 d-none">

                    <div class="row g-3">

                        <div class="col-md-3">

                            <small class="text-muted">
                                Student
                            </small>

                            <div class="fw-semibold"
                                 id="studentName">
                                -
                            </div>

                        </div>


                        <div class="col-md-3">

                            <small class="text-muted">
                                Admission No.
                            </small>

                            <div id="admissionNo">
                                -
                            </div>

                        </div>


                        <div class="col-md-2">

                            <small class="text-muted">
                                Academic Year
                            </small>

                            <div id="academicYear">
                                -
                            </div>

                        </div>


                        <div class="col-md-2">

                            <small class="text-muted">
                                Payment Date
                            </small>

                            <div id="paymentDate">
                                -
                            </div>

                        </div>


                        <div class="col-md-2">

                            <small class="text-muted">
                                Receipt Amount
                            </small>

                            <div class="fw-bold text-success"
                                 id="receiptAmount">
                                ₹0.00
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- INSTRUMENT --}}
        <div class="card shadow-sm mb-4">

            <div class="card-header bg-white">

                <strong>
                    Cheque / DD Information
                </strong>

            </div>


            <div class="card-body">

                <div class="row g-3">


                    <div class="col-md-3">

                        <label class="form-label">
                            Instrument Type
                            <span class="text-danger">*</span>
                        </label>

                        <select name="instrument_type"
                                id="instrument_type"
                                class="form-select"
                                required>

                            <option value="">
                                Select Type
                            </option>

                            <option value="cheque"
                                {{ old('instrument_type') == 'cheque' ? 'selected' : '' }}>
                                Cheque
                            </option>

                            <option value="dd"
                                {{ old('instrument_type') == 'dd' ? 'selected' : '' }}>
                                Demand Draft
                            </option>

                        </select>

                    </div>


                    <div class="col-md-3">

                        <label class="form-label">
                            Cheque / DD No.
                            <span class="text-danger">*</span>
                        </label>

                        <input type="text"
                               name="instrument_no"
                               id="instrument_no"
                               class="form-control"
                               value="{{ old('instrument_no') }}"
                               required>

                    </div>


                    <div class="col-md-3">

                        <label class="form-label">
                            Instrument Date
                            <span class="text-danger">*</span>
                        </label>

                        <input type="date"
                               name="instrument_date"
                               id="instrument_date"
                               class="form-control"
                               value="{{ old('instrument_date') }}"
                               required>

                    </div>


                    <div class="col-md-3">

                        <label class="form-label">
                            Bank
                        </label>

                        <select name="bank_master_id"
                                id="bank_master_id"
                                class="form-select">

                            <option value="">
                                Select Bank
                            </option>

                            @foreach($banks as $bank)

                                <option value="{{ $bank->id }}"
                                    data-bank="{{ $bank->bank_name }}"
                                    data-branch="{{ $bank->branch_name }}"
                                    {{ old('bank_master_id') == $bank->id ? 'selected' : '' }}>

                                    {{ $bank->bank_name }}

                                    @if($bank->branch_name)
                                        - {{ $bank->branch_name }}
                                    @endif

                                </option>

                            @endforeach

                        </select>

                    </div>


                    <div class="col-md-3">

                        <label class="form-label">
                            Bank Name
                        </label>

                        <input type="text"
                               name="bank_name"
                               id="bank_name"
                               class="form-control"
                               value="{{ old('bank_name') }}">

                    </div>


                    <div class="col-md-3">

                        <label class="form-label">
                            Branch Name
                        </label>

                        <input type="text"
                               name="branch_name"
                               id="branch_name"
                               class="form-control"
                               value="{{ old('branch_name') }}">

                    </div>


                    <div class="col-md-3">

                        <label class="form-label">
                            Deposit To School Account
                        </label>

                        <select name="school_account_id"
                                class="form-select">

                            <option value="">
                                Not Deposited Yet
                            </option>

                            @foreach($schoolAccounts as $account)

                                <option value="{{ $account->id }}"
                                    {{ old('school_account_id') == $account->id ? 'selected' : '' }}>

                                    {{ $account->account_name }}

                                    @if($account->account_number)
                                        - {{ $account->account_number }}
                                    @endif

                                </option>

                            @endforeach

                        </select>

                    </div>


                    <div class="col-md-3">

                        <label class="form-label">
                            Deposit Date
                        </label>

                        <input type="date"
                               name="deposit_date"
                               class="form-control"
                               value="{{ old('deposit_date') }}">

                    </div>


                    <div class="col-12">

                        <label class="form-label">
                            Remarks
                        </label>

                        <textarea name="remarks"
                                  class="form-control"
                                  rows="3">{{ old('remarks') }}</textarea>

                    </div>

                </div>

            </div>

        </div>


        <div class="text-end mb-4">

            <button type="submit"
                    class="btn btn-primary px-4">

                <i class="bi bi-save me-1"></i>

                Save Cheque / DD

            </button>

        </div>

    </form>

</div>


<script>

document.addEventListener('DOMContentLoaded', function () {

    const receipt =
        document.getElementById('fee_collection_id');

    const receiptInfo =
        document.getElementById('receiptInfo');

    const type =
        document.getElementById('instrument_type');

    const instrumentNo =
        document.getElementById('instrument_no');

    const instrumentDate =
        document.getElementById('instrument_date');

    const bankName =
        document.getElementById('bank_name');


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


    receipt.addEventListener(
        'change',
        loadReceipt
    );


    async function loadReceipt() {

        receiptInfo.classList.add('d-none');


        if (!receipt.value) {
            return;
        }


        try {

            const response = await fetch(

                "{{ route('cheque-dd-details.receipt-details') }}"

                + '?fee_collection_id='

                + encodeURIComponent(
                    receipt.value
                )
            );


            if (!response.ok) {
                throw new Error(
                    'Unable to load receipt.'
                );
            }


            const data =
                await response.json();


            document.getElementById(
                'studentName'
            ).textContent =
                data.student_name || '-';


            document.getElementById(
                'admissionNo'
            ).textContent =
                data.admission_no || '-';


            document.getElementById(
                'academicYear'
            ).textContent =
                data.academic_year || '-';


            document.getElementById(
                'paymentDate'
            ).textContent =
                data.payment_date || '-';


            document.getElementById(
                'receiptAmount'
            ).textContent =
                money(data.amount);


            if (data.instrument_type) {

                type.value =
                    data.instrument_type;

                type.setAttribute(
                    'readonly',
                    true
                );
            }


            if (
                data.instrument_no
                &&
                !instrumentNo.value
            ) {

                instrumentNo.value =
                    data.instrument_no;
            }


            if (
                data.instrument_date
                &&
                !instrumentDate.value
            ) {

                instrumentDate.value =
                    data.instrument_date;
            }


            if (
                data.bank_name
                &&
                !bankName.value
            ) {

                bankName.value =
                    data.bank_name;
            }


            receiptInfo
                .classList
                .remove('d-none');

        }
        catch (error) {

            alert(error.message);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Bank Master Auto Fill
    |--------------------------------------------------------------------------
    */

    document.getElementById(
        'bank_master_id'
    ).addEventListener(
        'change',
        function () {

            const option =
                this.options[
                    this.selectedIndex
                ];


            if (!this.value) {
                return;
            }


            document.getElementById(
                'bank_name'
            ).value =
                option.dataset.bank || '';


            document.getElementById(
                'branch_name'
            ).value =
                option.dataset.branch || '';
        }
    );


    /*
    |--------------------------------------------------------------------------
    | Reload old selected receipt
    |--------------------------------------------------------------------------
    */

    if (receipt.value) {
        loadReceipt();
    }

});

</script>

@endsection