<div class="row g-3">

    {{-- Academic Year --}}

    <div class="col-md-4">

        <label class="form-label">
            Academic Year
            <span class="text-danger">*</span>
        </label>

        <select
            name="academic_year_id"
            class="form-select @error('academic_year_id') is-invalid @enderror"
            required
        >

            <option value="">
                Select Academic Year
            </option>

            @foreach($academicYears as $year)

                <option
                    value="{{ $year->id }}"
                    @selected(
                        old(
                            'academic_year_id',
                            $feeStructure->academic_year_id
                                ?? $currentAcademicYear?->id
                                ?? ''
                        ) == $year->id
                    )
                >
                    {{ $year->name }}

                    @if($year->is_current)
                        (Current)
                    @endif
                </option>

            @endforeach

        </select>

        @error('academic_year_id')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>


    {{-- Class --}}

    <div class="col-md-4">

        <label class="form-label">
            Class
            <span class="text-danger">*</span>
        </label>

        <select
            name="school_class_id"
            class="form-select @error('school_class_id') is-invalid @enderror"
            required
        >

            <option value="">
                Select Class
            </option>

            @foreach($classes as $class)

                <option
                    value="{{ $class->id }}"
                    @selected(
                        old(
                            'school_class_id',
                            $feeStructure->school_class_id ?? ''
                        ) == $class->id
                    )
                >
                    {{ $class->name }}
                </option>

            @endforeach

        </select>

        @error('school_class_id')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>


    {{-- Structure Name --}}

    <div class="col-md-4">

        <label class="form-label">
            Structure Name
            <span class="text-danger">*</span>
        </label>

        <input
            type="text"
            name="name"
            class="form-control @error('name') is-invalid @enderror"
            value="{{ old(
                'name',
                $feeStructure->name ?? ''
            ) }}"
            placeholder="Example: Class I Regular Fee"
            required
        >

        @error('name')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>


    {{-- Description --}}

    <div class="col-md-9">

        <label class="form-label">
            Description
        </label>

        <textarea
            name="description"
            class="form-control"
            rows="2"
        >{{ old(
            'description',
            $feeStructure->description ?? ''
        ) }}</textarea>

    </div>


    {{-- Status --}}

    <div class="col-md-3">

        <label class="form-label d-block">
            Status
        </label>

        <input
            type="hidden"
            name="status"
            value="0"
        >

        <div class="form-check form-switch mt-2">

            <input
                type="checkbox"
                name="status"
                value="1"
                id="status"
                class="form-check-input"
                @checked(
                    old(
                        'status',
                        isset($feeStructure)
                            ? $feeStructure->status
                            : true
                    )
                )
            >

            <label
                for="status"
                class="form-check-label"
            >
                Active
            </label>

        </div>

    </div>

</div>


<hr class="my-4">


<div class="d-flex justify-content-between align-items-center mb-3">

    <div>

        <h5 class="mb-1">
            Fee Items
        </h5>

        <small class="text-muted">
            Select the applicable Fee Heads and enter their amounts.
        </small>

    </div>

    <button
        type="button"
        class="btn btn-sm btn-outline-primary"
        id="selectAllHeads"
    >
        Select All
    </button>

</div>


@error('items')
    <div class="alert alert-danger">
        {{ $message }}
    </div>
@enderror


