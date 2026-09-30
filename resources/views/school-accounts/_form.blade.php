<div class="row g-3">

    <div class="col-md-4">

        <label class="form-label">
            Bank
            <span class="text-danger">*</span>
        </label>

        <select name="bank_master_id"
                class="form-select"
                required>

            <option value="">
                Select Bank
            </option>

            @foreach($banks as $bank)

                <option
                    value="{{ $bank->id }}"

                    {{ old(
                        'bank_master_id',
                        $schoolAccount->bank_master_id ?? ''
                    ) == $bank->id
                        ? 'selected'
                        : '' }}
                >

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
            Account Name
            <span class="text-danger">*</span>
        </label>

        <input type="text"
               name="account_name"
               class="form-control"
               value="{{ old(
                    'account_name',
                    $schoolAccount->account_name ?? ''
               ) }}"
               placeholder="School Fee Account"
               required>

    </div>


    <div class="col-md-4">

        <label class="form-label">
            Account Number
            <span class="text-danger">*</span>
        </label>

        <input type="text"
               name="account_number"
               class="form-control"
               value="{{ old(
                    'account_number',
                    $schoolAccount->account_number ?? ''
               ) }}"
               required>

    </div>


    <div class="col-md-4">

        <label class="form-label">
            Account Type
            <span class="text-danger">*</span>
        </label>

        @php
            $accountType = old(
                'account_type',
                $schoolAccount->account_type
                    ?? 'current'
            );
        @endphp

        <select name="account_type"
                class="form-select"
                required>

            <option value="savings"
                {{ $accountType === 'savings'
                    ? 'selected' : '' }}>
                Savings
            </option>

            <option value="current"
                {{ $accountType === 'current'
                    ? 'selected' : '' }}>
                Current
            </option>

            <option value="cash_credit"
                {{ $accountType === 'cash_credit'
                    ? 'selected' : '' }}>
                Cash Credit
            </option>

            <option value="overdraft"
                {{ $accountType === 'overdraft'
                    ? 'selected' : '' }}>
                Overdraft
            </option>

            <option value="other"
                {{ $accountType === 'other'
                    ? 'selected' : '' }}>
                Other
            </option>

        </select>

    </div>


    <div class="col-md-4">

        <label class="form-label">
            Purpose
        </label>

        <input type="text"
               name="purpose"
               class="form-control"
               value="{{ old(
                    'purpose',
                    $schoolAccount->purpose ?? ''
               ) }}"
               placeholder="Fee Collection">

    </div>


    <div class="col-md-4">

        <label class="form-label">
            UPI ID
        </label>

        <input type="text"
               name="upi_id"
               class="form-control"
               value="{{ old(
                    'upi_id',
                    $schoolAccount->upi_id ?? ''
               ) }}"
               placeholder="school@bank">

    </div>


    <div class="col-md-4">

        <label class="form-label">
            Merchant ID
        </label>

        <input type="text"
               name="merchant_id"
               class="form-control"
               value="{{ old(
                    'merchant_id',
                    $schoolAccount->merchant_id ?? ''
               ) }}">

    </div>


    <div class="col-md-2">

        <label class="form-label">
            Sort Order
        </label>

        <input type="number"
               name="sort_order"
               min="0"
               class="form-control"
               value="{{ old(
                    'sort_order',
                    $schoolAccount->sort_order ?? 0
               ) }}">

    </div>


    <div class="col-md-3">

        <label class="form-label d-block">
            Default Account
        </label>

        <div class="form-check form-switch mt-2">

            <input type="checkbox"
                   name="is_default"
                   value="1"
                   id="is_default"
                   class="form-check-input"
                   {{ old(
                       'is_default',
                       $schoolAccount->is_default ?? false
                   ) ? 'checked' : '' }}>

            <label for="is_default"
                   class="form-check-label">

                Default

            </label>

        </div>

    </div>


    <div class="col-md-3">

        <label class="form-label d-block">
            Status
        </label>

        <div class="form-check form-switch mt-2">

            <input type="checkbox"
                   name="status"
                   value="1"
                   id="status"
                   class="form-check-input"
                   {{ old(
                       'status',
                       isset($schoolAccount)
                           ? $schoolAccount->status
                           : true
                   ) ? 'checked' : '' }}>

            <label for="status"
                   class="form-check-label">
                Active
            </label>

        </div>

    </div>


    <div class="col-md-12">

        <label class="form-label">
            Description / Notes
        </label>

        <textarea name="description"
                  rows="3"
                  class="form-control">{{ old(
                    'description',
                    $schoolAccount->description ?? ''
                  ) }}</textarea>

    </div>

</div>