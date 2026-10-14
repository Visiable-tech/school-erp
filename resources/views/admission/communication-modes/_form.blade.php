<div class="row">

    <div class="col-md-6 mb-3">

        <label class="form-label">
            Communication Mode
            <span class="text-danger">*</span>
        </label>

        <input
            type="text"
            name="name"
            class="form-control @error('name') is-invalid @enderror"
            value="{{ old(
                'name',
                $admissionCommunicationMode->name ?? ''
            ) }}"
            placeholder="e.g. Phone, Email, WhatsApp"
            required
        >

        @error('name')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>


    <div class="col-md-3 mb-3">

        <label class="form-label">
            Code
        </label>

        <input
            type="text"
            name="code"
            class="form-control @error('code') is-invalid @enderror"
            value="{{ old(
                'code',
                $admissionCommunicationMode->code ?? ''
            ) }}"
            placeholder="e.g. PHONE"
        >

        @error('code')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>


    <div class="col-md-3 mb-3">

        <label class="form-label">
            Sort Order
        </label>

        <input
            type="number"
            name="sort_order"
            min="0"
            class="form-control"
            value="{{ old(
                'sort_order',
                $admissionCommunicationMode->sort_order ?? 0
            ) }}"
        >

    </div>


    <div class="col-md-3 mb-3">

        <label class="form-label">
            Status
            <span class="text-danger">*</span>
        </label>

        <select
            name="status"
            class="form-select"
            required
        >

            <option
                value="1"
                @selected(
                    (string) old(
                        'status',
                        isset($admissionCommunicationMode)
                            ? (int) $admissionCommunicationMode->status
                            : 1
                    ) === '1'
                )
            >
                Active
            </option>

            <option
                value="0"
                @selected(
                    (string) old(
                        'status',
                        isset($admissionCommunicationMode)
                            ? (int) $admissionCommunicationMode->status
                            : 1
                    ) === '0'
                )
            >
                Inactive
            </option>

        </select>

    </div>


    <div class="col-md-12 mb-3">

        <label class="form-label">
            Description
        </label>

        <textarea
            name="description"
            rows="3"
            class="form-control"
            placeholder="Optional description"
        >{{ old(
            'description',
            $admissionCommunicationMode->description ?? ''
        ) }}</textarea>

    </div>

</div>