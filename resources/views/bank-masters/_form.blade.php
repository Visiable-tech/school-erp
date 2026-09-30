<div class="row g-3">

    <div class="col-md-5">

        <label class="form-label">
            Bank Name
            <span class="text-danger">*</span>
        </label>

        <input type="text"
               name="bank_name"
               class="form-control"
               value="{{ old(
                    'bank_name',
                    $bankMaster->bank_name ?? ''
               ) }}"
               placeholder="Example: State Bank of India"
               required>

    </div>


    <div class="col-md-3">

        <label class="form-label">
            Short Name
        </label>

        <input type="text"
               name="short_name"
               class="form-control"
               value="{{ old(
                    'short_name',
                    $bankMaster->short_name ?? ''
               ) }}"
               placeholder="SBI">

    </div>


    <div class="col-md-4">

        <label class="form-label">
            Bank Code
        </label>

        <input type="text"
               name="bank_code"
               class="form-control"
               value="{{ old(
                    'bank_code',
                    $bankMaster->bank_code ?? ''
               ) }}"
               placeholder="SBI">

    </div>


    <div class="col-md-4">

        <label class="form-label">
            Branch Name
        </label>

        <input type="text"
               name="branch_name"
               class="form-control"
               value="{{ old(
                    'branch_name',
                    $bankMaster->branch_name ?? ''
               ) }}"
               placeholder="Howrah">

    </div>


    <div class="col-md-4">

        <label class="form-label">
            Branch Code
        </label>

        <input type="text"
               name="branch_code"
               class="form-control"
               value="{{ old(
                    'branch_code',
                    $bankMaster->branch_code ?? ''
               ) }}">

    </div>


    <div class="col-md-4">

        <label class="form-label">
            IFSC Code
        </label>

        <input type="text"
               name="ifsc_code"
               class="form-control text-uppercase"
               value="{{ old(
                    'ifsc_code',
                    $bankMaster->ifsc_code ?? ''
               ) }}"
               placeholder="SBIN0001234">

    </div>


    <div class="col-md-4">

        <label class="form-label">
            MICR Code
        </label>

        <input type="text"
               name="micr_code"
               class="form-control"
               value="{{ old(
                    'micr_code',
                    $bankMaster->micr_code ?? ''
               ) }}">

    </div>


    <div class="col-md-4">

        <label class="form-label">
            City
        </label>

        <input type="text"
               name="city"
               class="form-control"
               value="{{ old(
                    'city',
                    $bankMaster->city ?? ''
               ) }}">

    </div>


    <div class="col-md-4">

        <label class="form-label">
            Contact Person
        </label>

        <input type="text"
               name="contact_person"
               class="form-control"
               value="{{ old(
                    'contact_person',
                    $bankMaster->contact_person ?? ''
               ) }}">

    </div>


    <div class="col-md-4">

        <label class="form-label">
            Phone
        </label>

        <input type="text"
               name="phone"
               class="form-control"
               value="{{ old(
                    'phone',
                    $bankMaster->phone ?? ''
               ) }}">

    </div>


    <div class="col-md-4">

        <label class="form-label">
            Email
        </label>

        <input type="email"
               name="email"
               class="form-control"
               value="{{ old(
                    'email',
                    $bankMaster->email ?? ''
               ) }}">

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
                    $bankMaster->sort_order ?? 0
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
                   id="status"
                   class="form-check-input"
                   {{ old(
                       'status',
                       isset($bankMaster)
                            ? $bankMaster->status
                            : true
                   ) ? 'checked' : '' }}>

            <label class="form-check-label"
                   for="status">
                Active
            </label>

        </div>

    </div>


    <div class="col-md-12">

        <label class="form-label">
            Branch Address
        </label>

        <textarea name="address"
                  rows="3"
                  class="form-control">{{ old(
                    'address',
                    $bankMaster->address ?? ''
                  ) }}</textarea>

    </div>

</div>