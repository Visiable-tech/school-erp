<div class="row g-3">

    <div class="col-md-4">

        <label class="form-label">
            Payment Mode Name
            <span class="text-danger">*</span>
        </label>

        <input type="text"
               name="name"
               class="form-control"
               value="{{ old(
                    'name',
                    $paymentMode->name ?? ''
               ) }}"
               placeholder="Cash"
               required>

    </div>


    <div class="col-md-2">

        <label class="form-label">
            Code
        </label>

        <input type="text"
               name="code"
               class="form-control"
               value="{{ old(
                    'code',
                    $paymentMode->code ?? ''
               ) }}"
               placeholder="CASH">

    </div>


    <div class="col-md-3">

        <label class="form-label">
            Mode Type
            <span class="text-danger">*</span>
        </label>

        @php
            $type = old(
                'mode_type',
                $paymentMode->mode_type ?? 'cash'
            );
        @endphp

        <select name="mode_type"
                id="mode_type"
                class="form-select"
                required>

            <option value="cash"
                {{ $type === 'cash' ? 'selected' : '' }}>
                Cash
            </option>

            <option value="cheque"
                {{ $type === 'cheque' ? 'selected' : '' }}>
                Cheque
            </option>

            <option value="dd"
                {{ $type === 'dd' ? 'selected' : '' }}>
                Demand Draft
            </option>

            <option value="card"
                {{ $type === 'card' ? 'selected' : '' }}>
                Card
            </option>

            <option value="upi"
                {{ $type === 'upi' ? 'selected' : '' }}>
                UPI
            </option>

            <option value="bank_transfer"
                {{ $type === 'bank_transfer' ? 'selected' : '' }}>
                Bank Transfer
            </option>

            <option value="online"
                {{ $type === 'online' ? 'selected' : '' }}>
                Online Gateway
            </option>

            <option value="other"
                {{ $type === 'other' ? 'selected' : '' }}>
                Other
            </option>

        </select>

    </div>


    <div class="col-md-3">

        <label class="form-label">
            Sort Order
        </label>

        <input type="number"
               name="sort_order"
               min="0"
               class="form-control"
               value="{{ old(
                    'sort_order',
                    $paymentMode->sort_order ?? 0
               ) }}">

    </div>


    <div class="col-md-12">

        <label class="form-label">
            Description
        </label>

        <textarea name="description"
                  class="form-control"
                  rows="2">{{ old(
                    'description',
                    $paymentMode->description ?? ''
                  ) }}</textarea>

    </div>


    <div class="col-md-12">

        <div class="card bg-light border-0">

            <div class="card-body">

                <div class="row g-3">

                    <div class="col-md-4">

                        <div class="form-check form-switch">

                            <input type="checkbox"
                                   name="requires_reference"
                                   value="1"
                                   id="requires_reference"
                                   class="form-check-input"
                                   {{ old(
                                       'requires_reference',
                                       $paymentMode->requires_reference ?? false
                                   ) ? 'checked' : '' }}>

                            <label class="form-check-label"
                                   for="requires_reference">

                                Reference / Transaction No.

                            </label>

                        </div>

                    </div>


                    <div class="col-md-4">

                        <div class="form-check form-switch">

                            <input type="checkbox"
                                   name="requires_bank"
                                   value="1"
                                   id="requires_bank"
                                   class="form-check-input"
                                   {{ old(
                                       'requires_bank',
                                       $paymentMode->requires_bank ?? false
                                   ) ? 'checked' : '' }}>

                            <label class="form-check-label"
                                   for="requires_bank">

                                Bank Required

                            </label>

                        </div>

                    </div>


                    <div class="col-md-4">

                        <div class="form-check form-switch">

                            <input type="checkbox"
                                   name="requires_instrument_date"
                                   value="1"
                                   id="requires_instrument_date"
                                   class="form-check-input"
                                   {{ old(
                                       'requires_instrument_date',
                                       $paymentMode->requires_instrument_date ?? false
                                   ) ? 'checked' : '' }}>

                            <label class="form-check-label"
                                   for="requires_instrument_date">

                                Instrument Date Required

                            </label>

                        </div>

                    </div>


                    <div class="col-md-4">

                        <div class="form-check form-switch">

                            <input type="checkbox"
                                   name="is_online"
                                   value="1"
                                   id="is_online"
                                   class="form-check-input"
                                   {{ old(
                                       'is_online',
                                       $paymentMode->is_online ?? false
                                   ) ? 'checked' : '' }}>

                            <label class="form-check-label"
                                   for="is_online">
                                Online Payment
                            </label>

                        </div>

                    </div>


                    <div class="col-md-4">

                        <div class="form-check form-switch">

                            <input type="checkbox"
                                   name="is_default"
                                   value="1"
                                   id="is_default"
                                   class="form-check-input"
                                   {{ old(
                                       'is_default',
                                       $paymentMode->is_default ?? false
                                   ) ? 'checked' : '' }}>

                            <label class="form-check-label"
                                   for="is_default">
                                Default Payment Mode
                            </label>

                        </div>

                    </div>


                    <div class="col-md-4">

                        <div class="form-check form-switch">

                            <input type="checkbox"
                                   name="status"
                                   value="1"
                                   id="status"
                                   class="form-check-input"
                                   {{ old(
                                       'status',
                                       isset($paymentMode)
                                           ? $paymentMode->status
                                           : true
                                   ) ? 'checked' : '' }}>

                            <label class="form-check-label"
                                   for="status">
                                Active
                            </label>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>