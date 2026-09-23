<div class="row">

    {{-- Academic Year --}}
    <div class="col-md-6 mb-3">

        <label class="form-label">
            Academic Year
            <span class="text-danger">*</span>
        </label>

        <select
            name="academic_year_id"
            id="academic_year_id"
            class="form-select @error('academic_year_id') is-invalid @enderror"
            required
        >

            <option value="">
                Select Academic Year
            </option>

            @foreach($academicYears as $year)

                <option
                    value="{{ $year->id }}"
                    @selected(
                        old(
                            'academic_year_id',
                            $sectionGroup->academic_year_id ?? ''
                        ) == $year->id
                    )
                >

                    {{ $year->name }}

                    @if($year->is_current)
                        (Current)
                    @endif

                </option>

            @endforeach

        </select>

        @error('academic_year_id')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>


    {{-- Group Name --}}
    <div class="col-md-6 mb-3">

        <label class="form-label">
            Group Name
            <span class="text-danger">*</span>
        </label>

        <input
            type="text"
            name="name"
            class="form-control @error('name') is-invalid @enderror"
            value="{{ old(
                'name',
                $sectionGroup->name ?? ''
            ) }}"
            placeholder="Example: Primary Group A"
            required
        >

        @error('name')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

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
                        $sectionGroup->status ?? true
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


<hr>


<div class="d-flex justify-content-between align-items-center mb-3">

    <div>
        <h6 class="mb-1">
            Select Sections
        </h6>

        <small class="text-muted">
            Select an academic year to load sections.
        </small>
    </div>

    <div>

        <button
            type="button"
            id="selectAllSections"
            class="btn btn-sm btn-outline-primary"
        >
            Select All
        </button>

        <button
            type="button"
            id="clearSections"
            class="btn btn-sm btn-outline-secondary"
        >
            Clear
        </button>

    </div>

</div>


@error('sections')
    <div class="alert alert-danger">
        {{ $message }}
    </div>
@enderror


<div
    id="sectionContainer"
    class="border rounded p-3"
>

    <div class="text-muted text-center py-4">

        Select Academic Year to load sections.

    </div>

</div>