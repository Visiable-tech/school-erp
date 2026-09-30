<div class="row g-3">

    <div class="col-md-4">

        <label class="form-label">
            Concession Name
            <span class="text-danger">*</span>
        </label>

        <input type="text"
               name="name"
               class="form-control"
               value="{{ old(
                   'name',
                   $concessionType->name ?? ''
               ) }}"
               placeholder="Sibling Concession"
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
                   $concessionType->code ?? ''
               ) }}"
               placeholder="SIBLING">

    </div>


    <div class="col-md-3">

        <label class="form-label">
            Concession Mode
            <span class="text-danger">*</span>
        </label>

        @php
            $mode = old(
                'concession_mode',
                $concessionType->concession_mode
                    ?? 'percentage'
            );
        @endphp

        <select name="concession_mode"
                id="concession_mode"
                class="form-select"
                required>

            <option value="percentage"
                {{ $mode === 'percentage'
                    ? 'selected'
                    : '' }}>

                Percentage

            </option>

            <option value="fixed"
                {{ $mode === 'fixed'
                    ? 'selected'
                    : '' }}>

                Fixed Amount

            </option>

        </select>

    </div>


    <div class="col-md-3">

        <label class="form-label">
            Default Value
        </label>

        <div class="input-group">

            <span class="input-group-text"
                  id="valuePrefix">
                %
            </span>

            <input type="number"
                   name="default_value"
                   class="form-control"
                   min="0"
                   step="0.01"
                   value="{{ old(
                       'default_value',
                       $concessionType->default_value
                           ?? ''
                   ) }}">

        </div>

    </div>


    <div class="col-md-3">

        <label class="form-label">
            Maximum Concession
        </label>

        <div class="input-group">

            <span class="input-group-text">
                ₹
            </span>

            <input type="number"
                   name="maximum_amount"
                   class="form-control"
                   min="0"
                   step="0.01"
                   value="{{ old(
                       'maximum_amount',
                       $concessionType->maximum_amount
                           ?? ''
                   ) }}"
                   placeholder="No limit">

        </div>

        <small class="text-muted">
            Optional. Leave blank for no limit.
        </small>

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
                   $concessionType->sort_order ?? 0
               ) }}">

    </div>


    <div class="col-md-7">

        <label class="form-label">
            Description
        </label>

        <textarea name="description"
                  class="form-control"
                  rows="2"
                  placeholder="Optional description">{{ old(
                      'description',
                      $concessionType->description ?? ''
                  ) }}</textarea>

    </div>


    <div class="col-md-12">

        <div class="card bg-light border-0">

            <div class="card-body">

                <div class="row">

                    <div class="col-md-4">

                        <div class="form-check form-switch">

                            <input type="checkbox"
                                   name="requires_approval"
                                   value="1"
                                   id="requires_approval"
                                   class="form-check-input"
                                   {{ old(
                                       'requires_approval',
                                       $concessionType->requires_approval
                                           ?? false
                                   ) ? 'checked' : '' }}>

                            <label class="form-check-label"
                                   for="requires_approval">

                                Requires Approval

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
                                       isset($concessionType)
                                           ? $concessionType->status
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

        const mode =
            document.getElementById(
                'concession_mode'
            );

        const prefix =
            document.getElementById(
                'valuePrefix'
            );

        function updateMode() {

            if (
                mode.value === 'percentage'
            ) {
                prefix.innerText = '%';
            } else {
                prefix.innerText = '₹';
            }
        }

        mode.addEventListener(
            'change',
            updateMode
        );

        updateMode();
    }
);
</script>