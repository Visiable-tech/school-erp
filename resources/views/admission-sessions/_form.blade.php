<div class="row">

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
                            $admissionSession->academic_year_id ?? ''
                        ) == $year->id
                    )
                >

                    {{ $year->name }}

                    @if($year->is_current)
                        (Current Academic Year)
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


    <div class="col-md-6 mb-3">

        <label class="form-label">
            Session Name
            <span class="text-danger">*</span>
        </label>

        <input
            type="text"
            name="name"
            class="form-control @error('name') is-invalid @enderror"
            value="{{ old(
                'name',
                $admissionSession->name ?? ''
            ) }}"
            placeholder="Example: Admission 2027-28"
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
            Start Date
        </label>

        <input
            type="date"
            name="start_date"
            class="form-control @error('start_date') is-invalid @enderror"
            value="{{ old(
                'start_date',
                isset($admissionSession)
                    && $admissionSession->start_date
                    ? $admissionSession
                        ->start_date
                        ->format('Y-m-d')
                    : ''
            ) }}"
        >

        @error('start_date')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>


    <div class="col-md-6 mb-3">

        <label class="form-label">
            End Date
        </label>

        <input
            type="date"
            name="end_date"
            class="form-control @error('end_date') is-invalid @enderror"
            value="{{ old(
                'end_date',
                isset($admissionSession)
                    && $admissionSession->end_date
                    ? $admissionSession
                        ->end_date
                        ->format('Y-m-d')
                    : ''
            ) }}"
        >

        @error('end_date')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>


    <div class="col-md-6 mb-3">

        <div class="form-check">

            <input
                type="checkbox"
                name="is_current"
                value="1"
                id="is_current"
                class="form-check-input"
                @checked(
                    old(
                        'is_current',
                        $admissionSession->is_current ?? false
                    )
                )
            >

            <label
                class="form-check-label"
                for="is_current"
            >
                Current Admission Session
            </label>

        </div>

        <div class="form-text">
            Selecting this will remove Current status from
            the previous admission session.
        </div>

    </div>


    <div class="col-md-6 mb-3">

        <div class="form-check">

            <input
                type="checkbox"
                name="status"
                value="1"
                id="status"
                class="form-check-input"
                @checked(
                    old(
                        'status',
                        $admissionSession->status ?? true
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