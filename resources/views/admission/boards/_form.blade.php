<div class="row">

    <div class="col-md-6 mb-3">
        <label class="form-label">
            Name <span class="text-danger">*</span>
        </label>

        <input
            type="text"
            name="name"
            class="form-control @error('name') is-invalid @enderror"
            value="{{ old('name', $admissionBoard->name ?? '') }}"
            required
        >

        @error('name')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>


    <div class="col-md-3 mb-3">
        <label class="form-label">Code</label>

        <input
            type="text"
            name="code"
            class="form-control @error('code') is-invalid @enderror"
            value="{{ old('code', $admissionBoard->code ?? '') }}"
        >

        @error('code')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>


    <div class="col-md-3 mb-3">
        <label class="form-label">
            Type <span class="text-danger">*</span>
        </label>

        <select
            name="type"
            class="form-select @error('type') is-invalid @enderror"
            required
        >
            <option value="board"
                @selected(
                    old(
                        'type',
                        $admissionBoard->type ?? 'board'
                    ) === 'board'
                )
            >
                Board
            </option>

            <option value="university"
                @selected(
                    old(
                        'type',
                        $admissionBoard->type ?? ''
                    ) === 'university'
                )
            >
                University
            </option>
        </select>

        @error('type')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>


    <div class="col-md-3 mb-3">
        <label class="form-label">Sort Order</label>

        <input
            type="number"
            min="0"
            name="sort_order"
            class="form-control"
            value="{{ old(
                'sort_order',
                $admissionBoard->sort_order ?? 0
            ) }}"
        >
    </div>


    <div class="col-md-3 mb-3">
        <label class="form-label">
            Status <span class="text-danger">*</span>
        </label>

        <select
            name="status"
            class="form-select"
            required
        >
            <option
                value="1"
                @selected(
                    (string) old(
                        'status',
                        isset($admissionBoard)
                            ? (int) $admissionBoard->status
                            : 1
                    ) === '1'
                )
            >
                Active
            </option>

            <option
                value="0"
                @selected(
                    (string) old(
                        'status',
                        isset($admissionBoard)
                            ? (int) $admissionBoard->status
                            : 1
                    ) === '0'
                )
            >
                Inactive
            </option>
        </select>
    </div>


    <div class="col-md-12 mb-3">
        <label class="form-label">Description</label>

        <textarea
            name="description"
            rows="3"
            class="form-control"
        >{{ old(
            'description',
            $admissionBoard->description ?? ''
        ) }}</textarea>
    </div>

</div>