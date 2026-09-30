<div class="row g-3">

    <div class="col-md-4">

        <label class="form-label">
            Academic Year
            <span class="text-danger">*</span>
        </label>

        <select name="academic_year_id"
                class="form-select"
                required>

            <option value="">
                Select Academic Year
            </option>

            @foreach($academicYears as $year)

                <option
                    value="{{ $year->id }}"
                    {{ old(
                        'academic_year_id',
                        $feeStructure->academic_year_id ?? ''
                    ) == $year->id
                        ? 'selected'
                        : '' }}
                >
                    {{ $year->name }}

                    @if($year->is_current)
                        - Current
                    @endif
                </option>

            @endforeach

        </select>

    </div>


    <div class="col-md-4">

        <label class="form-label">
            Class
            <span class="text-danger">*</span>
        </label>

        <select name="school_class_id"
                class="form-select"
                required>

            <option value="">
                Select Class
            </option>

            @foreach($classes as $class)

                <option
                    value="{{ $class->id }}"
                    {{ old(
                        'school_class_id',
                        $feeStructure->school_class_id ?? ''
                    ) == $class->id
                        ? 'selected'
                        : '' }}
                >
                    {{ $class->name }}
                </option>

            @endforeach

        </select>

    </div>


    <div class="col-md-4">

        <label class="form-label">
            Template Name
            <span class="text-danger">*</span>
        </label>

        <input type="text"
               name="name"
               class="form-control"
               value="{{ old(
                    'name',
                    $feeStructure->name ?? ''
               ) }}"
               placeholder="Example: Class V Regular Fee"
               required>

    </div>


    <div class="col-md-9">

        <label class="form-label">
            Description
        </label>

        <textarea name="description"
                  class="form-control"
                  rows="2">{{ old(
                    'description',
                    $feeStructure->description ?? ''
                  ) }}</textarea>

    </div>


    <div class="col-md-3">

        <label class="form-label d-block">
            Status
        </label>

        <div class="form-check form-switch mt-2">

            <input type="hidden"
                   name="status"
                   value="0">

            <input type="checkbox"
                   name="status"
                   value="1"
                   id="status"
                   class="form-check-input"
                   {{ old(
                       'status',
                       isset($feeStructure)
                           ? $feeStructure->status
                           : true
                   ) ? 'checked' : '' }}>

            <label class="form-check-label"
                   for="status">
                Active
            </label>

        </div>

    </div>

</div>


<hr class="my-4">


<div class="d-flex justify-content-between
            align-items-center mb-3">

    <div>
        <h5 class="mb-1">
            Fee Components
        </h5>

        <small class="text-muted">
            Select components and enter amount
            per fee cycle/installment.
        </small>
    </div>

</div>


@php

    $existingItems = collect(
        old(
            'items',
            isset($feeStructure)
                ? $feeStructure->items
                    ->map(function ($item) {
                        return [
                            'fee_head_id' =>
                                $item->fee_head_id,

                            'amount' =>
                                $item->amount,
                        ];
                    })
                    ->toArray()
                : []
        )
    )->keyBy('fee_head_id');

@endphp


<div class="table-responsive">

    <table class="table table-bordered
                  align-middle">

        <thead class="table-light">

            <tr>
                <th width="60">
                    Select
                </th>

                <th>
                    Component
                </th>

                <th>
                    Group
                </th>

                <th>
                    Fee Cycle
                </th>

                <th width="180">
                    Amount / Cycle
                </th>
            </tr>

        </thead>


        <tbody>

            @foreach($feeHeads as $feeHead)

                @php

                    $selectedItem =
                        $existingItems->get(
                            $feeHead->id
                        );

                    $selected =
                        $selectedItem !== null;

                @endphp


                <tr>

                    <td class="text-center">

                        <input type="checkbox"
                               class="form-check-input
                                      component-check"
                               data-id="{{ $feeHead->id }}"
                               {{ $selected
                                    ? 'checked'
                                    : '' }}>

                    </td>


                    <td>

                        <strong>
                            {{ $feeHead->name }}
                        </strong>

                        @if($feeHead->code)

                            <div class="small text-muted">
                                {{ $feeHead->code }}
                            </div>

                        @endif


                        @if($feeHead->is_optional)

                            <span class="badge bg-info mt-1">
                                Optional
                            </span>

                        @endif

                    </td>


                    <td>

                        {{ optional(
                            $feeHead->componentGroup
                        )->name ?: '—' }}

                    </td>


                    <td>

                        @if($feeHead->feeCycle)

                            <strong>
                                {{ $feeHead
                                    ->feeCycle
                                    ->name }}
                            </strong>

                            <div class="small text-muted">

                                {{ $feeHead
                                    ->feeCycle
                                    ->installments_count }}

                                installment(s)

                            </div>

                        @else

                            <span class="text-danger">
                                Cycle not configured
                            </span>

                        @endif

                    </td>


                    <td>

                        <input type="hidden"
                               class="component-id"
                               data-id="{{ $feeHead->id }}"
                               value="{{ $feeHead->id }}"
                               {{ !$selected
                                    ? 'disabled'
                                    : '' }}>

                        <div class="input-group">

                            <span class="input-group-text">
                                ₹
                            </span>

                            <input type="number"
                                   class="form-control
                                          component-amount"
                                   data-id="{{ $feeHead->id }}"
                                   min="0"
                                   step="0.01"

                                   value="{{ $selected
                                        ? $selectedItem['amount']
                                        : '' }}"

                                   placeholder="0.00"

                                   {{ !$selected
                                        ? 'disabled'
                                        : '' }}>

                        </div>

                    </td>

                </tr>

            @endforeach

        </tbody>

    </table>

</div>


<div class="alert alert-info">

    <i class="bi bi-info-circle"></i>

    Amount is entered per fee cycle.

    For example, if Tuition Fee uses a
    Monthly cycle and amount is ₹2,000,
    the system will later generate
    12 installments of ₹2,000.

</div>


<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const checks =
            document.querySelectorAll(
                '.component-check'
            );


        function rebuildNames() {

            let index = 0;

            document
                .querySelectorAll(
                    '.component-check'
                )
                .forEach(function (check) {

                    const id =
                        check.dataset.id;

                    const hidden =
                        document.querySelector(
                            '.component-id[data-id="' +
                            id +
                            '"]'
                        );

                    const amount =
                        document.querySelector(
                            '.component-amount[data-id="' +
                            id +
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

                    } else {

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


        rebuildNames();

    }
);

</script>