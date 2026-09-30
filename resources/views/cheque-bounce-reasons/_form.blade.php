<div class="row g-3">

    <div class="col-md-4">

        <label class="form-label">
            Reason
            <span class="text-danger">*</span>
        </label>

        <input type="text"
               name="name"
               class="form-control"
               value="{{ old(
                    'name',
                    $chequeBounceReason->name ?? ''
               ) }}"
               placeholder="Insufficient Funds"
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
                    $chequeBounceReason->code ?? ''
               ) }}"
               placeholder="INSUF">

    </div>


    <div class="col-md-3">

        <label class="form-label">
            Bounce Charge
        </label>

        <div class="input-group">

            <span class="input-group-text">
                ₹
            </span>

            <input type="number"
                   name="bounce_charge"
                   id="bounce_charge"
                   class="form-control"
                   min="0"
                   step="0.01"
                   value="{{ old(
                       'bounce_charge',
                       $chequeBounceReason->bounce_charge ?? 0
                   ) }}">

        </div>

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
                    $chequeBounceReason->sort_order ?? 0
               ) }}">

    </div>


    <div class="col-md-12">

        <label class="form-label">
            Description
        </label>

        <textarea name="description"
                  class="form-control"
                  rows="3"
                  placeholder="Optional description">{{ old(
                      'description',
                      $chequeBounceReason->description ?? ''
                  ) }}</textarea>

    </div>


    <div class="col-md-12">

        <div class="card bg-light border-0">

            <div class="card-body">

                <div class="row g-3">

                    <div class="col-md-4">

                        <div class="form-check form-switch">

                            <input type="checkbox"
                                   name="apply_charge"
                                   value="1"
                                   id="apply_charge"
                                   class="form-check-input"
                                   {{ old(
                                       'apply_charge',
                                       $chequeBounceReason->apply_charge
                                           ?? false
                                   ) ? 'checked' : '' }}>

                            <label class="form-check-label"
                                   for="apply_charge">

                                Apply Bounce Charge

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
                                       isset($chequeBounceReason)
                                           ? $chequeBounceReason->status
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


<script>
document.addEventListener(
    'DOMContentLoaded',
    function () {

        const apply =
            document.getElementById(
                'apply_charge'
            );

        const amount =
            document.getElementById(
                'bounce_charge'
            );

        function updateCharge() {

            amount.disabled =
                !apply.checked;

            if (!apply.checked) {
                amount.value = '0';
            }
        }

        apply.addEventListener(
            'change',
            updateCharge
        );

        updateCharge();
    }
);
</script>