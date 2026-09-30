<div class="row g-3">

    <div class="col-md-6">

        <label class="form-label">
            Group Name
            <span class="text-danger">*</span>
        </label>

        <input type="text"
               name="name"
               class="form-control"
               value="{{ old(
                    'name',
                    $feeComponentGroup->name ?? ''
               ) }}"
               placeholder="Example: Academic Fees"
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
                    $feeComponentGroup->code ?? ''
               ) }}"
               placeholder="ACADEMIC">

    </div>


    <div class="col-md-3">

        <label class="form-label">
            Sort Order
        </label>

        <input type="number"
               name="sort_order"
               class="form-control"
               min="0"
               value="{{ old(
                    'sort_order',
                    $feeComponentGroup->sort_order ?? 0
               ) }}">

    </div>


    <div class="col-md-9">

        <label class="form-label">
            Description
        </label>

        <textarea name="description"
                  class="form-control"
                  rows="3"
                  placeholder="Optional description">{{ old(
                    'description',
                    $feeComponentGroup->description ?? ''
                  ) }}</textarea>

    </div>


    <div class="col-md-3">

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
                        isset($feeComponentGroup)
                            ? $feeComponentGroup->status
                            : true
                   ) ? 'checked' : '' }}>

            <label class="form-check-label"
                   for="status">
                Active
            </label>

        </div>

    </div>

</div>