<div class="row g-3">

    <div class="col-md-6">

        <label class="form-label">
            Cycle Name
            <span class="text-danger">*</span>
        </label>

        <input type="text"
               name="name"
               class="form-control"
               value="{{ old(
                    'name',
                    $feeCycle->name ?? ''
               ) }}"
               placeholder="Example: Monthly"
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
                    $feeCycle->code ?? ''
               ) }}"
               placeholder="MONTHLY">

    </div>


    <div class="col-md-3">

        <label class="form-label">
            Cycle Type
            <span class="text-danger">*</span>
        </label>

        <select name="cycle_type"
                id="cycle_type"
                class="form-select"
                required>

            @php
                $selectedType = old(
                    'cycle_type',
                    $feeCycle->cycle_type ?? 'monthly'
                );
            @endphp

            <option value="monthly"
                {{ $selectedType === 'monthly'
                    ? 'selected' : '' }}>
                Monthly
            </option>

            <option value="quarterly"
                {{ $selectedType === 'quarterly'
                    ? 'selected' : '' }}>
                Quarterly
            </option>

            <option value="half_yearly"
                {{ $selectedType === 'half_yearly'
                    ? 'selected' : '' }}>
                Half Yearly
            </option>

            <option value="annual"
                {{ $selectedType === 'annual'
                    ? 'selected' : '' }}>
                Annual
            </option>

            <option value="one_time"
                {{ $selectedType === 'one_time'
                    ? 'selected' : '' }}>
                One Time
            </option>

            <option value="custom"
                {{ $selectedType === 'custom'
                    ? 'selected' : '' }}>
                Custom
            </option>

        </select>

    </div>


    <div class="col-md-4">

        <label class="form-label">
            No. of Installments
            <span class="text-danger">*</span>
        </label>

        <input type="number"
               name="installments_count"
               id="installments_count"
               class="form-control"
               min="1"
               max="24"
               value="{{ old(
                    'installments_count',
                    $feeCycle->installments_count ?? 12
               ) }}"
               required>

        <div class="form-text">
            Number of occurrences in one academic year.
        </div>

    </div>


    <div class="col-md-4">

        <label class="form-label">
            Sort Order
        </label>

        <input type="number"
               name="sort_order"
               class="form-control"
               min="0"
               value="{{ old(
                    'sort_order',
                    $feeCycle->sort_order ?? 0
               ) }}">

    </div>


    <div class="col-md-4">

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
                        isset($feeCycle)
                            ? $feeCycle->status
                            : true
                   ) ? 'checked' : '' }}>

            <label class="form-check-label"
                   for="status">
                Active
            </label>

        </div>

    </div>

</div>


<script>
document.addEventListener(
    'DOMContentLoaded',
    function () {

        const type =
            document.getElementById('cycle_type');

        const count =
            document.getElementById(
                'installments_count'
            );

        const defaults = {
            monthly: 12,
            quarterly: 4,
            half_yearly: 2,
            annual: 1,
            one_time: 1
        };


        type.addEventListener(
            'change',
            function () {

                if (
                    Object.prototype.hasOwnProperty.call(
                        defaults,
                        this.value
                    )
                ) {
                    count.value =
                        defaults[this.value];
                }
            }
        );

    }
);
</script>