<div class="row g-3">

    <div class="col-md-4">
        <label class="form-label">
            Scheme Name
            <span class="text-danger">*</span>
        </label>

        <input type="text"
               name="name"
               class="form-control"
               value="{{ old(
                    'name',
                    $feeReceiptScheme->name ?? ''
               ) }}"
               placeholder="Regular Fee Receipt"
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
                    $feeReceiptScheme->code ?? ''
               ) }}"
               placeholder="REG">
    </div>


    <div class="col-md-3">
        <label class="form-label">
            Academic Year
        </label>

        <select name="academic_year_id"
                class="form-select">

            <option value="">
                General / All Years
            </option>

            @foreach($academicYears as $year)

                <option value="{{ $year->id }}"
                    {{ old(
                        'academic_year_id',
                        $feeReceiptScheme->academic_year_id ?? ''
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
            Prefix
        </label>

        <input type="text"
               name="prefix"
               id="receipt_prefix"
               class="form-control"
               value="{{ old(
                    'prefix',
                    $feeReceiptScheme->prefix ?? 'FEE'
               ) }}"
               placeholder="FEE">
    </div>


    <div class="col-md-2">
        <label class="form-label">
            Separator
        </label>

        <select name="separator"
                id="receipt_separator"
                class="form-select">

            @php
                $separator = old(
                    'separator',
                    $feeReceiptScheme->separator ?? '/'
                );
            @endphp

            <option value="/"
                {{ $separator === '/' ? 'selected' : '' }}>
                /
            </option>

            <option value="-"
                {{ $separator === '-' ? 'selected' : '' }}>
                -
            </option>

            <option value="_"
                {{ $separator === '_' ? 'selected' : '' }}>
                _
            </option>

            <option value="."
                {{ $separator === '.' ? 'selected' : '' }}>
                .
            </option>

        </select>
    </div>


    <div class="col-md-2">
        <label class="form-label">
            Running Digits
        </label>

        <input type="number"
               name="number_length"
               id="number_length"
               min="1"
               max="12"
               class="form-control"
               value="{{ old(
                    'number_length',
                    $feeReceiptScheme->number_length ?? 6
               ) }}"
               required>
    </div>


    <div class="col-md-2">
        <label class="form-label">
            Start Number
        </label>

        <input type="number"
               name="start_number"
               id="start_number"
               min="1"
               class="form-control"
               value="{{ old(
                    'start_number',
                    $feeReceiptScheme->start_number ?? 1
               ) }}"
               required>
    </div>


    <div class="col-md-3">
        <label class="form-label">
            Suffix
        </label>

        <input type="text"
               name="suffix"
               id="receipt_suffix"
               class="form-control"
               value="{{ old(
                    'suffix',
                    $feeReceiptScheme->suffix ?? ''
               ) }}"
               placeholder="Optional">
    </div>


    <div class="col-md-3">
        <label class="form-label">
            Current Last Number
        </label>

        <input type="text"
               class="form-control"
               value="{{ isset($feeReceiptScheme)
                    ? $feeReceiptScheme->last_number
                    : 0 }}"
               disabled>

        <small class="text-muted">
            Updated automatically by Fee Collection.
        </small>
    </div>


    <div class="col-md-12">

        <div class="card bg-light border-0">

            <div class="card-body">

                <div class="row g-3">

                    <div class="col-md-3">

                        <div class="form-check form-switch">

                            <input type="checkbox"
                                   name="include_academic_year"
                                   value="1"
                                   id="include_academic_year"
                                   class="form-check-input"
                                   {{ old(
                                       'include_academic_year',
                                       isset($feeReceiptScheme)
                                           ? $feeReceiptScheme->include_academic_year
                                           : true
                                   ) ? 'checked' : '' }}>

                            <label class="form-check-label"
                                   for="include_academic_year">

                                Include Academic Year

                            </label>

                        </div>

                    </div>


                    <div class="col-md-3">

                        <div class="form-check form-switch">

                            <input type="checkbox"
                                   name="reset_yearly"
                                   value="1"
                                   id="reset_yearly"
                                   class="form-check-input"
                                   {{ old(
                                       'reset_yearly',
                                       isset($feeReceiptScheme)
                                           ? $feeReceiptScheme->reset_yearly
                                           : true
                                   ) ? 'checked' : '' }}>

                            <label class="form-check-label"
                                   for="reset_yearly">

                                Reset Every Academic Year

                            </label>

                        </div>

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
                                       $feeReceiptScheme->is_default ?? false
                                   ) ? 'checked' : '' }}>

                            <label class="form-check-label"
                                   for="is_default">

                                Default Scheme

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
                                       isset($feeReceiptScheme)
                                           ? $feeReceiptScheme->status
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
                    $feeReceiptScheme->sort_order ?? 0
               ) }}">

    </div>


    <div class="col-md-10">

        <label class="form-label">
            Receipt Number Preview
        </label>

        <div class="form-control bg-light"
             id="receiptPreview">

            FEE/2026-27/000001

        </div>

    </div>

</div>