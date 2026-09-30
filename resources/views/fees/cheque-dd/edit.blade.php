@extends('layouts.admin')

@section('title', 'Edit Cheque / DD')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h4 class="mb-1">
                Edit Cheque / DD
            </h4>

            <small class="text-muted">

                Receipt:
                {{ $chequeDdDetail->feeCollection->receipt_no ?? '-' }}

            </small>

        </div>


        <a href="{{ route('cheque-dd-details.show', $chequeDdDetail) }}"
           class="btn btn-outline-secondary">

            <i class="bi bi-arrow-left"></i>
            Back

        </a>

    </div>


    @if($errors->any())

        <div class="alert alert-danger">

            <ul class="mb-0">

                @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif


    <form method="POST"
          action="{{ route('cheque-dd-details.update', $chequeDdDetail) }}">

        @csrf
        @method('PUT')


        <div class="card shadow-sm mb-4">

            <div class="card-header bg-white">

                <strong>
                    Receipt Information
                </strong>

            </div>


            <div class="card-body">

                <div class="row g-3">

                    <div class="col-md-3">

                        <small class="text-muted">
                            Receipt No.
                        </small>

                        <div class="fw-semibold">

                            {{ $chequeDdDetail->feeCollection->receipt_no ?? '-' }}

                        </div>

                    </div>


                    <div class="col-md-3">

                        <small class="text-muted">
                            Student
                        </small>

                        <div>

                            {{ $chequeDdDetail->feeCollection->student->student_name
                                ?? $chequeDdDetail->feeCollection->student->name
                                ?? '-' }}

                        </div>

                    </div>


                    <div class="col-md-3">

                        <small class="text-muted">
                            Instrument Type
                        </small>

                        <div class="fw-semibold text-uppercase">

                            {{ $chequeDdDetail->instrument_type }}

                        </div>

                    </div>


                    <div class="col-md-3">

                        <small class="text-muted">
                            Amount
                        </small>

                        <div class="fw-bold">

                            ₹{{ number_format($chequeDdDetail->amount, 2) }}

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <div class="card shadow-sm mb-4">

            <div class="card-header bg-white">

                <strong>
                    Instrument Information
                </strong>

            </div>


            <div class="card-body">

                <div class="row g-3">


                    <div class="col-md-4">

                        <label class="form-label">
                            Cheque / DD No.
                        </label>

                        <input type="text"
                               name="instrument_no"
                               class="form-control"
                               value="{{ old(
                                    'instrument_no',
                                    $chequeDdDetail->instrument_no
                               ) }}"
                               required>

                    </div>


                    <div class="col-md-4">

                        <label class="form-label">
                            Instrument Date
                        </label>

                        <input type="date"
                               name="instrument_date"
                               class="form-control"
                               value="{{ old(
                                    'instrument_date',
                                    optional(
                                        $chequeDdDetail->instrument_date
                                    )->format('Y-m-d')
                               ) }}"
                               required>

                    </div>


                    <div class="col-md-4">

                        <label class="form-label">
                            Bank Master
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
                                    {{ old(
                                        'bank_master_id',
                                        $chequeDdDetail->bank_master_id
                                    ) == $bank->id ? 'selected' : '' }}>

                                    {{ $bank->bank_name }}

                                    @if($bank->branch_name)

                                        - {{ $bank->branch_name }}

                                    @endif

                                </option>

                            @endforeach

                        </select>

                    </div>


                    <div class="col-md-4">

                        <label class="form-label">
                            Bank Name
                        </label>

                        <input type="text"
                               name="bank_name"
                               id="bank_name"
                               class="form-control"
                               value="{{ old(
                                    'bank_name',
                                    $chequeDdDetail->bank_name
                               ) }}">

                    </div>


                    <div class="col-md-4">

                        <label class="form-label">
                            Branch
                        </label>

                        <input type="text"
                               name="branch_name"
                               id="branch_name"
                               class="form-control"
                               value="{{ old(
                                    'branch_name',
                                    $chequeDdDetail->branch_name
                               ) }}">

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

                                <option value="{{ $account->id }}"
                                    {{ old(
                                        'school_account_id',
                                        $chequeDdDetail->school_account_id
                                    ) == $account->id ? 'selected' : '' }}>

                                    {{ $account->account_name }}

                                    @if($account->account_number)

                                        - {{ $account->account_number }}

                                    @endif

                                </option>

                            @endforeach

                        </select>

                    </div>


                    <div class="col-md-4">

                        <label class="form-label">
                            Deposit Date
                        </label>

                        <input type="date"
                               name="deposit_date"
                               class="form-control"
                               value="{{ old(
                                    'deposit_date',
                                    optional(
                                        $chequeDdDetail->deposit_date
                                    )->format('Y-m-d')
                               ) }}">

                    </div>


                    <div class="col-12">

                        <label class="form-label">
                            Remarks
                        </label>

                        <textarea name="remarks"
                                  class="form-control"
                                  rows="3">{{ old(
                                      'remarks',
                                      $chequeDdDetail->remarks
                                  ) }}</textarea>

                    </div>

                </div>

            </div>

        </div>


        <div class="text-end">

            <button class="btn btn-primary">

                <i class="bi bi-save"></i>
                Update

            </button>

        </div>

    </form>

</div>


<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

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
    }
);

</script>

@endsection