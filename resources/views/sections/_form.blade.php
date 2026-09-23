<div class="row">

    {{-- Academic Year --}}
    <div class="col-md-6 mb-3">

        <label class="form-label">
            Academic Year
            <span class="text-danger">*</span>
        </label>

        <select
            name="academic_year_id"
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
                            $section->academic_year_id ?? ''
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


    {{-- Class --}}
    <div class="col-md-6 mb-3">

        <label class="form-label">
            Class
            <span class="text-danger">*</span>
        </label>

        <select
            name="school_class_id"
            class="form-select @error('school_class_id') is-invalid @enderror"
            required
        >

            <option value="">
                Select Class
            </option>

            @foreach($classes as $class)

                <option
                    value="{{ $class->id }}"
                    @selected(
                        old(
                            'school_class_id',
                            $section->school_class_id ?? ''
                        ) == $class->id
                    )
                >

                    {{ $class->name }}

                    @if($class->wing)
                        - {{ $class->wing->name }}
                    @endif

                </option>

            @endforeach

        </select>

        @error('school_class_id')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>


    {{-- Section --}}
    <div class="col-md-6 mb-3">

        <label class="form-label">
            Section Name
            <span class="text-danger">*</span>
        </label>

        <input
            type="text"
            name="name"
            class="form-control @error('name') is-invalid @enderror"
            value="{{ old('name', $section->name ?? '') }}"
            placeholder="Example: A"
            required
        >

        @error('name')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>


    {{-- Capacity --}}
    <div class="col-md-6 mb-3">

        <label class="form-label">
            Capacity
        </label>

        <input
            type="number"
            name="capacity"
            min="1"
            class="form-control @error('capacity') is-invalid @enderror"
            value="{{ old('capacity', $section->capacity ?? '') }}"
            placeholder="Example: 40"
        >

        @error('capacity')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>


    {{-- Sort --}}
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
                $section->sort_order ?? 0
            ) }}"
        >

    </div>


    {{-- Status --}}
    <div class="col-md-6 mb-3">

        <label class="form-label d-block">
            Status
        </label>

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
                        $section->status ?? true
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