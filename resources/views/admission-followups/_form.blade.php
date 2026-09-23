<div class="row">

    <div class="col-md-4 mb-3">

        <label class="form-label">
            Follow-up Date
            <span class="text-danger">*</span>
        </label>

        <input
            type="date"
            name="followup_date"
            class="form-control @error('followup_date') is-invalid @enderror"
            value="{{ old(
                'followup_date',
                isset($admissionFollowup)
                    ? $admissionFollowup
                        ->followup_date
                        ->format('Y-m-d')
                    : now()->format('Y-m-d')
            ) }}"
            required
        >

        @error('followup_date')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror

    </div>


    <div class="col-md-4 mb-3">

        <label class="form-label">
            Follow-up Type
        </label>

        <select
            name="followup_type"
            class="form-select"
            required
        >

            <option value="">
                Select
            </option>

            @foreach([
                'phone' => 'Phone Call',
                'whatsapp' => 'WhatsApp',
                'email' => 'Email',
                'school_visit' => 'School Visit',
                'home_visit' => 'Home Visit',
                'other' => 'Other'
            ] as $value => $label)

                <option
                    value="{{ $value }}"
                    @selected(
                        old(
                            'followup_type',
                            $admissionFollowup->followup_type ?? ''
                        ) == $value
                    )
                >
                    {{ $label }}
                </option>

            @endforeach

        </select>

    </div>


    <div class="col-md-4 mb-3">

        <label class="form-label">
            Contact Person
        </label>

        <input
            type="text"
            name="contact_person"
            class="form-control"
            placeholder="Father / Mother / Guardian"
            value="{{ old(
                'contact_person',
                $admissionFollowup->contact_person ?? ''
            ) }}"
        >

    </div>


    <div class="col-md-4 mb-3">

        <label class="form-label">
            Response
            <span class="text-danger">*</span>
        </label>

        <select
            name="response_status"
            class="form-select"
            required
        >

            <option value="">
                Select Response
            </option>

            @foreach([
                'follow_up' => 'Follow Up Required',
                'interested' => 'Interested',
                'not_interested' => 'Not Interested',
                'applied' => 'Applied',
                'closed' => 'Closed'
            ] as $value => $label)

                <option
                    value="{{ $value }}"
                    @selected(
                        old(
                            'response_status',
                            $admissionFollowup->response_status ?? ''
                        ) == $value
                    )
                >
                    {{ $label }}
                </option>

            @endforeach

        </select>

    </div>


    <div class="col-md-4 mb-3">

        <label class="form-label">
            Next Follow-up
        </label>

        <input
            type="date"
            name="next_followup_date"
            class="form-control"
            value="{{ old(
                'next_followup_date',
                isset($admissionFollowup)
                && $admissionFollowup->next_followup_date
                    ? $admissionFollowup
                        ->next_followup_date
                        ->format('Y-m-d')
                    : ''
            ) }}"
        >

    </div>


    <div class="col-md-12 mb-3">

        <label class="form-label">
            Remarks
        </label>

        <textarea
            name="remarks"
            rows="4"
            class="form-control"
            placeholder="Discussion with parent..."
        >{{ old(
            'remarks',
            $admissionFollowup->remarks ?? ''
        ) }}</textarea>

    </div>

</div>