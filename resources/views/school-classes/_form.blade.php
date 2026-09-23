<div class="row">

    <div class="col-md-6 mb-3">

        <label class="form-label">
            Wing <span class="text-danger">*</span>
        </label>

        <select
            name="wing_id"
            class="form-select @error('wing_id') is-invalid @enderror"
            required
        >

            <option value="">
                Select Wing
            </option>

            @foreach($wings as $wing)

                <option
                    value="{{ $wing->id }}"
                    @selected(
                        old(
                            'wing_id',
                            $schoolClass->wing_id ?? ''
                        ) == $wing->id
                    )
                >
                    {{ $wing->name }}
                </option>

            @endforeach

        </select>

        @error('wing_id')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>


    <div class="col-md-6 mb-3">

        <label class="form-label">
            Class Name <span class="text-danger">*</span>
        </label>

        <input
            type="text"
            name="name"
            class="form-control @error('name') is-invalid @enderror"
            value="{{ old('name', $schoolClass->name ?? '') }}"
            placeholder="Example: Class I"
            required
        >

        @error('name')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>


    <div class="col-md-6 mb-3">

        <label class="form-label">
            Class Code
        </label>

        <input
            type="text"
            name="code"
            class="form-control @error('code') is-invalid @enderror"
            value="{{ old('code', $schoolClass->code ?? '') }}"
            placeholder="Example: I"
        >

        @error('code')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>


    <div class="col-md-6 mb-3">

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
                $schoolClass->sort_order ?? 0
            ) }}"
        >

    </div>


    <div class="col-md-12">

        <div class="form-check">

            <input
                type="checkbox"
                name="status"
                value="1"
                class="form-check-input"
                id="status"
                @checked(
                    old(
                        'status',
                        $schoolClass->status ?? true
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