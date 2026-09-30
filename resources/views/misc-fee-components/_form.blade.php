<div class="row g-3">

    <div class="col-md-5">

        <label class="form-label">
            Component Name
            <span class="text-danger">*</span>
        </label>

        <input type="text"
               name="name"
               class="form-control"
               value="{{ old(
                    'name',
                    $miscFeeComponent->name ?? ''
               ) }}"
               placeholder="Example: Duplicate ID Card"
               required>

    </div>


    <div class="col-md-3">

        <label class="form-label">
            Code
        </label>

        <input type="text"
               name="code"
               class="form-control"
               value="{{ old(
                    'code',
                    $miscFeeComponent->code ?? ''
               ) }}"
               placeholder="DUP-ID">

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
                    $miscFeeComponent->sort_order ?? 0
               ) }}">

    </div>


    <div class="col-md-2">

        <label class="form-label d-block">
            Status
        </label>

        <div class="form-check form-switch mt-2">

            <input type="checkbox"
                   name="status"
                   value="1"
                   class="form-check-input"
                   id="status"
                   {{ old(
                       'status',
                       isset($miscFeeComponent)
                            ? $miscFeeComponent->status
                            : true
                   ) ? 'checked' : '' }}>

            <label class="form-check-label"
                   for="status">
                Active
            </label>

        </div>

    </div>


    <div class="col-md-4">

        <div class="card bg-light border-0 h-100">

            <div class="card-body">

                <div class="form-check form-switch">

                    <input type="checkbox"
                           name="fixed_amount"
                           value="1"
                           id="fixed_amount"
                           class="form-check-input"
                           {{ old(
                               'fixed_amount',
                               isset($miscFeeComponent)
                                    ? $miscFeeComponent->fixed_amount
                                    : true
                           ) ? 'checked' : '' }}>

                    <label class="form-check-label"
                           for="fixed_amount">

                        Fixed Amount

                    </label>

                </div>

                <small class="text-muted">
                    Turn off to enter the amount
                    during collection.
                </small>

            </div>

        </div>

    </div>


    <div class="col-md-4">

        <label class="form-label">
            Default Amount
        </label>

        <div class="input-group">

            <span class="input-group-text">
                ₹
            </span>

            <input type="number"
                   name="default_amount"
                   id="default_amount"
                   min="0"
                   step="0.01"
                   class="form-control"
                   value="{{ old(
                       'default_amount',
                       $miscFeeComponent->default_amount ?? ''
                   ) }}"
                   placeholder="0.00">

        </div>

    </div>


    <div class="col-md-4">

        <label class="form-label d-block">
            Options
        </label>

        <div class="d-flex flex-column gap-2">

            <div class="form-check">

                <input type="checkbox"
                       name="is_refundable"
                       value="1"
                       id="is_refundable"
                       class="form-check-input"
                       {{ old(
                           'is_refundable',
                           $miscFeeComponent->is_refundable ?? false
                       ) ? 'checked' : '' }}>

                <label class="form-check-label"
                       for="is_refundable">

                    Refundable

                </label>

            </div>


            <div class="form-check">

                <input type="checkbox"
                       name="allow_concession"
                       value="1"
                       id="allow_concession"
                       class="form-check-input"
                       {{ old(
                           'allow_concession',
                           $miscFeeComponent->allow_concession ?? false
                       ) ? 'checked' : '' }}>

                <label class="form-check-label"
                       for="allow_concession">

                    Allow Concession

                </label>

            </div>

        </div>

    </div>


    <div class="col-md-12">

        <label class="form-label">
            Description
        </label>

        <textarea name="description"
                  rows="3"
                  class="form-control"
                  placeholder="Optional description">{{ old(
                      'description',
                      $miscFeeComponent->description ?? ''
                  ) }}</textarea>

    </div>

</div>


<script>
document.addEventListener(
    'DOMContentLoaded',
    function () {

        const fixedAmount =
            document.getElementById(
                'fixed_amount'
            );

        const amount =
            document.getElementById(
                'default_amount'
            );


        function updateAmountField() {

            if (fixedAmount.checked) {

                amount.disabled = false;
                amount.required = true;

            } else {

                amount.disabled = true;
                amount.required = false;
                amount.value = '';

            }
        }


        fixedAmount.addEventListener(
            'change',
            updateAmountField
        );

        updateAmountField();
    }
);
</script>