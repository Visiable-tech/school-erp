<div class="mb-3">
    <label class="form-label">
        Academic Year
    </label>

    <input
        type="text"
        name="name"
        class="form-control @error('name') is-invalid @enderror"
        value="{{ old('name', $academicYear->name ?? '') }}"
        placeholder="Example: 2026-2027"
        required
    >

    @error('name')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>

<div class="row">

    <div class="col-md-6 mb-3">

        <label class="form-label">
            Start Date
        </label>

        <input
            type="date"
            name="start_date"
            class="form-control"
            value="{{ old(
                'start_date',
                isset($academicYear)
                    ? $academicYear->start_date->format('Y-m-d')
                    : ''
            ) }}"
            required
        >

    </div>

    <div class="col-md-6 mb-3">

        <label class="form-label">
            End Date
        </label>

        <input
            type="date"
            name="end_date"
            class="form-control"
            value="{{ old(
                'end_date',
                isset($academicYear)
                    ? $academicYear->end_date->format('Y-m-d')
                    : ''
            ) }}"
            required
        >

    </div>

</div>

<div class="form-check mb-3">

    <input
        type="checkbox"
        name="is_current"
        value="1"
        class="form-check-input"
        id="is_current"
        @checked(
            old(
                'is_current',
                $academicYear->is_current ?? false
            )
        )
    >

    <label
        class="form-check-label"
        for="is_current"
    >
        Current Academic Year
    </label>

</div>

<div class="form-check mb-4">

    <input
        type="checkbox"
        name="status"
        value="1"
        class="form-check-input"
        id="status"
        @checked(
            old(
                'status',
                $academicYear->status ?? true
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