<div class="table-responsive">

    <table class="table table-bordered align-middle">

        <thead class="table-light">

            <tr>

                <th width="70">
                    Use
                </th>

                <th>
                    Fee Head
                </th>

                <th width="150">
                    Frequency
                </th>

                <th width="180">
                    Amount
                </th>

            </tr>

        </thead>

        <tbody>

            @php

                /*
                 * Convert existing/old rows into a map:
                 * FeeHead ID => amount
                 */

                $selectedItems = [];

                if (old('items')) {

                    foreach (
                        old('items', [])
                        as $oldItem
                    ) {

                        if (
                            !empty(
                                $oldItem['fee_head_id']
                            )
                        ) {

                            $selectedItems[
                                $oldItem['fee_head_id']
                            ] =
                                $oldItem['amount'] ?? '';

                        }
                    }

                } elseif (isset($feeStructure)) {

                    foreach (
                        $feeStructure->items
                        as $existingItem
                    ) {

                        $selectedItems[
                            $existingItem->fee_head_id
                        ] =
                            $existingItem->amount;

                    }
                }

            @endphp


            @foreach($feeHeads as $feeHead)

                @php
                    $isSelected =
                        array_key_exists(
                            $feeHead->id,
                            $selectedItems
                        );
                @endphp

                <tr>

                    <td class="text-center">

                        <input
                            type="checkbox"
                            class="form-check-input fee-head-check"
                            data-head="{{ $feeHead->id }}"
                            @checked($isSelected)
                        >

                    </td>


                    <td>

                        <strong>
                            {{ $feeHead->name }}
                        </strong>

                        @if($feeHead->code)

                            <small class="text-muted ms-1">
                                ({{ $feeHead->code }})
                            </small>

                        @endif


                        @if($feeHead->is_optional)

                            <span class="badge bg-info text-dark ms-1">
                                Optional
                            </span>

                        @endif

                    </td>


                    <td>

                        {{ ucwords(
                            str_replace(
                                '_',
                                ' ',
                                $feeHead->frequency
                            )
                        ) }}

                    </td>


                    <td>

                        <input
                            type="hidden"
                            class="fee-head-id"
                            data-head="{{ $feeHead->id }}"
                            value="{{ $feeHead->id }}"
                            {{ $isSelected ? '' : 'disabled' }}
                        >

                        <div class="input-group">

                            <span class="input-group-text">
                                ₹
                            </span>

                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                class="form-control fee-amount"
                                data-head="{{ $feeHead->id }}"
                                value="{{ $selectedItems[$feeHead->id] ?? '' }}"
                                placeholder="0.00"
                                {{ $isSelected ? '' : 'disabled' }}
                            >

                        </div>

                    </td>

                </tr>

            @endforeach

        </tbody>

    </table>

</div>


<script>
document.addEventListener(
    'DOMContentLoaded',
    function () {

        const checks =
            document.querySelectorAll(
                '.fee-head-check'
            );


        function rebuildNames() {

            let index = 0;

            checks.forEach(function (check) {

                const headId =
                    check.dataset.head;

                const hidden =
                    document.querySelector(
                        '.fee-head-id[data-head="' +
                        headId +
                        '"]'
                    );

                const amount =
                    document.querySelector(
                        '.fee-amount[data-head="' +
                        headId +
                        '"]'
                    );


                if (check.checked) {

                    hidden.disabled = false;
                    amount.disabled = false;
                    amount.required = true;

                    hidden.name =
                        'items[' +
                        index +
                        '][fee_head_id]';

                    amount.name =
                        'items[' +
                        index +
                        '][amount]';

                    index++;

                }
                else {

                    hidden.disabled = true;
                    amount.disabled = true;
                    amount.required = false;

                    hidden.removeAttribute(
                        'name'
                    );

                    amount.removeAttribute(
                        'name'
                    );

                }

            });

        }


        checks.forEach(function (check) {

            check.addEventListener(
                'change',
                rebuildNames
            );

        });


        document.getElementById(
            'selectAllHeads'
        ).addEventListener(
            'click',
            function () {

                const allSelected =
                    Array.from(checks)
                        .every(
                            checkbox =>
                                checkbox.checked
                        );


                checks.forEach(
                    function (checkbox) {

                        checkbox.checked =
                            !allSelected;

                    }
                );


                this.textContent =
                    allSelected
                        ? 'Select All'
                        : 'Clear All';


                rebuildNames();

            }
        );


        rebuildNames();

    }
);
</script>