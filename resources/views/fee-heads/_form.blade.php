<div class="row g-3">

    {{-- NAME --}}

    <div class="col-md-6">

        <label class="form-label">
            Fee Head Name
            <span class="text-danger">*</span>
        </label>

        <input
            type="text"
            name="name"
            class="form-control @error('name') is-invalid @enderror"
            value="{{ old('name', $feeHead->name ?? '') }}"
            placeholder="Example: Tuition Fee"
            required
        >

        @error('name')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>


    {{-- CODE --}}

    <div class="col-md-3">

        <label class="form-label">
            Code
        </label>

        <input
            type="text"
            name="code"
            class="form-control @error('code') is-invalid @enderror"
            value="{{ old('code', $feeHead->code ?? '') }}"
            placeholder="Example: TUF"
        >

        @error('code')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>


    {{-- FREQUENCY --}}

    <div class="col-md-3">

        <label class="form-label">
            Frequency
            <span class="text-danger">*</span>
        </label>

        <select
            name="frequency"
            class="form-select @error('frequency') is-invalid @enderror"
            required
        >

            <option value="">
                Select Frequency
            </option>

            @php
                $frequencies = [
                    'one_time' => 'One Time',
                    'monthly' => 'Monthly',
                    'quarterly' => 'Quarterly',
                    'half_yearly' => 'Half Yearly',
                    'annual' => 'Annual',
                ];
            @endphp

            @foreach($frequencies as $key => $label)

                <option
                    value="{{ $key }}"
                    @selected(
                        old(
                            'frequency',
                            $feeHead->frequency ?? 'monthly'
                        ) == $key
                    )
                >
                    {{ $label }}
                </option>

            @endforeach

        </select>

        @error('frequency')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>


    {{-- SORT ORDER --}}

    <div class="col-md-3">

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
                $feeHead->sort_order ?? 0
            ) }}"
        >

    </div>


    {{-- OPTIONAL --}}

    <div class="col-md-3">

        <label class="form-label d-block">
            Optional Fee?
        </label>

        <input
            type="hidden"
            name="is_optional"
            value="0"
        >

        <div class="form-check form-switch mt-2">

            <input
                type="checkbox"
                name="is_optional"
                value="1"
                class="form-check-input"
                id="is_optional"
                @checked(
                    old(
                        'is_optional',
                        $feeHead->is_optional ?? false
                    )
                )
            >

            <label
                class="form-check-label"
                for="is_optional"
            >
                Yes
            </label>

        </div>

    </div>


    {{-- STATUS --}}

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
                class="form-check-input"
                id="status"
                @checked(
                    old(
                        'status',
                        isset($feeHead)
                            ? $feeHead->status
                            : true
                    )
                )
            >

            <label
                class="form-check-label"
                for="status"
            >
                Active
            </label>

        </div>

    </div>

</div>