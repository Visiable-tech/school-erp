<div class="row">

    <div class="col-md-6 mb-3">

        <label class="form-label">
            Event Type Name
            <span class="text-danger">*</span>
        </label>

        <input
            type="text"
            name="name"
            class="form-control @error('name') is-invalid @enderror"
            value="{{ old(
                'name',
                $calendarEventType->name ?? ''
            ) }}"
            placeholder="Example: Holiday"
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
            class="form-control"
            value="{{ old(
                'code',
                $calendarEventType->code ?? ''
            ) }}"
            placeholder="HOLIDAY"
        >

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
                $calendarEventType->sort_order ?? 0
            ) }}"
        >

    </div>


    <div class="col-md-6 mb-3">

        <div class="form-check">

            <input
                type="checkbox"
                name="is_holiday"
                value="1"
                id="is_holiday"
                class="form-check-input"
                @checked(
                    old(
                        'is_holiday',
                        $calendarEventType->is_holiday ?? false
                    )
                )
            >

            <label
                class="form-check-label"
                for="is_holiday"
            >
                Treat this event type as a holiday
            </label>

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
                        $calendarEventType->status ?? true
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