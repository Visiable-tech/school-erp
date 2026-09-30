<div class="row g-3">

    <div class="col-md-4">

        <label class="form-label">
            Rule Name *
        </label>

        <input type="text"
               name="name"
               class="form-control"
               value="{{ old(
                   'name',
                   $lateFeeFine->name ?? ''
               ) }}"
               placeholder="Quarterly Late Fee"
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
                   $lateFeeFine->code ?? ''
               ) }}"
               placeholder="QLF">

    </div>


    <div class="col-md-3">

        <label class="form-label">
            Academic Year
        </label>

        <select name="academic_year_id"
                class="form-select">

            <option value="">
                All Years
            </option>

            @foreach($academicYears as $year)

                <option value="{{ $year->id }}"
                    {{ old(
                        'academic_year_id',
                        $lateFeeFine->academic_year_id ?? ''
                    ) == $year->id
                        ? 'selected'
                        : '' }}>

                    {{ $year->name }}

                </option>

            @endforeach

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
                   $lateFeeFine->sort_order ?? 0
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
                    $lateFeeFine->description ?? ''
                  ) }}</textarea>

    </div>


    <div class="col-md-3">

        <div class="form-check form-switch">

            <input type="checkbox"
                   name="is_default"
                   value="1"
                   id="is_default"
                   class="form-check-input"
                   {{ old(
                       'is_default',
                       $lateFeeFine->is_default ?? false
                   ) ? 'checked' : '' }}>

            <label class="form-check-label"
                   for="is_default">

                Default Fine Rule

            </label>

        </div>

    </div>


    <div class="col-md-3">

        <div class="form-check form-switch">

            <input type="checkbox"
                   name="status"
                   value="1"
                   id="status"
                   class="form-check-input"
                   {{ old(
                       'status',
                       isset($lateFeeFine)
                           ? $lateFeeFine->status
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
            Fine Slabs
        </h5>

        <small class="text-muted">
            Configure date-wise late fee amounts.
        </small>

    </div>

    <button type="button"
            class="btn btn-sm btn-primary"
            id="addSlab">

        <i class="bi bi-plus-circle"></i>
        Add Slab

    </button>

</div>


<div class="table-responsive">

<table class="table table-bordered align-middle">

    <thead class="table-light">

        <tr>
            <th>Type</th>
            <th width="140">From Day</th>
            <th width="140">To Day</th>
            <th width="180">Fine Amount</th>
            <th width="80"></th>
        </tr>

    </thead>

    <tbody id="slabRows">

    @php

        $defaultSlabs = [
            [
                'period_type' => 'day_range',
                'from_day' => 1,
                'to_day' => 10,
                'amount' => 0
            ],
            [
                'period_type' => 'day_range',
                'from_day' => 11,
                'to_day' => 20,
                'amount' => 100
            ],
            [
                'period_type' => 'day_range',
                'from_day' => 21,
                'to_day' => null,
                'amount' => 200
            ],
            [
                'period_type' => 'after_first_month',
                'from_day' => null,
                'to_day' => null,
                'amount' => 500
            ],
        ];

        $slabs = old(
            'slabs',
            isset($lateFeeFine)
                ? $lateFeeFine->slabs
                    ->map(fn($slab) => [
                        'period_type' =>
                            $slab->period_type,
                        'from_day' =>
                            $slab->from_day,
                        'to_day' =>
                            $slab->to_day,
                        'amount' =>
                            $slab->amount,
                    ])->toArray()
                : $defaultSlabs
        );

    @endphp


    @foreach($slabs as $index => $slab)

        <tr class="slab-row">

            <td>

                <select
                    name="slabs[{{ $index }}][period_type]"
                    class="form-select slab-type"
                    required>

                    <option value="day_range"
                        {{ $slab['period_type'] === 'day_range'
                            ? 'selected'
                            : '' }}>

                        Day Range

                    </option>

                    <option value="after_first_month"
                        {{ $slab['period_type'] === 'after_first_month'
                            ? 'selected'
                            : '' }}>

                        After First Month

                    </option>

                </select>

            </td>


            <td>

                <input type="number"
                       name="slabs[{{ $index }}][from_day]"
                       class="form-control from-day"
                       min="1"
                       max="31"
                       value="{{ $slab['from_day'] }}">

            </td>


            <td>

                <input type="number"
                       name="slabs[{{ $index }}][to_day]"
                       class="form-control to-day"
                       min="1"
                       max="31"
                       value="{{ $slab['to_day'] }}"
                       placeholder="Month end">

            </td>


            <td>

                <div class="input-group">

                    <span class="input-group-text">
                        ₹
                    </span>

                    <input type="number"
                           name="slabs[{{ $index }}][amount]"
                           class="form-control"
                           min="0"
                           step="0.01"
                           value="{{ $slab['amount'] }}"
                           required>

                </div>

                @if(
                    $slab['period_type']
                    === 'after_first_month'
                )

                    <small class="text-muted">
                        Per month
                    </small>

                @endif

            </td>


            <td class="text-center">

                <button type="button"
                        class="btn btn-sm
                               btn-outline-danger
                               remove-slab">

                    <i class="bi bi-trash"></i>

                </button>

            </td>

        </tr>

    @endforeach

    </tbody>

</table>

</div>


<div class="alert alert-info">

    <strong>Example:</strong>

    1–10 = No Fine,
    11–20 = ₹100,
    21–month end = ₹200,
    after first month = ₹500 per month.

</div>


<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const tbody =
            document.getElementById(
                'slabRows'
            );

        const addButton =
            document.getElementById(
                'addSlab'
            );


        function reIndex() {

            const rows =
                tbody.querySelectorAll(
                    '.slab-row'
                );

            rows.forEach(
                function (row, index) {

                    row.querySelectorAll(
                        'select, input'
                    ).forEach(
                        function (field) {

                            const name =
                                field.getAttribute(
                                    'name'
                                );

                            if (!name) {
                                return;
                            }

                            field.setAttribute(
                                'name',
                                name.replace(
                                    /slabs\[\d+\]/,
                                    'slabs[' +
                                    index +
                                    ']'
                                )
                            );
                        }
                    );
                }
            );
        }


        function updateRow(row) {

            const type =
                row.querySelector(
                    '.slab-type'
                );

            const from =
                row.querySelector(
                    '.from-day'
                );

            const to =
                row.querySelector(
                    '.to-day'
                );


            if (
                type.value ===
                'after_first_month'
            ) {

                from.value = '';
                to.value = '';

                from.disabled = true;
                to.disabled = true;

            } else {

                from.disabled = false;
                to.disabled = false;
            }
        }


        tbody.querySelectorAll(
            '.slab-row'
        ).forEach(updateRow);


        tbody.addEventListener(
            'change',
            function (event) {

                if (
                    event.target.classList
                        .contains('slab-type')
                ) {
                    updateRow(
                        event.target.closest(
                            '.slab-row'
                        )
                    );
                }
            }
        );


        addButton.addEventListener(
            'click',
            function () {

                const index =
                    tbody.querySelectorAll(
                        '.slab-row'
                    ).length;

                const row =
                    document.createElement(
                        'tr'
                    );

                row.className =
                    'slab-row';

                row.innerHTML = `

                    <td>

                        <select
                            name="slabs[${index}][period_type]"
                            class="form-select slab-type"
                            required>

                            <option value="day_range">
                                Day Range
                            </option>

                            <option value="after_first_month">
                                After First Month
                            </option>

                        </select>

                    </td>

                    <td>

                        <input type="number"
                               name="slabs[${index}][from_day]"
                               class="form-control from-day"
                               min="1"
                               max="31">

                    </td>

                    <td>

                        <input type="number"
                               name="slabs[${index}][to_day]"
                               class="form-control to-day"
                               min="1"
                               max="31"
                               placeholder="Month end">

                    </td>

                    <td>

                        <div class="input-group">

                            <span class="input-group-text">
                                ₹
                            </span>

                            <input type="number"
                                   name="slabs[${index}][amount]"
                                   class="form-control"
                                   min="0"
                                   step="0.01"
                                   required>

                        </div>

                    </td>

                    <td class="text-center">

                        <button type="button"
                                class="btn btn-sm
                                       btn-outline-danger
                                       remove-slab">

                            <i class="bi bi-trash"></i>

                        </button>

                    </td>
                `;

                tbody.appendChild(row);
            }
        );


        tbody.addEventListener(
            'click',
            function (event) {

                const button =
                    event.target.closest(
                        '.remove-slab'
                    );

                if (!button) {
                    return;
                }

                const rows =
                    tbody.querySelectorAll(
                        '.slab-row'
                    );

                if (rows.length <= 1) {

                    alert(
                        'At least one fine slab is required.'
                    );

                    return;
                }

                button.closest(
                    '.slab-row'
                ).remove();

                reIndex();
            }
        );

    }
);

</script>