<div class="row">

    {{-- Subject Name --}}
    <div class="col-md-6 mb-3">

        <label class="form-label">
            Subject Name
            <span class="text-danger">*</span>
        </label>

        <input
            type="text"
            name="name"
            class="form-control @error('name') is-invalid @enderror"
            value="{{ old('name', $subject->name ?? '') }}"
            placeholder="Example: Mathematics"
            required
        >

        @error('name')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>


    {{-- Subject Code --}}
    <div class="col-md-6 mb-3">

        <label class="form-label">
            Subject Code
        </label>

        <input
            type="text"
            name="code"
            class="form-control @error('code') is-invalid @enderror"
            value="{{ old('code', $subject->code ?? '') }}"
            placeholder="Example: MATH"
        >

        @error('code')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>


    {{-- Subject Type --}}
    <div class="col-md-6 mb-3">

        <label class="form-label">
            Subject Type
            <span class="text-danger">*</span>
        </label>

        <select
            name="type"
            class="form-select @error('type') is-invalid @enderror"
            required
        >

            <option value="">
                Select Subject Type
            </option>

            <option
                value="theory"
                @selected(
                    old(
                        'type',
                        $subject->type ?? 'theory'
                    ) == 'theory'
                )
            >
                Theory
            </option>

            <option
                value="practical"
                @selected(
                    old(
                        'type',
                        $subject->type ?? ''
                    ) == 'practical'
                )
            >
                Practical
            </option>

            <option
                value="activity"
                @selected(
                    old(
                        'type',
                        $subject->type ?? ''
                    ) == 'activity'
                )
            >
                Activity
            </option>

        </select>

        @error('type')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>


    {{-- Sort Order --}}
    <div class="col-md-6 mb-3">

        <label class="form-label">
            Sort Order
        </label>

        <input
            type="number"
            name="sort_order"
            min="0"
            class="form-control @error('sort_order') is-invalid @enderror"
            value="{{ old(
                'sort_order',
                $subject->sort_order ?? 0
            ) }}"
        >

        @error('sort_order')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>


    {{-- Optional --}}
    <div class="col-md-6 mb-3">

        <div class="form-check mt-2">

            <input
                type="checkbox"
                name="is_optional"
                value="1"
                class="form-check-input"
                id="is_optional"
                @checked(
                    old(
                        'is_optional',
                        $subject->is_optional ?? false
                    )
                )
            >

            <label
                class="form-check-label"
                for="is_optional"
            >
                Optional Subject
            </label>

        </div>

        <small class="text-muted">
            Enable this if students may choose this subject.
        </small>

    </div>


    {{-- Status --}}
    <div class="col-md-6 mb-3">

        <div class="form-check mt-2">

            <input
                type="checkbox"
                name="status"
                value="1"
                class="form-check-input"
                id="status"
                @checked(
                    old(
                        'status',
                        $subject->status ?? true
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