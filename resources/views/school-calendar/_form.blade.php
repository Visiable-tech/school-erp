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
                            $schoolCalendarEvent->academic_year_id ?? ''
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


    <div class="col-md-6 mb-3">

        <label class="form-label">
            Event Type
        </label>

        <select
            name="calendar_event_type_id"
            id="calendar_event_type_id"
            class="form-select"
        >

            <option value="">
                Select Event Type
            </option>

            @foreach($eventTypes as $type)

                <option
                    value="{{ $type->id }}"
                    data-holiday="{{ $type->is_holiday ? 1 : 0 }}"
                    @selected(
                        old(
                            'calendar_event_type_id',
                            $schoolCalendarEvent->calendar_event_type_id ?? ''
                        ) == $type->id
                    )
                >
                    {{ $type->name }}
                </option>

            @endforeach

        </select>

    </div>


    <div class="col-md-12 mb-3">

        <label class="form-label">
            Event Title
            <span class="text-danger">*</span>
        </label>

        <input
            type="text"
            name="title"
            class="form-control @error('title') is-invalid @enderror"
            value="{{ old(
                'title',
                $schoolCalendarEvent->title ?? ''
            ) }}"
            placeholder="Example: Durga Puja Holiday"
            required
        >

        @error('title')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>


    <div class="col-md-12 mb-3">

        <label class="form-label">
            Description
        </label>

        <textarea
            name="description"
            class="form-control"
            rows="3"
        >{{ old(
            'description',
            $schoolCalendarEvent->description ?? ''
        ) }}</textarea>

    </div>


    <div class="col-md-6 mb-3">

        <label class="form-label">
            Start Date & Time
            <span class="text-danger">*</span>
        </label>

        <input
            type="datetime-local"
            name="start_at"
            class="form-control @error('start_at') is-invalid @enderror"
            value="{{ old(
                'start_at',
                isset($schoolCalendarEvent)
                    && $schoolCalendarEvent->start_at
                    ? $schoolCalendarEvent
                        ->start_at
                        ->format('Y-m-d\TH:i')
                    : ''
            ) }}"
            required
        >

        @error('start_at')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>


    <div class="col-md-6 mb-3">

        <label class="form-label">
            End Date & Time
        </label>

        <input
            type="datetime-local"
            name="end_at"
            class="form-control @error('end_at') is-invalid @enderror"
            value="{{ old(
                'end_at',
                isset($schoolCalendarEvent)
                    && $schoolCalendarEvent->end_at
                    ? $schoolCalendarEvent
                        ->end_at
                        ->format('Y-m-d\TH:i')
                    : ''
            ) }}"
        >

        @error('end_at')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>


    <div class="col-md-4 mb-3">

        <div class="form-check">

            <input
                type="checkbox"
                name="is_all_day"
                value="1"
                id="is_all_day"
                class="form-check-input"
                @checked(
                    old(
                        'is_all_day',
                        $schoolCalendarEvent->is_all_day ?? false
                    )
                )
            >

            <label
                class="form-check-label"
                for="is_all_day"
            >
                All Day Event
            </label>

        </div>

    </div>


    <div class="col-md-4 mb-3">

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
                        $schoolCalendarEvent->is_holiday ?? false
                    )
                )
            >

            <label
                class="form-check-label"
                for="is_holiday"
            >
                Holiday
            </label>

        </div>

    </div>


    <div class="col-md-4 mb-3">

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
                        $schoolCalendarEvent->status ?? true
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

<script>
document.addEventListener('DOMContentLoaded', function () {

    const eventType =
        document.getElementById('calendar_event_type_id');

    const holiday =
        document.getElementById('is_holiday');

    eventType.addEventListener('change', function () {

        const selected =
            this.options[this.selectedIndex];

        holiday.checked =
            selected.dataset.holiday === '1';

    });

});
</script>