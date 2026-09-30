<div class="row g-3">

    {{-- Component Group --}}
    <div class="col-md-4">

        <label class="form-label">
            Component Group
            <span class="text-danger">*</span>
        </label>

        <select name="fee_component_group_id"
                class="form-select"
                required>

            <option value="">
                Select Component Group
            </option>

            @foreach($groups as $group)

                <option
                    value="{{ $group->id }}"
                    {{ old(
                        'fee_component_group_id',
                        $feeHead->fee_component_group_id ?? ''
                    ) == $group->id
                        ? 'selected'
                        : '' }}
                >
                    {{ $group->name }}
                </option>

            @endforeach

        </select>

    </div>


    {{-- Fee Cycle --}}
    <div class="col-md-4">

        <label class="form-label">
            Fee Cycle
            <span class="text-danger">*</span>
        </label>

        <select name="fee_cycle_id"
                class="form-select"
                required>

            <option value="">
                Select Fee Cycle
            </option>

            @foreach($cycles as $cycle)

                <option
                    value="{{ $cycle->id }}"
                    {{ old(
                        'fee_cycle_id',
                        $feeHead->fee_cycle_id ?? ''
                    ) == $cycle->id
                        ? 'selected'
                        : '' }}
                >
                    {{ $cycle->name }}
                    ({{ $cycle->installments_count }})
                </option>

            @endforeach

        </select>

    </div>


    {{-- Name --}}
    <div class="col-md-4">

        <label class="form-label">
            Component Name
            <span class="text-danger">*</span>
        </label>

        <input type="text"
               name="name"
               class="form-control"
               value="{{ old(
                    'name',
                    $feeHead->name ?? ''
               ) }}"
               placeholder="Example: Tuition Fee"
               required>

    </div>


    {{-- Code --}}
    <div class="col-md-4">

        <label class="form-label">
            Component Code
        </label>

        <input type="text"
               name="code"
               class="form-control"
               value="{{ old(
                    'code',
                    $feeHead->code ?? ''
               ) }}"
               placeholder="TUITION">

    </div>


    {{-- Sort --}}
    <div class="col-md-4">

        <label class="form-label">
            Sort Order
        </label>

        <input type="number"
               name="sort_order"
               min="0"
               class="form-control"
               value="{{ old(
                    'sort_order',
                    $feeHead->sort_order ?? 0
               ) }}">

    </div>


    {{-- Status --}}
    <div class="col-md-4">

        <label class="form-label d-block">
            Status
        </label>

        <div class="form-check form-switch mt-2">

            <input type="checkbox"
                   name="status"
                   value="1"
                   id="status"
                   class="form-check-input"

                   {{ old(
                       'status',
                       isset($feeHead)
                           ? $feeHead->status
                           : true
                   ) ? 'checked' : '' }}>

            <label class="form-check-label"
                   for="status">
                Active
            </label>

        </div>

    </div>


    {{-- Description --}}
    <div class="col-md-12">

        <label class="form-label">
            Description
        </label>

        <textarea name="description"
                  rows="3"
                  class="form-control"
                  placeholder="Optional description">{{ old(
                      'description',
                      $feeHead->description ?? ''
                  ) }}</textarea>

    </div>


    {{-- Behaviour --}}
    <div class="col-12">

        <div class="card bg-light border-0">

            <div class="card-body">

                <h6 class="mb-3">
                    Component Behaviour
                </h6>

                <div class="row g-3">


                    <div class="col-md-3">

                        <div class="form-check">

                            <input type="checkbox"
                                   name="is_optional"
                                   value="1"
                                   class="form-check-input"
                                   id="is_optional"

                                   {{ old(
                                       'is_optional',
                                       $feeHead->is_optional ?? false
                                   ) ? 'checked' : '' }}>

                            <label class="form-check-label"
                                   for="is_optional">

                                Optional Fee

                            </label>

                        </div>

                    </div>


                    <div class="col-md-3">

                        <div class="form-check">

                            <input type="checkbox"
                                   name="is_refundable"
                                   value="1"
                                   class="form-check-input"
                                   id="is_refundable"

                                   {{ old(
                                       'is_refundable',
                                       $feeHead->is_refundable ?? false
                                   ) ? 'checked' : '' }}>

                            <label class="form-check-label"
                                   for="is_refundable">

                                Refundable

                            </label>

                        </div>

                    </div>


                    <div class="col-md-3">

                        <div class="form-check">

                            <input type="checkbox"
                                   name="allow_concession"
                                   value="1"
                                   class="form-check-input"
                                   id="allow_concession"

                                   {{ old(
                                       'allow_concession',
                                       isset($feeHead)
                                           ? $feeHead->allow_concession
                                           : true
                                   ) ? 'checked' : '' }}>

                            <label class="form-check-label"
                                   for="allow_concession">

                                Allow Concession

                            </label>

                        </div>

                    </div>


                    <div class="col-md-3">

                        <div class="form-check">

                            <input type="checkbox"
                                   name="allow_waiver"
                                   value="1"
                                   class="form-check-input"
                                   id="allow_waiver"

                                   {{ old(
                                       'allow_waiver',
                                       isset($feeHead)
                                           ? $feeHead->allow_waiver
                                           : true
                                   ) ? 'checked' : '' }}>

                            <label class="form-check-label"
                                   for="allow_waiver">

                                Allow Waiver

                            </label>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>